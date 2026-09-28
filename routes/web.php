<?php

use Illuminate\Support\Facades\Route;
// use App\Livewire\ReservationList;
// use App\Livewire\ReservationCreate;
// use App\Livewire\ReservationShow;
// use App\Livewire\ReservationEdit;
// use App\Http\Controllers\ReservationController;
// use App\Livewire\SeatManager;
// use Illuminate\Support\Facades\Route;
use \App\Livewire\ShopSetting;
use App\Livewire\UserManagement;
use App\Livewire\ReservationList;
use App\Livewire\ReservationCreate;
use App\Livewire\ReservationShow;
use App\Livewire\ReservationEdit;
use App\Livewire\SeatManager;

// Route::get('/', function () {
//     return view('welcome');
// });

// Route::get('/reservations', ReservationList::class)
//     ->name('reservations.index');
// Route::get('/reservations/create', ReservationCreate::class)
//     ->name('reservations.create');

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

// トップ
Route::get('/top', function () {
  return view('top');
});

// 席管理
Route::get('/seats', SeatManager::class)
  ->name('seats.index');
// Route::get('/reservations/{reservation}', ReservationShow::class)
//     ->name('reservations.show');

// Route::get('/reservations/{reservation}/edit', ReservationEdit::class)
//     ->name('reservations.edit');

// Route::get('/reservations', [ReservationController::class, 'index'])->name('reservations.index');

// Route::get('/top', function () {
//     return view('top');
// });

// Route::get('/seats', SeatManager::class)
//     ->name('seats.index');

Route::get('/settings', ShopSetting::class)
    ->name('shop_setting');

Route::get('/users', UserManagement::class)
->name('users.index');