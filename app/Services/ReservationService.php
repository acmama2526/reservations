<?php

namespace App\Services;

use App\Models\Reservation;
use App\Models\ShopSetting;
use Carbon\Carbon;
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

            // 営業時間を確認
            $this->checkBusinessHours(
                $data['reservation_date'],
                $data['start_time'],
                $data['end_time']
            );

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

    public function update(Reservation $reservation, array $data, array $seatIds): Reservation
    {
        return DB::transaction(function () use ($reservation, $data, $seatIds) {

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
                $data['end_time'],
                $reservation->id
            );

            // 予約を更新
            $reservation->update([
                'customer_name' => $data['customer_name'],
                'people' => $data['people'],
                'reservation_date' => $data['reservation_date'],
                'start_time' => $data['start_time'],
                'end_time' => $data['end_time'],
                'phone' => $data['phone'] ?? null,
                'status' => $data['status'],
                'description' => $data['description'] ?? null,
            ]);

            // 席を更新
            $reservation->seats()->sync($seatIds);

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
        string $endTime,
        ?int $excludeReservationId = null
    ): void {
        $query = Reservation::query()
            ->where('reservation_date', $reservationDate)

            // キャンセル済みの予約は対象外
            ->where('status', '!=', 'cancelled')

            // 指定した席のどれかを使用している予約
            ->whereHas('seats', function ($query) use ($seatIds) {
                $query->whereIn('seats.id', $seatIds);
            })

            // 時間が重なっているか
            ->where('start_time', '<', $endTime)
            ->where('end_time', '>', $startTime);

        // 編集中の予約自身はダブルブッキングの対象外にする
        if ($excludeReservationId !== null) {
            $query->where('id', '!=', $excludeReservationId);
        }

        $exists = $query->exists();

        if ($exists) {
            throw ValidationException::withMessages([
                'startTime' => '選択した席は、その時間帯にすでに予約されています。',
            ]);
        }
    }

    //営業時間のチェック
    private function checkBusinessHours(
        string $reservationDate,
        string $startTime,
        string $endTime
    ): void {

        $shopSetting = ShopSetting::first();

        if (!$shopSetting) {
            throw ValidationException::withMessages([
                'reservationDate' => '店舗設定が登録されていません。',
            ]);
        }

        $date = Carbon::parse($reservationDate);

        // 定休日チェック
        $dayOfWeek = $date->locale('ja')->isoFormat('dddd');

        if (in_array($dayOfWeek, $shopSetting->closed_days ?? [], true)) {
            throw ValidationException::withMessages([
                'reservationDate' => "この日は定休日（{$dayOfWeek}）です。",
            ]);
        }

        // 営業時間をCarbonに変換
        $businessStart = Carbon::createFromFormat(
            'H:i:s',
            $shopSetting->business_start
        );

        $businessEnd = Carbon::createFromFormat(
            'H:i:s',
            $shopSetting->business_end
        );

        $start = Carbon::createFromFormat('H:i', $startTime);
        $end = Carbon::createFromFormat('H:i', $endTime);

        // 営業時間外チェック
        if ($start->lt($businessStart) || $end->gt($businessEnd)) {
            throw ValidationException::withMessages([
                'startTime' => sprintf(
                    '営業時間は%s～%sです。',
                    $businessStart->format('H:i'),
                    $businessEnd->format('H:i')
                ),
            ]);
        }

        // 15分単位チェック
        $slotMinutes = (int) $shopSetting->slot_minutes;

        if ($slotMinutes > 0) {
            $startMinutes = ((int) $start->format('H')) * 60
                + (int) $start->format('i');

            $endMinutes = ((int) $end->format('H')) * 60
                + (int) $end->format('i');

            if (
                $startMinutes % $slotMinutes !== 0
                || $endMinutes % $slotMinutes !== 0
            ) {
                throw ValidationException::withMessages([
                    'startTime' => "予約時間は{$slotMinutes}分単位で指定してください。",
                ]);
            }
        }
    }
}
