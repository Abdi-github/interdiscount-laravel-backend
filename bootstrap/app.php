<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\HttpExceptionInterface;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
        then: function () {
            Route::middleware('api')
                ->prefix('api/v1')
                ->group(function () {
                    require base_path('routes/api/public.php');
                    require base_path('routes/api/customer.php');
                    require base_path('routes/api/store.php');
                    require base_path('routes/api/admin.php');
                });

            Route::middleware('api')
                ->prefix('api/v1/webhooks')
                ->group(base_path('routes/webhooks.php'));
        },
    )
    ->withMiddleware(function (Middleware $middleware): void {
        $middleware->web(append: [
            \App\Http\Middleware\HandleInertiaRequests::class,
            \Illuminate\Http\Middleware\AddLinkHeadersForPreloadedAssets::class,
        ]);

        $middleware->alias([
            'set.locale' => \App\Http\Middleware\SetLocale::class,
            'require.admin' => \App\Http\Middleware\RequireAdmin::class,
            'require.store.access' => \App\Http\Middleware\RequireStoreAccess::class,
            'verified.email' => \App\Http\Middleware\EnsureEmailIsVerified::class,
        ]);

        $middleware->api(prepend: [
            \App\Http\Middleware\SetLocale::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        $exceptions->render(function (NotFoundHttpException $e, Request $request) {
            if ($request->is('api/*')) {
                return response()->json([
                    'success' => false,
                    'message' => 'Resource not found',
                    'error' => ['code' => 'NOT_FOUND'],
                ], 404);
            }
        });

        $exceptions->respond(function (Response $response, \Throwable $e, Request $request) {
            if (! $request->is('api/*') && $e instanceof HttpExceptionInterface && in_array($response->getStatusCode(), [403, 404, 500, 503])) {
                return Inertia::render('Error', ['status' => $response->getStatusCode()])
                    ->toResponse($request)
                    ->setStatusCode($response->getStatusCode());
            }

            return $response;
        });
    })->create();
