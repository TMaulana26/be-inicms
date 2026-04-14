<?php

namespace Modules\Setting\Services;

use Modules\Setting\Models\Setting;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Collection;

use App\Traits\HandlesIndexQuery;

class SettingService
{
    use HandlesIndexQuery;

    /**
     * Find a setting by its ID.
     */
    public function findById(string $id): Setting
    {
        return Setting::findOrFail($id);
    }

    /**
     * Display a listing of the settings.
     */
    public function index(array $params)
    {
        return $this->handleIndexQuery(
            Setting::query(),
            $params,
            ['key', 'name'],
            fn($q) => $q->when($params['group'] ?? null, fn($subQ, $group) => $subQ->where('group', $group)),
            15
        );
    }

    /**
     * Get all settings grouped by their group.
     */
    public function getAllGrouped(): Collection
    {
        return Setting::all()->groupBy('group');
    }

    /**
     * Update multiple settings at once.
     */
    public function updateBulk(array $settings): Collection
    {
        return DB::transaction(function () use ($settings) {
            $updatedSettings = collect();

            foreach ($settings as $settingData) {
                /** @var Setting $setting */
                $setting = Setting::where('key', $settingData['key'])->first();

                if ($setting) {
                    // Handle image type separately via media library
                    if ($setting->type === 'image' && isset($settingData['value']) && $settingData['value'] instanceof \Illuminate\Http\UploadedFile) {
                        $setting->addMedia($settingData['value'])
                            ->toMediaCollection('setting_image');

                        $setting->update(['value' => $setting->getFirstMediaUrl('setting_image')]);
                    } else {
                        $setting->update(['value' => $settingData['value'] ?? $setting->value]);
                    }

                    $updatedSettings->push($setting->refresh());
                }
            }

            return $updatedSettings;
        });
    }

    /**
     * Delete a setting.
     */
    public function delete(Setting $setting): bool
    {
        return $setting->delete();
    }

    /**
     * Get a setting by its key.
     */
    public function getByKey(string $key)
    {
        return Setting::where('key', $key)->first();
    }

    /**
     * Toggle a single setting's activity status.
     */
    public function toggleStatus(Setting $setting): Setting
    {
        return DB::transaction(function () use ($setting) {
            $setting->update(['is_active' => !$setting->is_active]);
            return $setting->refresh();
        });
    }

    /**
     * Restore a single setting.
     */
    public function restore(string $id): Setting
    {
        return DB::transaction(function () use ($id) {
            $setting = Setting::onlyTrashed()->findOrFail($id);
            $setting->restore();
            return $setting->refresh();
        });
    }

    /**
     * Force delete a single setting.
     */
    public function forceDelete(string $id): Setting
    {
        return DB::transaction(function () use ($id) {
            $setting = Setting::onlyTrashed()->findOrFail($id);
            $settingData = clone $setting;
            $setting->forceDelete();
            return $settingData;
        });
    }

    /**
     * Perform bulk operations on settings.
     */
    public function handleBulkOperation(array $ids, string $operation): array
    {
        return DB::transaction(function () use ($ids, $operation) {
            $query = match ($operation) {
                'delete',
                'toggle' => Setting::query(),
                'restore',
                'forceDelete' => Setting::onlyTrashed(),
                default => throw new \InvalidArgumentException("Invalid operation: {$operation}"),
            };

            $settings = $query->whereIn('id', $ids)->get();
            $foundIds = $settings->pluck('id')->toArray();
            $notFoundIds = array_values(array_diff($ids, $foundIds));

            if ($settings->isNotEmpty()) {
                match ($operation) {
                    'delete' => Setting::whereIn('id', $foundIds)->delete(),
                    'restore' => Setting::onlyTrashed()->whereIn('id', $foundIds)->restore(),
                    'forceDelete' => Setting::onlyTrashed()->whereIn('id', $foundIds)->forceDelete(),
                    'toggle' => $settings->each(fn($u) => $u->update(['is_active' => !$u->is_active])),
                };

                if ($operation !== 'forceDelete') {
                    $settings->each->refresh();
                }
            }

            return [
                'affected' => $settings,
                'failed_ids' => $notFoundIds,
            ];
        });
    }
}
