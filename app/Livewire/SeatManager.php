<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\Seat;
use App\Models\Reservation;
use App\Models\ShopSetting as ShopSettingModel;
use App\Services\SeatAssignmentService;

class SeatManager extends Component
{
  public ?string $seat_name = null;
  public ?string $type = null;
  public ?int $capacity = null;
  public ?int $display_order = null;
  public bool $is_active = true;
  public ?int $editingSeatId = null;  //編集中の席id または null:新規作成

  //席自動配置用データ
  public ?int $people = null;               //予約人数
  public ?string $reservation_date = null;  //予約日
  public ?string $start_time = null;        //開始時刻
  public ?string $end_time = null;          //終了時刻
  public array $availableSeatIds = [];      //空いている席を入れる配列
  public array $selectedSeatIds = [];       //自動配置で最終的に選ばれた席を入れる配列
  public ?int $reservationId = null;        // 対象となる予約ID テスト用


  /**
   * 予約情報と既存予約を基に、自動席割り当てを行い、selectedSeatIds[]に格納
   * @return void
   * SIDE EFFECT: selectedSeatIds[]を更新。最終的に選ばれた席IDの配列
   */
  public function autoAssignSeats()
  {
    $service = app(SeatAssignmentService::class);

    $result = $service->assign(
      $this->people,
      $this->reservation_date,
      $this->start_time,
      $this->end_time,
      $this->reservationId
    );

    $this->availableSeatIds = $result['availableSeatIds'];
    $this->selectedSeatIds = $result['selectedSeatIds'];

    if ($result['error']) {
      session()->flash('error', $result['error']);
    }
  }

  /**
   * 自動配置した席を予約DBに保存
   * @return void
   */
  public function saveAssignment()
  {
    if (! $this->reservationId) {
      session()->flash('error', '予約IDを入力してください。');
      return;
    }

    if (empty($this->selectedSeatIds)) {
      session()->flash('error', '先に自動配置を実行してください。');
      return;
    }

    //reservations.id から対象予約を取得
    $reservation = Reservation::find($this->reservationId);

    if (! $reservation) {
      session()->flash('error', '指定された予約IDが見つかりません。');
      return;
    }

    //reservation_seat に保存
    $reservation->seats()->sync($this->selectedSeatIds);

    session()->flash('message', '席配置を保存しました。');
  }

  /**
   * 利用可能な席を検索し、availableSeatIds[]に格納
   * @return void
   * SIDE EFFECT: availableSeatIds[]を更新
   */
  public function searchAvailableSeats()
  {
    $service = app(SeatAssignmentService::class);

    $result = $service->searchAvailableSeatIds(
      $this->reservation_date,
      $this->start_time,
      $this->end_time
    );

    $this->availableSeatIds = $result['availableSeatIds'];

    if ($result['error']) {
      $this->selectedSeatIds = [];

      session()->flash('error', $result['error']);
    }
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