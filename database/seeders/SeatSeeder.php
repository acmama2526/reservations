<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Seat;

class SeatSeeder extends Seeder
{
  /**
   * Run the database seeds.
   */
  public function run(): void
  {
    Seat::create([
      'seat_name' => 'T1',
      'type' => 'テーブル',
      'capacity' => 4,
      'display_order' => 1,
      'is_active' => true,
    ]);

    Seat::create([
      'seat_name' => 'T2',
      'type' => 'テーブル',
      'capacity' => 4,
      'display_order' => 2,
      'is_active' => true,
    ]);

    Seat::create([
      'seat_name' => 'Z1',
      'type' => '座敷',
      'capacity' => 6,
      'display_order' => 3,
      'is_active' => true,
    ]);

    Seat::create([
      'seat_name' => 'Z2',
      'type' => '座敷',
      'capacity' => 6,
      'display_order' => 4,
      'is_active' => true,
    ]);

    Seat::create([
      'seat_name' => 'C1',
      'type' => 'カウンター',
      'capacity' => 1,
      'display_order' => 5,
      'is_active' => true,
    ]);

    Seat::create([
      'seat_name' => 'C2',
      'type' => 'カウンター',
      'capacity' => 1,
      'display_order' => 6,
      'is_active' => true,
    ]);

    Seat::create([
      'seat_name' => 'C3',
      'type' => 'カウンター',
      'capacity' => 1,
      'display_order' => 7,
      'is_active' => true,
    ]);

    Seat::create([
      'seat_name' => 'C4',
      'type' => 'カウンター',
      'capacity' => 1,
      'display_order' => 8,
      'is_active' => true,
    ]);

    Seat::create([
      'seat_name' => 'C5',
      'type' => 'カウンター',
      'capacity' => 1,
      'display_order' => 9,
      'is_active' => true,
    ]);
  }
}
