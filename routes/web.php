<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ReservationController;

Route::get('/', function () {
    return view('welcome');
});


Route::get('/reservations', [ReservationController::class, 'index'])->name('reservations.index');
