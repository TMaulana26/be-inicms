<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        // web: __DIR__.'/../routes/web.php',
        api: __DIR__.'/../routes/api.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        $middleware->alias([
            'abilities' => \Laravel\Sanctum\Http\Middleware\CheckAbilities::class,
            'ability' => \Laravel\Sanctum\Http\Middleware\CheckForAnyAbility::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions) {
        // Handle 404 - Not Found (including Route Model Binding failures)
        $exceptions->render(function (\Symfony\Component\HttpKernel\Exception\NotFoundHttpException $e, \Illuminate\Http\Request $request) {
            if ($request->is('api/*')) {
                return response()->json([
                    'success' => false,
                    'message' => 'The requested resource was not found.',
                    'data'    => null,
                    'errors'  => [
                        'resource' => ['Resource does not exist or has been removed.']
                    ],
                ], 404);
            }
        });

        // Global API Response Wrapper for other exceptions
        $exceptions->respond(function (\Illuminate\Http\Response|\Illuminate\Http\JsonResponse $response) {
            if (request()->is('api/*')) {
                $original = $response instanceof \Illuminate\Http\JsonResponse
                    ? $response->getData(true)
                    : ['message' => $response->getContent() ?: $response->statusText()];

                // Allow specific error structure to pass through if already set (like our 404 above)
                if (isset($original['success'])) {
                    return $response;
                }

                $data = $original['data'] ?? null;
                $errors = $original['errors'] ?? null;

                // Handle generic client/server errors that haven't been structured yet
                if (!$response->isSuccessful() && !$errors) {
                    $errors = ['message' => [$original['message'] ?? $response->statusText()]];
                }

                return response()->json([
                    'success' => $response->isSuccessful(),
                    'message' => $original['message'] ?? ($response->statusText() ?: 'An error occurred'),
                    'data'    => $data,
                    'errors'  => $errors,
                ], $response->getStatusCode());
            }

            return $response;
        });
    })->create();
