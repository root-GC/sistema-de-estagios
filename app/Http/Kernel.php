<?php

namespace App\Http;

use Illuminate\Foundation\Http\Kernel as HttpKernel;
use Spatie\Permission\Middleware\RoleMiddleware;
use Spatie\Permission\Middleware\PermissionMiddleware;
use Spatie\Permission\Middleware\RoleOrPermissionMiddleware;

class Kernel extends HttpKernel
{
    /**
     * Global HTTP middleware
     */
    protected $middleware = [
        \Illuminate\Http\Middleware\HandleCors::class,
        \Illuminate\Foundation\Http\Middleware\ValidatePostSize::class,
        \Illuminate\Foundation\Http\Middleware\TrimStrings::class,
        \Illuminate\Foundation\Http\Middleware\ConvertEmptyStringsToNull::class,
    ];

    /**
     * Route middleware groups
     */
    protected $middlewareGroups = [
        'web' => [
           
            \Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse::class,
            \Illuminate\Session\Middleware\StartSession::class,
            \Illuminate\View\Middleware\ShareErrorsFromSession::class,
            \Illuminate\Routing\Middleware\SubstituteBindings::class,
        ],

       'api' => [
                \Laravel\Sanctum\Http\Middleware\EnsureFrontendRequestsAreStateful::class,
                'throttle:api',
                \Illuminate\Routing\Middleware\SubstituteBindings::class,
            ],
        ];

    /**
     * Route Middleware (AQUI é onde registamos o role)
     */
//    protected $middlewareAliases = [
//         'auth' => \Illuminate\Auth\Middleware\Authenticate::class,
//     'role' => RoleMiddleware::class,
//     'permission' => PermissionMiddleware::class,
//     'role_or_permission' => RoleOrPermissionMiddleware::class,
//     ];

     protected $routeMiddleware = [
     'auth' => \Illuminate\Auth\Middleware\Authenticate::class,
     'role' => RoleMiddleware::class,
    'permission' => PermissionMiddleware::class,
    'role_or_permission' => RoleOrPermissionMiddleware::class,
 ];
// protected $routeMiddleware = [
//     // ... other middleware
//     'role' => \Spatie\Permission\Middlewares\RoleMiddleware::class,
//     'permission' => \Spatie\Permission\Middlewares\PermissionMiddleware::class,
//     'role_or_permission' => \Spatie\Permission\Middlewares\RoleOrPermissionMiddleware::class,
// ];

}