<?php

use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Http\Request;
use Symfony\Component\HttpKernel\Exception\HttpException;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        //
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        $exceptions->render(function (
            HttpException $exception,
            Request $request
        ) {
            if (
                $exception->getStatusCode() === 403
                && $exception->getPrevious() instanceof AuthorizationException
                && $exception->getMessage()
                    === '席マスタを閲覧する権限がありません。'
            ) {
                return redirect()
                    ->route('admin-dashboard')
                    ->with(
                        'permission-message',
                        '席マスタを閲覧する権限がありません。'
                    );
            }

            return null;
        });
    })
    ->create();