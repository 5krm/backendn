<?php

namespace App\Traits;

use Illuminate\Http\JsonResponse;
use Illuminate\Pagination\LengthAwarePaginator;

/**
 * Provides consistent JSON response envelopes across all mobile API controllers.
 *
 * Every response follows the structure:
 * {
 *   "success": bool,
 *   "message": string,
 *   "data": mixed,       // present on success
 *   "errors": mixed,     // present on error
 *   "meta": object       // present on paginated responses
 * }
 */
trait ApiResponse
{
    /**
     * Return a successful JSON response.
     */
    protected function success(
        mixed $data = null,
        string $message = 'Success',
        int $code = 200,
    ): JsonResponse {
        return response()->json([
            'success' => true,
            'message' => $message,
            'data' => $data,
        ], $code);
    }

    /**
     * Return a 201 Created response.
     */
    protected function created(mixed $data = null, string $message = 'Created'): JsonResponse
    {
        return $this->success($data, $message, 201);
    }

    /**
     * Return a paginated JSON response with meta block.
     */
    protected function paginated(
        LengthAwarePaginator $paginator,
        callable $transform,
        string $message = 'Success',
    ): JsonResponse {
        return response()->json([
            'success' => true,
            'message' => $message,
            'data' => $transform($paginator->items()),
            'meta' => [
                'current_page' => $paginator->currentPage(),
                'last_page' => $paginator->lastPage(),
                'per_page' => $paginator->perPage(),
                'total' => $paginator->total(),
                'has_more' => $paginator->hasMorePages(),
            ],
        ]);
    }

    /**
     * Return an error response.
     */
    protected function error(
        string $message = 'An error occurred',
        mixed $errors = null,
        int $code = 400,
    ): JsonResponse {
        return response()->json([
            'success' => false,
            'message' => $message,
            'errors' => $errors,
        ], $code);
    }

    /**
     * Return a 404 Not Found response.
     */
    protected function notFound(string $message = 'Resource not found'): JsonResponse
    {
        return $this->error($message, null, 404);
    }

    /**
     * Return a 403 Forbidden response.
     */
    protected function forbidden(string $message = 'Forbidden'): JsonResponse
    {
        return $this->error($message, null, 403);
    }

    /**
     * Return a 401 Unauthorised response.
     */
    protected function unauthorised(string $message = 'Unauthenticated'): JsonResponse
    {
        return $this->error($message, null, 401);
    }

    /**
     * Return a 422 Validation error response.
     */
    protected function validationError(array $errors, string $message = 'Validation failed'): JsonResponse
    {
        return response()->json([
            'success' => false,
            'message' => $message,
            'errors' => $errors,
        ], 422);
    }
}
