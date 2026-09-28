<?php

namespace App\Services;

use App\Models\Reservation;
use App\Models\Seat;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class ReservationService
{
    /**
     * 予約を新規登録する
     *
     * @param array $data     予約情報
     * @param array $seatIds  使用する席のID
     * @return Reservation
     */
    public function create(array $data, array $seatIds): Reservation
    {
        // トランザクション開始
        return DB::transaction(function () use ($data, $seatIds) {

            // 選択された席を取得
            $seats = Seat::query()
                ->whereIn('id', $seatIds)
                ->where('is_active', true)
                ->get();

            // 選択した席が存在するか確認
            if ($seats->count() !== count($seatIds)) {
                throw ValidationException::withMessages([
                    'seat' => '選択された席が存在しない、または利用できません。',
                ]);
            }

            // 席の合計収容人数を計算
            $totalCapacity = $seats->sum('capacity');

            // 人数が席の定員を超えていないか確認
            if ($totalCapacity < (int) $data['people']) {
                throw ValidationException::withMessages([
                    'people' => '選択された席の定員が人数を満たしていません。',
                ]);
            }

            // ダブルブッキングを確認
            $this->checkDoubleBooking(
                $seatIds,
                $data['reservation_date'],
                $data['start_time'],
                $data['end_time']
            );

            // 予約を登録
            $reservation = Reservation::create([
                'customer_name' => $data['customer_name'],
                'people' => $data['people'],
                'reservation_date' => $data['reservation_date'],
                'start_time' => $data['start_time'],
                'end_time' => $data['end_time'],
                'phone' => $data['phone'] ?? null,
                'status' => $data['status'] ?? 'reserved',
                'description' => $data['description'] ?? null,
            ]);

            // 予約と席を紐付ける
            $reservation->seats()->sync($seatIds);

            // 登録した予約を返す
            return $reservation;
        });
    }

    /**
     * ダブルブッキングを確認する
     */
    private function checkDoubleBooking(
        array $seatIds,
        string $reservationDate,
        string $startTime,
        string $endTime
    ): void {
        $exists = Reservation::query()
            ->where('reservation_date', $reservationDate)

            // キャンセル済みの予約は対象外
            ->where('status', '!=', 'cancelled')

            // 指定した席のどれかを使用している予約
            ->whereHas('seats', function ($query) use ($seatIds) {
                $query->whereIn('seats.id', $seatIds);
            })

            // 時間が重なっているか
            ->where('start_time', '<', $endTime)
            ->where('end_time', '>', $startTime)

            ->exists();

        if ($exists) {
            throw ValidationException::withMessages([
                'startTime' => '選択した席は、その時間帯にすでに予約されています。',
            ]);
        }
    }
}