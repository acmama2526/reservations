<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\Seat;
use App\Models\Reservation;
use App\Models\ShopSetting as ShopSettingModel;
use App\Services\SeatAssignmentService;
use Illuminate\Validation\Rule;

class SeatManager extends Component
{
  public ?string $seat_name = null;
  public ?string $type = null;
  public ?int $capacity = null;
  public ?int $display_order = null;
  public bool $is_active = true;
  public ?int $editingSeatId = null;  //編集中の席id または null:新規作成

  //席自動配置用データ
  public ?int $people = null;               // 予約人数
  public ?string $reservation_date = null;  // 予約日
  public ?string $start_time = null;        // 開始時刻
  public ?string $end_time = null;          // 終了時刻
  public array $availableSeatIds = [];      // 空いている席を入れる配列
  public array $selectedSeatIds = [];       // 自動配置で最終的に選ばれた席を入れる配列
  public ?int $reservationId = null;        // 対象となる予約ID テスト用


  /**
   * Blade画面から入力された予約条件を SeatAssignmentService に渡し、
   * 自動配置結果を Livewire の画面状態へ反映する。
   * このメソッド自身は席の組み合わせ計算を行わず、
   * 実際の自動配置ロジックは SeatAssignmentService に任せる。
   * wire:click="autoAssignSeats" などから呼ばれる。
   *
   * @return void
   * SIDE EFFECT:
   * - availableSeatIds[] を更新
   * - selectedSeatIds[] を更新
   * - エラー時は session flash にメッセージを設定
   */
  public function autoAssignSeats()
  {
    // Laravelのサービスコンテナから SeatAssignmentService のインスタンスを取得
    // new SeatAssignmentService() と直接生成せず、Laravelに生成を任せる
    $service = app(SeatAssignmentService::class);

    // 予約条件を Service に渡して自動配置を実行する
    // reservationId は予約編集時、自分自身の予約を空席判定から除外するために使用
    // assign() の返り値例
    // [
    //   'availableSeatIds' => [1, 2, 5], // 空いている席
    //   'selectedSeatIds'  => [2, 5],    // 自動配置で選ばれた席
    //   'error'            => null,      // エラーメッセージ
    // ]
    $result = $service->assign(   //SeatAssignmentServiceのassignをCallし、$resultに最適な席を返す
      $this->people,              //人数
      $this->reservation_date,    //予約日
      $this->start_time,          //開始時刻
      $this->end_time,            //終了時刻
      $this->reservationId        //編集時に除外したい「自分自身の予約ID」
    );

    // Serviceの計算結果をLivewireプロパティへ反映
    // プロパティを書き換えるとBlade側の表示も自動更新される
    $this->availableSeatIds = $result['availableSeatIds'];  //空いてる席idの配列
    $this->selectedSeatIds = $result['selectedSeatIds'];    //選択された席idの配列

    // Serviceからエラーが返った場合、画面表示用に1回だけセッションへ保存
    if ($result['error']) {
      session()->flash('error', $result['error']);
    }
  }

  /**
   * 自動配置結果を既存予約に紐付けて保存する。
   * このメソッドは reservations 本体の人数・日時は変更しない。
   * reservation_seat 中間テーブルの席割り当てだけを更新する。
   *
   * 現在はテスト画面用。
   *
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

    // reservation_seat の紐付けを selectedSeatIds の内容に揃える
    // 既存の席割り当ては必要に応じて解除され、新しい席へ置き換わる
    $reservation->seats()->sync($this->selectedSeatIds);

    session()->flash('message', '席配置を保存しました。');
  }

  /**
   * 指定日時の空席だけを取得し、画面表示用プロパティへ反映する。
   *
   * 実際の営業時間チェック・予約重複判定・空席抽出は
   * SeatAssignmentService に任せる。
   *
   * @return void
   */
  public function searchAvailableSeats()
  {
    $service = app(SeatAssignmentService::class);

    // 日付・開始・終了時刻をServiceへ渡して空席検索
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
    // バリデーション：席名、収容人数、表示順の必須チェック
    $this->validate([
      'seat_name' => 'required|string|max:255',
      'type' => 'required|in:テーブル,座敷,カウンター',
      'capacity' => 'required|integer|min:1',
      'display_order' => [
        'required',
        'integer',
        'min:1',
        Rule::unique('seats', 'display_order')->ignore($this->editingSeatId),
      ],
    ], [
      'seat_name.required' => '席名を入力してください。',
      'capacity.required' => '定員を入力してください。',
      'capacity.integer' => '定員は整数で入力してください。',
      'capacity.min' => '定員は1以上で入力してください。',
      'type.required' => '種類を選択してください。',
      'type.in' => '種類を正しく選択してください。',
      'display_order.required' => '表示順を入力してください。',
      'display_order.integer' => '表示順は整数で入力してください。',
      'display_order.min' => '表示順は1以上で入力してください。',
      'display_order.unique' => 'この表示順はすでに使用されています。',
    ]);

    // editingSeatId がある場合は既存席を更新
    // null の場合は新規席を作成
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
    // 編集中のフォーム内容を破棄し、新規登録モードへ戻す
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
    // 指定された席をDBから取得し、その値をLivewireプロパティへコピー
    $seat = Seat::findOrFail($id);

    // Bladeの編集フォームに既存値を表示する
    $this->editingSeatId = $seat->id;
    $this->seat_name = $seat->seat_name;
    $this->type = $seat->type;
    $this->capacity = $seat->capacity;
    $this->display_order = $seat->display_order;
    $this->is_active = $seat->is_active;

    // Blade側へ「編集フォームまでスクロールして」と通知
    $this->dispatch('scroll-to-seat-form');
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
