<?php

namespace Modules\Dashboard\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Modules\Dashboard\Services\StatsService;
use Modules\Dashboard\Transformers\StatsResource;

class StatsController extends Controller
{
    public function __construct(
        protected StatsService $statsService
    ) {}

    /**
     * Display stats of users, roles, and permissions.
     */
    public function index(): JsonResponse
    {
        $stats = $this->statsService->getDashboardStats();

        return $this->resourceResponse(
            new StatsResource($stats),
            'Stats retrieved successfully.'
        );
    }
}
