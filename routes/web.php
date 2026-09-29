<?php

use Illuminate\Support\Facades\Route;

use \App\Livewire\ShopSetting;
use App\Livewire\UserManagement;
use App\Livewire\ReservationList;
use App\Livewire\ReservationCreate;
use App\Livewire\ReservationShow;
use App\Livewire\ReservationEdit;
use App\Livewire\SeatManager;
use App\Livewire\Login;
use App\Http\Controllers\AdminDashboardController;


// Route::get('/', function () {
//     return view('welcome');
// });

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

// 席管理
Route::get('/seats', SeatManager::class)
    ->name('seats.index');

// 店舗設定
Route::get('/settings', ShopSetting::class)
    ->name('shop_setting');

// ユーザー管理
Route::get('/users', UserManagement::class)
    ->name('users.index');

Route::get('/login', Login::class)
    ->name('login');

Route::get('/admin-dashboard', [
    AdminDashboardController::class,'index'])
    ->name('admin.dashboard');
