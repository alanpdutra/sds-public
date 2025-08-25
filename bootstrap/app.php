<?php

use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Validation\ValidationException;
use Symfony\Component\HttpKernel\Exception\HttpExceptionInterface;
use Symfony\Component\HttpKernel\Exception\MethodNotAllowedHttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        api: __DIR__.'/../routes/api.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware) {
        //
    })
    ->withExceptions(function (Exceptions $exceptions) {
        $exceptions->render(function (ValidationException $e, $request) {
            if ($request->expectsJson() || $request->is('api/*')) {
                return response()->json([
                    'success' => false,
                    'error' => [
                        'type' => 'VALIDATION_ERROR',
                        'message' => 'Dados inválidos',
                        'fields' => $e->errors(),
                    ],
                ], 422);
            }
        });

        $exceptions->render(function (ModelNotFoundException $e, $request) {
            if ($request->expectsJson() || $request->is('api/*')) {
                return response()->json([
                    'success' => false,
                    'error' => [
                        'type' => 'NOT_FOUND',
                        'message' => 'Recurso não encontrado',
                    ],
                ], 404);
            }
        });

        $exceptions->render(function (MethodNotAllowedHttpException $e, $request) {
            if ($request->expectsJson() || $request->is('api/*')) {
                return response()->json([
                    'success' => false,
                    'error' => [
                        'type' => 'METHOD_NOT_ALLOWED',
                        'message' => 'Método não permitido',
                    ],
                ], 405);
            }
        });

        $exceptions->render(function (HttpExceptionInterface $e, $request) {
            if ($request->expectsJson() || $request->is('api/*')) {
                if ($e instanceof NotFoundHttpException) {
                    return response()->json([
                        'success' => false,
                        'error' => [
                            'type' => 'NOT_FOUND',
                            'message' => 'Recurso não encontrado',
                        ],
                    ], 404);
                }
                if ($e->getStatusCode() === 400) {
                    return response()->json([
                        'success' => false,
                        'error' => [
                            'type' => 'BAD_REQUEST',
                            'message' => 'Requisição inválida',
                        ],
                    ], 400);
                }
            }
        });

        // Fallback 500 kept default by Laravel
    })->create();
