<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\Seat;

class SeatManager extends Component
{
  public ?string $seat_name = null;
  public ?string $type = null;
  public ?int $capacity = null;
  public ?int $display_order = null;
  public bool $is_active = true;
  public ?int $editingSeatId = null;  //編集中の席id または null:新規作成


  /**
   * 利用可否の切り替えトグル
   * @param int $id  席ID
   * @return void
   */
  public function toggleActive(int $id)
  {
    $seat = Seat::findOrFail($id);

    $seat->update([
      'is_active' => ! $seat->is_active,
    ]);
  }

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
