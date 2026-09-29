<?php

namespace App\Services;

use App\Models\Seat;
use App\Models\Reservation;
use App\Models\ShopSetting as ShopSettingModel;

class SeatAssignmentService
{
  /**
   * 指定日時で利用可能な席IDを取得する
   * @return array
   */
  /**
   * 指定日時で利用可能な席IDを取得する
   *
   * @return array
   */
  public function searchAvailableSeatIds(
    string $reservationDate,
    string $startTime,
    string $endTime,
    ?int $excludeReservationId = null
  ): array {

    // 営業時間チェック
    $setting = ShopSettingModel::first();

    if ($setting) {
      $businessStart = substr($setting->business_start, 0, 5);
      $businessEnd   = substr($setting->business_end, 0, 5);

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

    // 同じ日・時間帯で重なっている予約を検索
    $query = Reservation::where(
      'reservation_date',
      $reservationDate
    )
      ->where('status', '!=', 'cancelled')
      ->where('start_time', '<', $endTime)
      ->where('end_time', '>', $startTime);

    // 予約変更時は、自分自身の予約を使用中判定から除外する
    if ($excludeReservationId) {
      $query->where('id', '!=', $excludeReservationId);
    }

    // 予約済みの席IDを取得
    $reservedSeatIds = $query
      ->with('seats')
      ->get()
      ->flatMap(function ($reservation) {
        return $reservation->seats->pluck('id');
      })
      ->unique()
      ->values()
      ->all();

    // 利用可能な席IDを取得
    $availableSeatIds = Seat::where('is_active', true)
      ->whereNotIn('id', $reservedSeatIds)
      ->orderBy('display_order')
      ->pluck('id')
      ->all();

    return [
      'availableSeatIds' => $availableSeatIds,
      'error' => null,
    ];
  }

  /**
   * 人数・日時から自動配置する席IDを決める
   * @return array
   */
  /**
   * 人数・日時から自動配置する席IDを決める
   *
   * @return array
   */
  public function assign(
    int $people,
    string $reservationDate,
    string $startTime,
    string $endTime,
    ?int $excludeReservationId = null
  ): array {

    // まず空席検索
    $searchResult = $this->searchAvailableSeatIds(
      $reservationDate,
      $startTime,
      $endTime,
      $excludeReservationId
    );

    // 営業時間外などのエラーがあれば、そのまま返す
    if ($searchResult['error']) {
      return [
        'availableSeatIds' => [],
        'selectedSeatIds' => [],
        'error' => $searchResult['error'],
      ];
    }

    $availableSeatIds = $searchResult['availableSeatIds'];

    // 空いている席を取得
    $availableSeats = Seat::whereIn('id', $availableSeatIds)
      ->orderBy('capacity')
      ->get();

    $bestCombination = null;
    $bestExcess = PHP_INT_MAX;
    $bestCount = PHP_INT_MAX;

    $seatCount = $availableSeats->count();

    // 空席の全組み合わせを調べる
    for ($mask = 1; $mask < (1 << $seatCount); $mask++) {

      $combination = [];
      $totalCapacity = 0;

      for ($i = 0; $i < $seatCount; $i++) {

        if ($mask & (1 << $i)) {
          $seat = $availableSeats[$i];

          $combination[] = $seat->id;
          $totalCapacity += $seat->capacity;
        }
      }

      // 人数を収容できない組み合わせは除外
      if ($totalCapacity < $people) {
        continue;
      }

      $excess = $totalCapacity - $people;
      $count = count($combination);

      // 使用する席数が少ない組み合わせを優先
      // 席数が同じなら、定員の余りが少ない方を優先
      if (
        $count < $bestCount ||
        ($count === $bestCount && $excess < $bestExcess)
      ) {
        $bestCombination = $combination;
        $bestExcess = $excess;
        $bestCount = $count;
      }
    }

    // 人数を収容できる組み合わせが見つからなかった場合
    if ($bestCombination === null) {
      return [
        'availableSeatIds' => $availableSeatIds,
        'selectedSeatIds' => [],
        'error' => '人数分の席を確保できません。',
      ];
    }

    return [
      'availableSeatIds' => $availableSeatIds,
      'selectedSeatIds' => $bestCombination,
      'error' => null,
    ];
  }
}