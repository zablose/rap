<?php

use App\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Zablose\Rap\Middleware\VerifyPermission;
use Zablose\Rap\Middleware\VerifyRole;

$app = Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: dirname(__DIR__).'/routes/web.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        $middleware->alias([
            'role' => VerifyRole::class,
            'permission' => VerifyPermission::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions) {
        //
    })->create();

$app->useEnvironmentPath(dirname(__DIR__, 2))->setNamespace('App\\');

return $app;
