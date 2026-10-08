<?php

use App\Livewire\ShopSetting;
use App\Livewire\UserManagement;
use App\Livewire\ReservationList;
use App\Livewire\ReservationCreate;
use App\Livewire\ReservationShow;
use App\Livewire\ReservationEdit;
use App\Livewire\SeatManager;
use App\Livewire\Login;
use App\Livewire\ReservationTop;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

// トップページ
Route::get('/top', ReservationTop::class)
    ->name('top');

// 予約一覧
Route::get('/reservations', ReservationList::class)
    ->name('reservations.index');

// 新規予約登録
Route::get('/reservations/create', ReservationCreate::class)
    ->name('reservations.create');

// 予約詳細
Route::get('/reservations/{reservation}', ReservationShow::class)
    ->name('reservations.show');

// 予約編集
Route::get('/reservations/{reservation}/edit', ReservationEdit::class)
    ->name('reservations.edit');

// 席マスター
Route::get('/seats', SeatManager::class)
    ->middleware('can:access-seats')
    ->name('seats.index');

// 店舗設定
Route::get('/settings', ShopSetting::class)
    ->name('shop_setting');

// ユーザー管理
Route::get('/users', UserManagement::class)
    ->name('users.index');

// ログイン
Route::get('/login', Login::class)
    ->name('login');

// 店舗・スタッフ管理
Route::view('/admin-dashboard', 'admin-dashboard')
    ->name('admin-dashboard');

// 開いたままの画面からログイン状態を確認
Route::get('/auth/session-status', function () {
    return response()->json([
        'authenticated' => Auth::check(),
    ])->header('Cache-Control', 'no-store, private');
})
    ->name('login.session-status');

// ログアウト
Route::post('/logout', function (Request $request) {
    Auth::logout();

    $request->session()->invalidate();
    $request->session()->regenerateToken();

    return redirect()->route('login');
})
    ->middleware('auth')
    ->name('logout');