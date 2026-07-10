<?php

namespace Modules\Contact\Services;

use App\Traits\HandlesIndexQuery;
use Illuminate\Support\Facades\DB;
use Modules\Contact\Models\ContactMessage;

class ContactService
{
    use HandlesIndexQuery;

    /**
     * Find a contact message by its ID.
     */
    public function findById(string $id, bool $withTrashed = false): ContactMessage
    {
        $query = ContactMessage::query();
        if ($withTrashed) {
            $query->withTrashed();
        }

        return $query->findOrFail($id);
    }

    /**
     * Display a listing of the contact messages.
     */
    public function index(array $params)
    {
        return $this->handleIndexQuery(
            ContactMessage::query(),
            $params,
            ['name', 'email', 'message'],
            function ($query) use ($params) {
                if (isset($params['is_read'])) {
                    $query->where('is_read', filter_var($params['is_read'], FILTER_VALIDATE_BOOLEAN));
                }
            },
            15
        );
    }

    /**
     * Create a new contact message.
     */
    public function store(array $data): ContactMessage
    {
        return ContactMessage::create($data)->refresh();
    }

    /**
     * Delete a contact message.
     */
    public function delete(ContactMessage $contactMessage): bool
    {
        return $contactMessage->delete();
    }

    /**
     * Toggle active status.
     */
    public function toggleStatus(ContactMessage $contactMessage): ContactMessage
    {
        return DB::transaction(function () use ($contactMessage) {
            $contactMessage->update(['is_active' => !$contactMessage->is_active]);

            return $contactMessage->refresh();
        });
    }

    /**
     * Toggle read status.
     */
    public function toggleReadStatus(ContactMessage $contactMessage): ContactMessage
    {
        return DB::transaction(function () use ($contactMessage) {
            $contactMessage->update(['is_read' => !$contactMessage->is_read]);

            return $contactMessage->refresh();
        });
    }

    /**
     * Restore a contact message.
     */
    public function restore(string $id): ContactMessage
    {
        return DB::transaction(function () use ($id) {
            $contactMessage = ContactMessage::onlyTrashed()->findOrFail($id);
            $contactMessage->restore();

            return $contactMessage->refresh();
        });
    }

    /**
     * Force delete a contact message.
     */
    public function forceDelete(string $id): ContactMessage
    {
        return DB::transaction(function () use ($id) {
            $contactMessage = ContactMessage::onlyTrashed()->findOrFail($id);
            $contactMessageData = clone $contactMessage;
            $contactMessage->forceDelete();

            return $contactMessageData;
        });
    }

    /**
     * Perform bulk operations.
     */
    public function handleBulkOperation(array $ids, string $operation): array
    {
        return DB::transaction(function () use ($ids, $operation) {
            $query = match ($operation) {
                'delete',
                'toggle',
                'toggle-read' => ContactMessage::query(),
                'restore',
                'forceDelete' => ContactMessage::onlyTrashed(),
                default => throw new \InvalidArgumentException("Invalid operation: {$operation}"),
            };

            $messages = $query->whereIn('id', $ids)->get();
            $foundIds = $messages->pluck('id')->toArray();
            $notFoundIds = array_values(array_diff($ids, $foundIds));

            if ($messages->isNotEmpty()) {
                switch ($operation) {
                    case 'delete':
                        ContactMessage::whereIn('id', $foundIds)->delete();
                        break;
                    case 'restore':
                        ContactMessage::onlyTrashed()->whereIn('id', $foundIds)->restore();
                        break;
                    case 'forceDelete':
                        ContactMessage::onlyTrashed()->whereIn('id', $foundIds)->forceDelete();
                        break;
                    case 'toggle':
                        foreach ($messages as $message) {
                            $message->update(['is_active' => !$message->is_active]);
                        }
                        break;
                    case 'toggle-read':
                        foreach ($messages as $message) {
                            $message->update(['is_read' => !$message->is_read]);
                        }
                        break;
                }

                if ($operation !== 'forceDelete') {
                    $messages = ContactMessage::withTrashed()->whereIn('id', $foundIds)->get();
                }
            }

            return [
                'affected' => $messages,
                'failed_ids' => $notFoundIds,
            ];
        });
    }
}
