<?php

namespace App\Livewire;

use App\Models\Reservation;
use Livewire\Component;

class ReservationTop extends Component
{
    /*
    |--------------------------------------------------------------------------
    | 予約管理TOPページ
    |--------------------------------------------------------------------------
    |
    | TOPページで必要になるデータを取得し、
    | reservation-top.blade.php に渡します。
    |
    */

    public function render()
    {
        /*
        |--------------------------------------------------------------------------
        | 本日の予約を取得
        |--------------------------------------------------------------------------
        |
        | with('seats')
        | → 予約に紐付いている席情報も一緒に取得します。
        |
        | whereDate(...)
        | → reservation_date が今日の予約だけ取得します。
        |
        | orderBy(...)
        | → 開始時間が早い順に並べます。
        |
        */

        $todayReservations = Reservation::with('seats')
            ->whereDate('reservation_date', today())
            ->orderBy('start_time')
            ->get();


        /*
        |--------------------------------------------------------------------------
        | Bladeへデータを渡す
        |--------------------------------------------------------------------------
        |
        | ここで渡した 'todayReservations' が、
        | reservation-top.blade.php で
        |
        | $todayReservations
        |
        | として使用できます。
        |
        */

        return view('livewire.reservation-top', [
            'todayReservations' => $todayReservations,
        ]);
    }
}
