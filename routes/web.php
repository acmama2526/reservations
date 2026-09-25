<?php

use Illuminate\Support\Facades\Route;
use App\Livewire\ReservationList;
use App\Livewire\ReservationCreate;
use App\Livewire\ReservationShow;
use App\Livewire\ReservationEdit;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/reservations', ReservationList::class)
    ->name('reservations.index');

Route::get('/reservations/create', ReservationCreate::class)
    ->name('reservations.create');

Route::get('/reservations/{reservation}', ReservationShow::class)
    ->name('reservations.show');

Route::get('/reservations/{reservation}/edit', ReservationEdit::class)
    ->name('reservations.edit');