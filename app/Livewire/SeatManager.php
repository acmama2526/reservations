<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\Seat;
use App\Models\Reservation;

class SeatManager extends Component
{
  public ?string $seat_name = null;
  public ?string $type = null;
  public ?int $capacity = null;
  public ?int $display_order = null;
  public bool $is_active = true;
  public ?int $editingSeatId = null;  //編集中の席id または null:新規作成

  //席自動配置用
  public ?int $people = null;               //人数
  public ?string $reservation_date = null;  //予約日
  public ?string $start_time = null;        //開始時刻
  public ?string $end_time = null;          //終了時刻
  public array $availableSeatIds = [];      //空席候補を入れる配列

  /**
   * 利用可能な席を検索
   * @return void
   */
  public function searchAvailableSeats()
  {
    $reservedSeatIds = Reservation::where('reservation_date', $this->reservation_date)
      ->where('start_time', '<', $this->end_time)
      ->where('end_time', '>', $this->start_time)
      ->with('seats')
      ->get()
      ->flatMap(function ($reservation) {
        return $reservation->seats->pluck('id');
      })
      ->unique()
      ->values()
      ->all();

    $this->availableSeatIds = Seat::where('is_active', true)
      ->whereNotIn('id', $reservedSeatIds)
      ->orderBy('display_order')
      ->pluck('id')
      ->all();
  }

  /**
   * 利用可否の切り替えトグル
   * @param int $id  席ID
   * @return void
   */
  // 停止ボタンを消したので要らない
  // public function toggleActive(int $id)
  // {
  //   $seat = Seat::findOrFail($id);

  //   $seat->update([
  //     'is_active' => ! $seat->is_active,
  //   ]);
  // }

  /**
   * 追加ボタンを押したときにDBへ保存する処理
   * @return void
   */
  public function save()
  {
    if ($this->editingSeatId) {
      $seat = Seat::findOrFail($this->editingSeatId);

      $seat->update([
        'seat_name' => $this->seat_name,
        'type' => $this->type,
        'capacity' => $this->capacity,
        'display_order' => $this->display_order,
        'is_active' => $this->is_active,
      ]);
    } else {
      Seat::create([
        'seat_name' => $this->seat_name,
        'type' => $this->type,
        'capacity' => $this->capacity,
        'display_order' => $this->display_order,
        'is_active' => $this->is_active,
      ]);
    }

    $this->reset([
      'seat_name',
      'type',
      'capacity',
      'display_order',
      'editingSeatId',
    ]);

    $this->is_active = true;
  }

  /**
   * 編集モードから抜ける
   * editingSeatId を null に戻す
   * →　フォームも空に戻す
   * →　新規追加モードへ戻る
   * @return void
   */
  public function cancelEdit()
  {
    $this->reset([
      'seat_name',
      'type',
      'capacity',
      'display_order',
      'editingSeatId',
    ]);

    $this->is_active = true;
  }

  /**
   * クリックされた$idの席情報をLivewireフォームに読み込む
   * @param int $id  席ID
   * @return void
   */
  public function edit(int $id)
  {
    $seat = Seat::findOrFail($id);

    $this->editingSeatId = $seat->id;
    $this->seat_name = $seat->seat_name;
    $this->type = $seat->type;
    $this->capacity = $seat->capacity;
    $this->display_order = $seat->display_order;
    $this->is_active = $seat->is_active;
  }

  public function render()
  {
    //seats DBを読みdisplay_order順に並べ,$seatsに入れる
    $seats = Seat::orderBy('display_order')->get();

    //
    return view('livewire.seat-manager', [
      'seats' => $seats,
    ]);
  }
}
