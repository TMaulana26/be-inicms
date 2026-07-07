<?php

namespace App\Traits;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Http\Resources\Json\JsonResource;

trait ApiResponse
{
    /**
     * Send a success response.
     *
     * @param  mixed  $data
     */
    protected function successResponse($data = null, string $message = 'Success', int $code = 200): JsonResponse
    {
        return response()->json([
            'success' => true,
            'message' => $message,
            'data' => $data,
        ], $code);
    }

    /**
     * Send an error response.
     *
     * @param  mixed  $errors
     */
    protected function errorResponse(string $message, int $code = 400, $errors = null): JsonResponse
    {
        return response()->json([
            'success' => false,
            'message' => $message,
            'errors' => $errors,
        ], $code);
    }

    /**
     * Standardize Single Resource responses.
     */
    protected function resourceResponse(JsonResource $resource, string $message = 'Success', int $code = 200): JsonResponse
    {
        return response()->json([
            'success' => true,
            'message' => $message,
            'data' => $resource,
        ], $code);
    }

    /**
     * Standardize Paginated Resource responses.
     */
    protected function paginatedResponse(AnonymousResourceCollection $resource, string $message = 'Success', int $code = 200): JsonResponse
    {
        $paginated = $resource->response()->getData(true);

        return response()->json([
            'success' => true,
            'message' => $message,
            'data' => $paginated['data'],
            'links' => $paginated['links'] ?? null,
            'meta' => $paginated['meta'] ?? null,
        ], $code);
    }
}
