<?php

namespace App\Providers;

use App\Models\User;
use Illuminate\Auth\Access\Response;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        //
    }

    public function boot(): void
    {
        Gate::define('access-seats', function (?User $user) {
            if (
                $user !== null
                && in_array($user->role, ['admin', 'manager'], true)
            ) {
                return Response::allow();
            }

            return Response::deny(
                '席マスタを閲覧する権限がありません。'
            );
        });
    }
}