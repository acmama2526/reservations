<?php

namespace App\Livewire;

use App\Models\Reservation;
use Livewire\Component;
use Livewire\Attributes\On;

class ReservationTop extends Component
{
    /*
    |--------------------------------------------------------------------------
    | 現在選択されている日付
    |--------------------------------------------------------------------------
    |
    | 上部の「予約状況」で選択している日付と、
    | 下部の「予約一覧」で使用する日付を同期させます。
    |
    */
    public string $selectedDate;


    /*
    |--------------------------------------------------------------------------
    | 初期表示
    |--------------------------------------------------------------------------
    |
    | 最初にTOPを開いたときは今日の日付を使用します。
    |
    */
    public function mount(): void
    {
        $this->selectedDate = today()->format('Y-m-d');
    }


    /*
    |--------------------------------------------------------------------------
    | 予約状況の日付変更を受け取る
    |--------------------------------------------------------------------------
    |
    | ReservationStatus側から
    |
    | reservation-date-changed
    |
    | というイベントが送られてきたときに実行されます。
    |
    */
    #[On('reservation-date-changed')]
    public function changeReservationDate(string $date): void
    {
        $this->selectedDate = $date;
    }


    /*
    |--------------------------------------------------------------------------
    | 画面表示
    |--------------------------------------------------------------------------
    */
    public function render()
    {
        /*
        |--------------------------------------------------------------------------
        | 選択されている日付の予約を取得
        |--------------------------------------------------------------------------
        |
        | 以前は today() 固定でしたが、
        | $selectedDate を使用することで
        | 上部の日付選択と連動します。
        |
        */

        $reservations = Reservation::with('seats')
            ->whereDate('reservation_date', $this->selectedDate)
            ->orderBy('start_time')
            ->get();


        return view('livewire.reservation-top', [
            'todayReservations' => $reservations,
            'selectedDate' => $this->selectedDate,
        ])
            ->layout('components.layout');
    }
}