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
        // 席マスタ：管理者・責任者のみ閲覧可能
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

        // 店舗設定：管理者・責任者のみ閲覧可能
        Gate::define('view-shop-settings', function (User $user): bool {
            return in_array(
                $user->role,
                ['admin', 'manager'],
                true
            );
        });

        // ユーザー管理：管理者のみ閲覧可能
        Gate::define('view-user-management', function (User $user): bool {
            return $user->role === 'admin';
        });
    }
}