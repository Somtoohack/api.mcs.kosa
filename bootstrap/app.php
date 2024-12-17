<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        api: __DIR__ . '/../routes/web.php',
        commands: __DIR__ . '/../routes/console.php',
        health: '/up',
        apiPrefix: '',
    )
    ->withMiddleware(function (Middleware $middleware) {

        // $middleware->append(CheckJsonHeaders::class);

    })
    ->withExceptions(function (Exceptions $exceptions) {

        $exceptions->renderable(function (NotFoundHttpException $exception, Request $request) {

            return response()->json([
                'success' => false,
                'code' => 4040,
                'message' => 'Route not found',
                'error' => 'The requested page could doesn\'t exist',
            ], 200);
        });

        $exceptions->renderable(function (ValidationException $exception, Request $request) {

            $errors = $exception->errors();
            $firstErrorMessage = collect($errors)->flatten()->first(); // Get the first error message

            return response()->json([
                'success' => false,
                'code' => 4220,
                'message' => $firstErrorMessage,
                'errors' => $errors,
            ], 200);
        });
    })->create();