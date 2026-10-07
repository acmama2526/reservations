<?php

namespace Tests\Feature;

use App\Livewire\SeatManager;
use Livewire\Livewire;
use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;

class SeatManagerTest extends TestCase
{
  use RefreshDatabase;

  public function test_席名が空欄ならバリデーションエラーになる(): void
  {
    Livewire::test(SeatManager::class)
      ->set('seat_name', null)
      ->call('save')
      ->assertHasErrors([
        'seat_name' => 'required',
      ]);
  }

  public function test_種類未選択ならバリデーションエラーになる(): void
  {
    Livewire::test(SeatManager::class)
      ->set('seat_name', 'T1')
      ->set('type', null)
      ->set('capacity', 4)
      ->set('display_order', 1)
      ->call('save')
      ->assertHasErrors([
        'type' => 'required',
      ]);
  }

  public function test_定員0ならバリデーションエラーになる(): void
  {
    Livewire::test(SeatManager::class)
      ->set('seat_name', 'T1')
      ->set('type', 'テーブル')
      ->set('capacity', 0)
      ->set('display_order', 1)
      ->call('save')
      ->assertHasErrors([
        'capacity' => 'min',
      ]);
  }

  public function test_表示順0ならバリデーションエラーになる(): void
  {
    Livewire::test(SeatManager::class)
      ->set('seat_name', 'T1')
      ->set('type', 'テーブル')
      ->set('capacity', 4)
      ->set('display_order', 0)
      ->call('save')
      ->assertHasErrors([
        'display_order' => 'min',
      ]);
  }

  public function test_表示順が重複したらバリデーションエラーになる(): void
  {
    \App\Models\Seat::create([
      'seat_name' => '既存席',
      'type' => 'テーブル',
      'capacity' => 4,
      'display_order' => 1,
      'is_active' => true,
    ]);

    Livewire::test(SeatManager::class)
      ->set('seat_name', '新しい席')
      ->set('type', 'テーブル')
      ->set('capacity', 2)
      ->set('display_order', 1)
      ->call('save')
      ->assertHasErrors([
        'display_order' => 'unique',
      ]);
  }
}
