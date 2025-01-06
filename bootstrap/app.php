<?php

use App\Http\Middleware\CheckAdminRole;
use App\Http\Middleware\CheckCompanyRegistration;
use App\Http\Middleware\CheckProductKey;
use App\Http\Middleware\CheckUsers;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware) {
        $middleware->alias([
            'checkCompany' => CheckCompanyRegistration::class,
            'admin' => CheckAdminRole::class,
            'checkProductKey' => CheckProductKey::class,
            'checkUser' => CheckUsers::class
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions) {
        //
    })->create();
