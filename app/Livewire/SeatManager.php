<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\Seat;

class SeatManager extends Component
{
  public function render()
  {
    $seats = Seat::orderBy('display_order')->get();

    return view('livewire.seat-manager', [
      'seats' => $seats,
    ]);
  }
}
