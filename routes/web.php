<?php

use App\Livewire\ShopSetting;
use App\Livewire\UserManagement;
use App\Livewire\ReservationList;
use App\Livewire\ReservationCreate;
use App\Livewire\ReservationShow;
use App\Livewire\ReservationEdit;
use App\Livewire\ReservationPrint;
use App\Livewire\SeatManager;
use App\Livewire\Login;
use App\Livewire\ReservationTop;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

// サイトの入口
Route::get('/', function () {
    return redirect()->route(
        Auth::check() ? 'top' : 'login'
    );
});

// ログイン
Route::get('/login', Login::class)
    ->name('login');

// 開いたままの画面からログイン状態を確認
// 未ログインでもJSONを返せるよう、authグループの外に配置
Route::get('/auth/session-status', function () {
    return response()->json([
        'authenticated' => Auth::check(),
    ])->header('Cache-Control', 'no-store, private');
})
    ->name('login.session-status');

// 以下の業務用ページはログイン必須
Route::middleware('auth')->group(function () {
    // トップページ
    Route::get('/top', ReservationTop::class)
        ->name('top');

    // 予約一覧
    Route::get('/reservations', ReservationList::class)
        ->name('reservations.index');

    // 予約一覧印刷
    Route::get('/reservations/print', ReservationPrint::class)
        ->name('reservations.print');

    // 新規予約登録
    Route::get('/reservations/create', ReservationCreate::class)
        ->name('reservations.create');

    // 予約詳細
    Route::get('/reservations/{reservation}', ReservationShow::class)
        ->name('reservations.show');

    // 予約編集
    Route::get('/reservations/{reservation}/edit', ReservationEdit::class)
        ->name('reservations.edit');

    // 席マスター：既存の閲覧権限を維持
    Route::get('/seats', SeatManager::class)
        ->middleware('can:access-seats')
        ->name('seats.index');

    // 店舗設定：管理者・責任者のみ
    Route::get('/settings', ShopSetting::class)
        ->middleware('can:view-shop-settings')
        ->name('shop_setting');

    // ユーザー管理：管理者のみ
    Route::get('/users', UserManagement::class)
        ->middleware('can:view-user-management')
        ->name('users.index');

    // 店舗・スタッフ管理
    Route::view('/admin-dashboard', 'admin-dashboard')
        ->name('admin-dashboard');

    // ログアウト
    Route::post('/logout', function (Request $request) {
        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login');
    })
        ->name('logout');
});
