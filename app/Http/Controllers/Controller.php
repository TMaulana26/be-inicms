<?php

namespace App\Http\Controllers;

use App\Traits\ApiResponse;

abstract class Controller
{
    use ApiResponse;

    /**
     * Formatting helper for bulk operation responses.
     *
     * @param array $result
     * @param string $action
     * @param string $resourceClass
     * @param string $modelName
     * @param array|null $eagerLoad
     * @return \Illuminate\Http\JsonResponse
     */
    protected function bulkResponse(array $result, string $action, string $resourceClass, string $modelName, ?array $eagerLoad = null): \Illuminate\Http\JsonResponse
    {
        $count = $result['affected']->count();
        $failedCount = count($result['failed_ids']);

        if ($count === 0 && $failedCount > 0) {
            return $this->errorResponse("No " . str($modelName)->plural() . " found to be {$action}.", 404, [
                'not_found_ids' => $result['failed_ids']
            ]);
        }

        $message = sprintf(
            '%d %s %s successfully. %d failed.',
            $count,
            str($modelName)->plural($count),
            $action,
            $failedCount
        );

        $affected = $result['affected'];
        if ($eagerLoad) {
            $affected->load($eagerLoad);
        }

        return $this->successResponse([
            'affected' => $resourceClass::collection($affected),
            'failed_ids' => $result['failed_ids']
        ], $message);
    }
}
