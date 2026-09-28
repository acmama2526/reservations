<?php

namespace App\Livewire;

use App\Models\Reservation;
use Livewire\Component;

class ReservationShow extends Component
{
    public Reservation $reservation;

    public function mount(Reservation $reservation): void
    {
        // 予約に紐づいている席も一緒に取得
        $this->reservation = $reservation->load('seats');
    }

    public function render()
    {
        return view('livewire.reservation-show');
    }
}
