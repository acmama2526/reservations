<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\Seat;

class SeatManager extends Component
{
  public function render()
  {
    //seats DBを読みdisplay_order順に並べ,$seatsに入れる
    $seats = Seat::orderBy('display_order')->get();

    return view('livewire.seat-manager', [
      'seats' => $seats,
    ]);
  }
}
