<?php

use App\Http\Middleware\CheckRole;
use App\Http\Middleware\HandleAppearance;
use App\Http\Middleware\HandleInertiaRequests;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Http\Middleware\AddLinkHeadersForPreloadedAssets;
use Illuminate\Http\Request;
return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )

    ->withMiddleware(function (Middleware $middleware) {


          $middleware->trustProxies(
        at: '*',
        headers: Request::HEADER_X_FORWARDED_FOR
            | Request::HEADER_X_FORWARDED_HOST
            | Request::HEADER_X_FORWARDED_PORT
            | Request::HEADER_X_FORWARDED_PROTO
          );

        // 1. DAFTARKAN ALIAS (Untuk Route 'role:security')
        $middleware->alias([
            'role' => CheckRole::class,
            'resident' => \App\Http\Middleware\CheckResidentSession::class,
        ]);

        // 2. TAMBAHKAN MIDDLEWARE KE GROUP 'WEB'
        // (Digabung jadi satu array biar rapi dan tidak double load)
        $middleware->web(append: [
            HandleAppearance::class,                 // Mengurus Dark Mode/Theme
            HandleInertiaRequests::class,            // Mengurus Data Inertia (User, Flash msg)
            AddLinkHeadersForPreloadedAssets::class, // Optimasi Aset
        ]);

        // 3. PENGECUALIAN ENKRIPSI COOKIE
        // Biar settingan tema/sidebar tidak ter-reset saat browser ditutup
        $middleware->encryptCookies(except: [
            'appearance',
            'sidebar_state',
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions) {
        //
    })->create();
