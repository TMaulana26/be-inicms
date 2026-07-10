<?php

namespace Modules\Contact\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Http\Requests\Shared\BulkRequest;
use App\Traits\HandlesBulkAndSoftDeletes;
use Illuminate\Http\JsonResponse;
use Modules\Contact\Http\Requests\IndexContactRequest;
use Modules\Contact\Http\Requests\StoreContactRequest;
use Modules\Contact\Services\ContactService;
use Modules\Contact\Transformers\ContactMessageResource;

/**
 * @tags Contact
 */
class ContactMessageController extends Controller
{
    use HandlesBulkAndSoftDeletes;

    public function __construct(
        protected ContactService $service
    ) {}

    protected function getService()
    {
        return $this->service;
    }

    protected function getResourceClass(): string
    {
        return ContactMessageResource::class;
    }

    protected function getModelName(): string
    {
        return 'contactMessage';
    }

    /**
     * Display a listing of the contact messages.
     */
    public function index(IndexContactRequest $request): JsonResponse
    {
        $messages = $this->service->index($request->validated());

        return $this->paginatedResponse(
            ContactMessageResource::collection($messages),
            'Contact messages retrieved successfully.'
        );
    }

    /**
     * Store a newly created contact message in storage.
     */
    public function store(StoreContactRequest $request): JsonResponse
    {
        $message = $this->service->store($request->validated());

        return $this->resourceResponse(
            new ContactMessageResource($message),
            'Contact message sent successfully.',
            201
        );
    }

    /**
     * Display the specified contact message.
     */
    public function show(string $id): JsonResponse
    {
        $message = $this->service->findById($id, true);

        return $this->resourceResponse(
            new ContactMessageResource($message),
            'Contact message retrieved successfully.'
        );
    }

    /**
     * Remove the specified contact message from storage.
     */
    public function destroy(string $id): JsonResponse
    {
        $message = $this->service->findById($id);
        $this->service->delete($message);

        return $this->resourceResponse(
            new ContactMessageResource($message),
            'Contact message deleted successfully.'
        );
    }

    /**
     * Toggle active status for the specified contact message.
     */
    public function toggleStatus(string $id): JsonResponse
    {
        $message = $this->service->findById($id);
        $updatedMessage = $this->service->toggleStatus($message);

        return $this->resourceResponse(
            new ContactMessageResource($updatedMessage),
            'Contact message status updated successfully.'
        );
    }

    /**
     * Toggle read status for the specified contact message.
     */
    public function toggleRead(string $id): JsonResponse
    {
        $message = $this->service->findById($id);
        $updatedMessage = $this->service->toggleReadStatus($message);

        return $this->resourceResponse(
            new ContactMessageResource($updatedMessage),
            'Contact message read status updated successfully.'
        );
    }

    /**
     * Toggle read status for multiple contact messages in bulk.
     */
    public function bulkToggleRead(BulkRequest $request): JsonResponse
    {
        $result = $this->service->handleBulkOperation($request->validated()['ids'], 'toggle-read');

        return $this->bulkResponse(
            $result,
            'read status toggled',
            $this->getResourceClass(),
            $this->getModelName()
        );
    }
}
