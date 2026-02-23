<?php

namespace App\Traits;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Pagination\AbstractPaginator;

trait ApiResponse
{
    /**
     * Send a success response.
     *
     * @param mixed $data
     * @param string $message
     * @param int $code
     * @return JsonResponse
     */
    protected function successResponse($data = null, string $message = 'Success', int $code = 200): JsonResponse
    {
        return response()->json([
            'success' => true,
            'message' => $message,
            'data'    => $data,
        ], $code);
    }

    /**
     * Send an error response.
     *
     * @param string $message
     * @param int $code
     * @param mixed $errors
     * @return JsonResponse
     */
    protected function errorResponse(string $message, int $code = 400, $errors = null): JsonResponse
    {
        return response()->json([
            'success' => false,
            'message' => $message,
            'errors'  => $errors,
        ], $code);
    }

    /**
     * Standardize Single Resource responses.
     *
     * @param JsonResource $resource
     * @param string $message
     * @param int $code
     * @return JsonResponse
     */
    protected function resourceResponse(JsonResource $resource, string $message = 'Success', int $code = 200): JsonResponse
    {
        return response()->json([
            'success' => true,
            'message' => $message,
            'data'    => $resource,
        ], $code);
    }

    /**
     * Standardize Paginated Resource responses.
     *
     * @param AnonymousResourceCollection $resource
     * @param string $message
     * @param int $code
     * @return JsonResponse
     */
    protected function paginatedResponse(AnonymousResourceCollection $resource, string $message = 'Success', int $code = 200): JsonResponse
    {
        $paginated = $resource->response()->getData(true);

        return response()->json([
            'success' => true,
            'message' => $message,
            'data'    => $paginated['data'],
            'links'   => $paginated['links'] ?? null,
            'meta'    => $paginated['meta'] ?? null,
        ], $code);
    }
}
