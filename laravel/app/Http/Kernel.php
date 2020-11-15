<?php

/**
 * @noinspection PhpMissingFieldTypeInspection
 * @noinspection PhpFullyQualifiedNameUsageInspection
 */

namespace App\Http;

use Illuminate\Foundation\Http\Kernel as HttpKernel;

class Kernel extends HttpKernel
{
    /**
     * The application's global HTTP middleware stack.
     *
     * These middleware are run during every request to your application.
     *
     * @var array
     */
    protected $middleware = [];

    /**
     * The application's route middleware groups.
     *
     * @var array
     */
    protected $middlewareGroups = [
        'web' => [],
    ];

    /**
     * The application's route middleware.
     *
     * These middleware may be assigned to groups or used individually.
     *
     * @var array
     */
    protected $routeMiddleware = [
        'role' => \Zablose\Rap\Middleware\VerifyRole::class,
        'permission' => \Zablose\Rap\Middleware\VerifyPermission::class,
    ];
}
