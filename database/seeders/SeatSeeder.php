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
      'seat_name' => 'テーブル１',
      'type' => 'テーブル',
      'capacity' => 4,
      'display_order' => 1,
      'is_active' => true,
    ]);

    Seat::create([
      'seat_name' => 'テーブル２',
      'type' => 'テーブル',
      'capacity' => 4,
      'display_order' => 2,
      'is_active' => true,
    ]);

    Seat::create([
      'seat_name' => '座敷１',
      'type' => '座敷',
      'capacity' => 6,
      'display_order' => 3,
      'is_active' => true,
    ]);

    Seat::create([
      'seat_name' => '座敷２',
      'type' => '座敷',
      'capacity' => 6,
      'display_order' => 4,
      'is_active' => true,
    ]);

    Seat::create([
      'seat_name' => 'カウンター１',
      'type' => 'カウンター',
      'capacity' => 1,
      'display_order' => 5,
      'is_active' => true,
    ]);

    Seat::create([
      'seat_name' => 'カウンター２',
      'type' => 'カウンター',
      'capacity' => 1,
      'display_order' => 6,
      'is_active' => true,
    ]);

    Seat::create([
      'seat_name' => 'カウンター３',
      'type' => 'カウンター',
      'capacity' => 1,
      'display_order' => 7,
      'is_active' => true,
    ]);

    Seat::create([
      'seat_name' => 'カウンター４',
      'type' => 'カウンター',
      'capacity' => 1,
      'display_order' => 8,
      'is_active' => true,
    ]);

    Seat::create([
      'seat_name' => 'カウンター５',
      'type' => 'カウンター',
      'capacity' => 1,
      'display_order' => 9,
      'is_active' => true,
    ]);
  }
}
