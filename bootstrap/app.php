<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Http\Request;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        $middleware->web(append: [
            \App\Http\Middleware\IdentifyTenant::class,
        ]);
        $middleware->alias([
            'superadmin'       => \App\Http\Middleware\CheckSuperAdmin::class,
            'tenant.admin'     => \App\Http\Middleware\CheckTenantAdmin::class,
            'require.tenant'   => \App\Http\Middleware\RequireTenant::class,
            'require.central'  => \App\Http\Middleware\RequireCentralDomain::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        $exceptions->shouldRenderJsonWhen(
            fn (Request $request) => $request->is('api/*'),
        );
    })->create();
