<?php

namespace Tests\Feature;

use App\Models\Reservation;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use App\Livewire\ReservationList;
use Tests\TestCase;

class ReservationListTest extends TestCase
{
    /**
     * A basic feature test example.
     */
    use RefreshDatabase;

    public function test_予約一覧画面が表示される(): void
    {
        $response = $this->get('/reservations');

        $response->assertStatus(200);
    }

    public function test_お客様名で検索できる(): void
    {
        // 今日の予約を2件作る
        Reservation::factory()->create([
            'customer_name' => '山田太郎',
            'reservation_date' => now()->toDateString(),
        ]);

        Reservation::factory()->create([
            'customer_name' => '佐藤花子',
            'reservation_date' => now()->toDateString(),
        ]);

        // 「山田」で検索
        Livewire::test(ReservationList::class)
            ->set('customerName', '山田')
            ->call('search')
            ->assertSee('山田太郎')
            ->assertDontSee('佐藤花子');
    }

    public function test_人数で検索できる(): void
    {
        Reservation::factory()->create([
            'customer_name' => '山田太郎',
            'people' => 2,
            'reservation_date' => now()->toDateString(),
        ]);

        Reservation::factory()->create([
            'customer_name' => '佐藤花子',
            'people' =>4,
            'reservation_date' => now()->toDateString(),
        ]);

        Livewire::test(ReservationList::class)
        ->set('people','2')
        ->call('search')
        ->assertSee('山田太郎')
        ->assertDontSee('佐藤花子');
    }

    public function test_状態で検索できる():void{
    Reservation::factory()->create([
        'customer_name' => '山田太郎',
        'people' => 2,
        'status' => 'reserved',
        'reservation_date' => now()->toDateString(),
    ]);

    Reservation::factory()->create([
        'customer_name' => '佐藤花子',
        'people' => 4,
        'status' => 'cancelled',
        'reservation_date' => now()->toDateString(),
    ]);

    Livewire::test(ReservationList::class)
        ->set('status', 'reserved')
        ->call('search')
        ->assertSee('山田太郎')
        ->assertDontSee('佐藤花子');
    }
}
