<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ReservationController;
use App\Livewire\SeatManager;

Route::get('/', function () {
  return view('welcome');
});


Route::get(
  '/reservations',
  [ReservationController::class, 'index']
)->name('reservations.index');

Route::get(
  '/seats',
  SeatManager::class
)->name('seats.index');
