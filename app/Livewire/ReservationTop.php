<?php

namespace App\Livewire;

use App\Models\Reservation;
use Livewire\Component;

class ReservationTop extends Component
{
    /*
    |--------------------------------------------------------------------------
    | 予約管理TOP
    |--------------------------------------------------------------------------
    |
    | TOPページ全体を管理するLivewireコンポーネントです。
    |
    | この画面では、
    |
    | ・予約状況
    | ・本日の予約一覧
    | ・新規予約
    |
    | を表示します。
    |
    */

    public function render()
    {
        /*
        |--------------------------------------------------------------------------
        | 本日の予約を取得
        |--------------------------------------------------------------------------
        |
        | TOP左下に表示する本日の予約です。
        |
        | with('seats') によって
        | 予約に紐付いている席も同時に取得します。
        |
        */

        $todayReservations = Reservation::with('seats')
            ->whereDate('reservation_date', today())
            ->orderBy('start_time')
            ->get();


        /*
        |--------------------------------------------------------------------------
        | TOP画面を表示
        |--------------------------------------------------------------------------
        |
        | layout('components.layout') を指定することで、
        |
        | header
        | ↓
        | reservation-top
        | ↓
        | footer
        |
        | の構成になります。
        |
        */

        return view('livewire.reservation-top', [
            'todayReservations' => $todayReservations,
        ])
            ->layout('components.layout');
    }
}