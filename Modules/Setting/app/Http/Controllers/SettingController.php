<?php

namespace Modules\Setting\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Http\Resources\SettingResource;
use Modules\Setting\Http\Requests\Setting\IndexSettingRequest;
use Modules\Setting\Http\Requests\Setting\UpdateBulkSettingRequest;
use App\Traits\HandlesBulkAndSoftDeletes;
use Modules\Setting\Services\SettingService;
use Modules\Setting\Models\Setting;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class SettingController extends Controller
{
    use HandlesBulkAndSoftDeletes;

    public function __construct(
        protected SettingService $settingService
    ) {}

    protected function getService() { return $this->settingService; }
    protected function getResourceClass(): string { return SettingResource::class; }
    protected function getModelName(): string { return 'setting'; }

    /**
     * Display a listing of the settings.
     */
    public function index(IndexSettingRequest $request): JsonResponse
    {
        $settings = $this->settingService->index($request->validated());

        return $this->paginatedResponse(
            SettingResource::collection($settings),
            'Settings retrieved successfully.'
        );
    }

    /**
     * Display settings grouped by group.
     */
    public function grouped(): JsonResponse
    {
        $settings = $this->settingService->getAllGrouped();

        $formattedSettings = $settings->map(function ($group) {
            return SettingResource::collection($group);
        });

        return $this->successResponse(
            $formattedSettings,
            'Grouped settings retrieved successfully.'
        );
    }

    /**
     * Display the specified setting.
     */
    public function show(Setting $setting): JsonResponse
    {
        return $this->resourceResponse(
            new SettingResource($setting),
            'Setting retrieved successfully.'
        );
    }

    /**
     * Update multiple settings in bulk.
     */
    public function updateBulk(UpdateBulkSettingRequest $request): JsonResponse
    {
        $updatedSettings = $this->settingService->updateBulk($request->validated()['settings']);

        return $this->successResponse(
            SettingResource::collection($updatedSettings),
            'Settings updated successfully.'
        );
    }

    /**
     * Remove the specified setting.
     */
    public function destroy(Setting $setting): JsonResponse
    {
        $this->settingService->delete($setting);

        return $this->resourceResponse(
            new SettingResource($setting),
            'Setting deleted successfully.'
        );
    }

    /**
     * Toggle status for a single setting.
     */
    public function toggleStatus(Setting $setting): JsonResponse
    {
        $setting = $this->settingService->toggleStatus($setting);

        return $this->resourceResponse(
            new SettingResource($setting),
            'Setting status toggled successfully.'
        );
    }
}
