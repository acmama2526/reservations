<?php

namespace App\Services;

use App\Models\Seat;
use App\Models\Reservation;
use App\Models\ShopSetting as ShopSettingModel;

class SeatAssignmentService
{
  /**
   * 指定した予約日・時間帯で利用可能な席IDを取得する。
   *
   * 処理内容：
   * 1. 店舗営業時間内か確認
   * 2. 同じ日時に重なっている予約を検索
   * 3. 予約済み席を除外
   * 4. is_active=true の席だけを空席として返す
   *
   * 予約編集時は $excludeReservationId を指定すると、
   * 編集対象の予約自身を使用中判定から除外できる。
   *
   * @return array
   */
  public function searchAvailableSeatIds(
    string $reservationDate,
    string $startTime,
    string $endTime,
    ?int $excludeReservationId = null
  ): array {

    // 店舗設定から営業時間を取得
    $setting = ShopSettingModel::first();

    if ($setting) {
      // DBの HH:MM:SS を画面入力と同じ HH:MM に揃える
      $businessStart = substr($setting->business_start, 0, 5);
      $businessEnd   = substr($setting->business_end, 0, 5);

      // 開始または終了が営業時間の外なら検索を中止
      if (
        $startTime < $businessStart ||
        $endTime > $businessEnd
      ) {
        return [
          'availableSeatIds' => [],
          'error' => '営業時間外です。営業時間は '
            . $businessStart
            . ' ～ '
            . $businessEnd
            . ' です。',
        ];
      }
    }

    // 同じ日で、指定時間帯と重なっているキャンセル以外の予約を検索
    $query = Reservation::where(
      'reservation_date',
      $reservationDate
    )
      ->where('status', '!=', 'cancelled')
      ->where('start_time', '<', $endTime)
      ->where('end_time', '>', $startTime);

    // 予約変更時、自分自身の予約は空席判定の競合対象から除外
    if ($excludeReservationId) {
      $query->where('id', '!=', $excludeReservationId);
    }

    // 条件に該当する予約を取得し、
    // 各予約に紐づく席IDだけを1つの配列にまとめる
    $reservedSeatIds = $query
      ->with('seats')
      ->get()
      ->flatMap(function ($reservation) {
        return $reservation->seats->pluck('id');
      })
      ->unique()
      ->values()
      ->all();

    // 使用可能な席から予約済み席を除外し、空席IDだけ取得
    $availableSeatIds = Seat::where('is_active', true)
      ->whereNotIn('id', $reservedSeatIds)
      ->orderBy('display_order')
      ->pluck('id')
      ->all();

    // 呼び出し元が扱いやすいように、結果とエラーを配列で返す
    return [
      'availableSeatIds' => $availableSeatIds,
      'error' => null,
    ];
  }

  /**
   * 人数・日時から最適な席の組み合わせを決定する。
   *
   * 処理内容：
   * 1. 空席検索
   * 2. 営業時間などのエラー確認
   * 3. 空席の全組み合わせを列挙
   * 4. 人数を収容できる組み合わせだけ残す
   * 5. 使用席数最少を優先
   * 6. 同数なら余り定員最少を優先
   *
   * @return array
   * 
   * 例：
   * [
   * 'availableSeatIds' => [4, 5, 6, 7],
   * 'selectedSeatIds' => [4, 5],
   * 'error' => null,
   * ]
   */
  public function assign(
    int $people,
    string $reservationDate,
    string $startTime,
    string $endTime,
    ?int $excludeReservationId = null
  ): array {

    // まず指定日時の空席を取得する
    $searchResult = $this->searchAvailableSeatIds(
      $reservationDate,
      $startTime,
      $endTime,
      $excludeReservationId         //編集時に除外したい「自分自身の予約ID」
    );

    // 空席検索時点でエラーなら、自動配置計算を行わずそのまま返す
    if ($searchResult['error']) {
      return [
        'availableSeatIds' => [],
        'selectedSeatIds' => [],
        'error' => $searchResult['error'],
      ];
    }

    $availableSeatIds = $searchResult['availableSeatIds'];

    // 空席IDから席情報を取得し、定員の小さい順に並べる
    $availableSeats = Seat::whereIn('id', $availableSeatIds)
      ->orderBy('capacity')
      ->get();

    $bestCombination = null;    // 現時点で最良の席組み合わせ
    $bestExcess = PHP_INT_MAX;  // 定員の余り人数の最小値
    $bestCount = PHP_INT_MAX;   // 使用席数の最小値

    $seatCount = $availableSeats->count();

    // ビット演算を使って、空席の全組み合わせを1通りずつ調べる
    // 例：3席なら 001, 010, 011, 100, 101, 110, 111 の7通り
    for ($mask = 1; $mask < (1 << $seatCount); $mask++) {

      $combination = [];    //候補シートのidの組み合わせ
      $totalCapacity = 0;   //その許容人数合計

      for ($i = 0; $i < $seatCount; $i++) {

        // 現在の組み合わせに i番目の席が含まれていれば採用
        if ($mask & (1 << $i)) {
          $seat = $availableSeats[$i];

          $combination[] = $seat->id;
          $totalCapacity += $seat->capacity;
        }
      }

      // 合計定員が予約人数に届かない組み合わせは候補外
      if ($totalCapacity < $people) {
        continue;
      }

      $excess = $totalCapacity - $people;   // 定員の余り人数
      $count = count($combination);         // 使用する席数

      // ①使用する席数が少ない組み合わせを優先
      // ②席数が同じなら、定員の余りが少ない方を優先
      if (
        $count < $bestCount ||
        ($count === $bestCount && $excess < $bestExcess)
      ) {
        $bestCombination = $combination;
        $bestExcess = $excess;
        $bestCount = $count;
      }
    }

    // 全候補を調べても収容可能な組み合わせがなければエラー
    if ($bestCombination === null) {
      return [
        'availableSeatIds' => $availableSeatIds,
        'selectedSeatIds' => [],
        'error' => '人数分の席を確保できません。',
      ];
    }

    // 空席一覧と最適配置結果を呼び出し元へ返す
    return [
      'availableSeatIds' => $availableSeatIds,
      'selectedSeatIds' => $bestCombination,
      'error' => null,
    ];
  }
}
