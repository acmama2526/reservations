<?php

namespace App\Livewire;

use App\Models\Reservation;
use App\Models\Seat;
use App\Models\ShopSetting;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Livewire\Attributes\On;
use Livewire\Component;

class ReservationStatus extends Component
{
    /*
    |--------------------------------------------------------------------------
    | 現在表示している日付
    |--------------------------------------------------------------------------
    */
    public string $date = '';


    /*
    |--------------------------------------------------------------------------
    | Gridの表示目盛り
    |--------------------------------------------------------------------------
    |
    | 15 / 30 / 60
    |
    | これは表示だけに使用します。
    |
    | 実際のドラッグ単位は
    | ShopSettingのslot_minutesを使用します。
    |
    */
    public int $displayMinutes = 30;


    /*
    |--------------------------------------------------------------------------
    | 時間目盛り
    |--------------------------------------------------------------------------
    */
    public array $times = [];


    /*
    |--------------------------------------------------------------------------
    | 初期表示
    |--------------------------------------------------------------------------
    */
    public function mount(): void
    {
        $this->date =
            today()->format('Y-m-d');

        $this->makeTimes();
    }


    /*
    |--------------------------------------------------------------------------
    | 前日
    |--------------------------------------------------------------------------
    */
    public function previousDay(): void
    {
        $this->date =
            Carbon::parse(
                $this->date
            )
                ->subDay()
                ->format('Y-m-d');


        $this->dateChanged();
    }


    /*
    |--------------------------------------------------------------------------
    | 翌日
    |--------------------------------------------------------------------------
    */
    public function nextDay(): void
    {
        $this->date =
            Carbon::parse(
                $this->date
            )
                ->addDay()
                ->format('Y-m-d');


        $this->dateChanged();
    }


    /*
    |--------------------------------------------------------------------------
    | 日付入力変更
    |--------------------------------------------------------------------------
    */
    public function updatedDate(): void
    {
        $this->dateChanged();
    }


    /*
    |--------------------------------------------------------------------------
    | TOPへ日付変更通知
    |--------------------------------------------------------------------------
    */
    private function dateChanged(): void
    {
        $this->dispatch(
            'reservation-date-changed',
            date: $this->date
        );
    }


    /*
    |--------------------------------------------------------------------------
    | 表示単位変更
    |--------------------------------------------------------------------------
    */
    public function updatedDisplayMinutes(
        $value
    ): void {

        $value = (int) $value;


        /*
         * 使用可能な表示単位
         */
        if (
            !in_array(
                $value,
                [
                    15,
                    30,
                    60,
                ],
                true
            )
        ) {

            $value = 30;
        }


        $this->displayMinutes =
            $value;


        $this->makeTimes();
    }


    /*
    |--------------------------------------------------------------------------
    | Gridの時間目盛り作成
    |--------------------------------------------------------------------------
    */
    private function makeTimes(): void
    {
        [
            $start,
            $end
        ] = $this->businessHours();


        $this->times = [];


        $current =
            $start->copy();


        /*
         * 営業終了時間もヘッダーに表示します。
         *
         * 例
         *
         * 17:00
         * 17:30
         * ...
         * 22:00
         */
        while (
            $current->lte($end)
        ) {

            $this->times[] =
                $current->format('H:i');


            $current->addMinutes(
                $this->displayMinutes
            );
        }


        /*
         * displayMinutesによって22:00が
         * ちょうど生成されない場合も、
         * 営業終了時刻を最後に表示します。
         */
        $endString =
            $end->format('H:i');


        if (
            end($this->times)
            !==
            $endString
        ) {

            $this->times[] =
                $endString;
        }
    }


    /*
    |--------------------------------------------------------------------------
    | 営業時間取得
    |--------------------------------------------------------------------------
    */
    private function businessHours(): array
    {
        $setting =
            ShopSetting::first();


        if ($setting) {

            return [
                Carbon::createFromFormat(
                    'H:i:s',
                    $setting->business_start
                ),

                Carbon::createFromFormat(
                    'H:i:s',
                    $setting->business_end
                ),
            ];
        }


        /*
         * 店舗設定が存在しない場合のみ
         */
        return [
            Carbon::createFromTime(
                17,
                0
            ),

            Carbon::createFromTime(
                22,
                0
            ),
        ];
    }


    /*
    |--------------------------------------------------------------------------
    | 予約クリック
    |--------------------------------------------------------------------------
    */
    public function selectReservation(
        int $reservationId
    ): void {

        $this->dispatch(
            'reservation-selected',
            reservationId:
                $reservationId
        );
    }


    /*
    |--------------------------------------------------------------------------
    | 空き時間クリック
    |--------------------------------------------------------------------------
    */
    public function selectEmptySlot(
        int $seatId,
        string $time
    ): void {

        /*
         * ReservationTopへ
         *
         * 日付
         * 席
         * 時間
         *
         * を送ります。
         */
        $this->dispatch(
            'reservation-slot-selected',

            date:
                $this->date,

            seatId:
                $seatId,

            time:
                $time
        );
    }


    /*
    |--------------------------------------------------------------------------
    | ドラッグ / リサイズ
    |--------------------------------------------------------------------------
    |
    | mode
    |
    | move
    | resize-start
    | resize-end
    |
    */
    public function moveReservation(
        int $reservationId,
        int $seatId,
        string $startTime,
        string $endTime,
        string $mode = 'move',
        int $sourceSeatId = 0
    ): void {

        /*
         * 予約取得
         */
        $reservation =
            Reservation::with('seats')
                ->findOrFail(
                    $reservationId
                );


        /*
        |--------------------------------------------------------------------------
        | 時刻をCarbonへ変換
        |--------------------------------------------------------------------------
        */
        try {

            $newStart =
                Carbon::createFromFormat(
                    'H:i',
                    $startTime
                );


            $newEnd =
                Carbon::createFromFormat(
                    'H:i',
                    $endTime
                );

        } catch (\Throwable $e) {

            $this->dispatch(
                'reservation-drag-failed',
                message:
                    '時間が正しくありません。'
            );

            return;
        }


        /*
        |--------------------------------------------------------------------------
        | 終了時間チェック
        |--------------------------------------------------------------------------
        */
        if (
            $newEnd->lte(
                $newStart
            )
        ) {

            $this->dispatch(
                'reservation-drag-failed',
                message:
                    '終了時間は開始時間より後にしてください。'
            );

            return;
        }


        /*
        |--------------------------------------------------------------------------
        | 営業時間
        |--------------------------------------------------------------------------
        */
        [
            $businessStart,
            $businessEnd
        ] = $this->businessHours();


        /*
        |--------------------------------------------------------------------------
        | 営業時間外チェック
        |--------------------------------------------------------------------------
        */
        if (
            $newStart->lt(
                $businessStart
            )
            ||
            $newEnd->gt(
                $businessEnd
            )
        ) {

            $this->dispatch(
                'reservation-drag-failed',
                message:
                    '営業時間外には移動できません。'
            );

            return;
        }


        /*
        |--------------------------------------------------------------------------
        | 移動先席が存在するか
        |--------------------------------------------------------------------------
        */
        $targetSeat =
            Seat::query()
                ->where(
                    'id',
                    $seatId
                )
                ->where(
                    'is_active',
                    true
                )
                ->first();


        if (!$targetSeat) {

            $this->dispatch(
                'reservation-drag-failed',
                message:
                    '移動先の席が存在しません。'
            );

            return;
        }

        if (
            $mode === 'move'
            && $sourceSeatId !== $seatId
            && (int) $reservation->people > (int) $targetSeat->capacity
        ) {
            $this->dispatch(
                'reservation-drag-failed',
                message: "移動先の席「{$targetSeat->seat_name}」は{$targetSeat->capacity}名までです。"
            );

            return;
        }


        /*
        |--------------------------------------------------------------------------
        | 現在の席
        |--------------------------------------------------------------------------
        */
        $seatIds =
            $reservation
                ->seats
                ->pluck('id')
                ->map(
                    fn ($id) =>
                        (int) $id
                )
                ->toArray();


        /*
        |--------------------------------------------------------------------------
        | 1席予約の場合
        |--------------------------------------------------------------------------
        |
        | 別の席へドラッグした場合は
        | その席へ変更します。
        |
        */
        if ($mode === 'move') {
            if (count($seatIds) <= 1) {
                $seatIds = [$seatId];
            } elseif ($sourceSeatId > 0 && in_array($sourceSeatId, $seatIds, true)) {
                $seatIds = array_values(array_unique(array_map(
                    static fn (int $id): int => $id === $sourceSeatId ? $seatId : $id,
                    $seatIds
                )));
            } else {
                $this->dispatch('reservation-drag-failed', message: '移動元の席を確認できません。');
                return;
            }
        }


        /*
        |--------------------------------------------------------------------------
        | 重複チェック
        |--------------------------------------------------------------------------
        |
        | 半開区間として判定します。
        |
        | 新規開始 < 既存終了
        | &&
        | 新規終了 > 既存開始
        |
        | 例
        |
        | 17:00～18:00
        | 18:00～19:00
        |
        | は重複しません。
        |
        */
        $conflict =
            Reservation::query()

                /*
                 * 自分自身は除外
                 */
                ->where(
                    'id',
                    '!=',
                    $reservation->id
                )

                /*
                 * 同じ日
                 */
                ->whereDate(
                    'reservation_date',
                    $this->date
                )

                /*
                 * キャンセル以外
                 */
                ->where(
                    'status',
                    '!=',
                    'cancelled'
                )

                /*
                 * 同じ席
                 */
                ->whereHas(
                    'seats',

                    function ($query) use (
                        $seatIds
                    ) {

                        $query->whereIn(
                            'seats.id',
                            $seatIds
                        );
                    }
                )

                /*
                 * 時間重複
                 */
                ->where(
                    'start_time',
                    '<',
                    $endTime
                )

                ->where(
                    'end_time',
                    '>',
                    $startTime
                )

                ->exists();


        /*
         * 重複している場合
         */
        if ($conflict) {

            $this->dispatch(
                'reservation-drag-failed',

                message:
                    'その時間帯には既に予約があります。'
            );

            return;
        }


        /*
        |--------------------------------------------------------------------------
        | DB更新
        |--------------------------------------------------------------------------
        */
        DB::transaction(
            function () use (
                $reservation,
                $startTime,
                $endTime,
                $seatIds
            ) {

                /*
                 * 時間更新
                 */
                $reservation->update([
                    'start_time' =>
                        $startTime,

                    'end_time' =>
                        $endTime,
                ]);


                /*
                 * 席更新
                 */
                $reservation
                    ->seats()
                    ->sync(
                        $seatIds
                    );
            }
        );


        /*
        |--------------------------------------------------------------------------
        | TOPへ更新通知
        |--------------------------------------------------------------------------
        |
        | 右側の予約一覧も
        | 最新状態へ更新されます。
        |
        */
        $this->dispatch(
            'reservation-updated'
        );


        /*
         * JS側へ成功通知
         */
        $this->dispatch(
            'reservation-drag-success'
        );
    }


    /*
    |--------------------------------------------------------------------------
    | 新規登録 / 編集モーダルからの更新
    |--------------------------------------------------------------------------
    */
    #[On('reservation-updated')]
    public function refreshReservations(): void
    {
        /*
         * メソッドが呼ばれることで
         * render()が再実行されます。
         */
    }


    /*
    |--------------------------------------------------------------------------
    | 画面表示
    |--------------------------------------------------------------------------
    */
    public function render()
    {
        /*
         * 営業時間
         */
        [
            $businessStart,
            $businessEnd
        ] = $this->businessHours();


        /*
         * 営業時間全体の分数
         *
         * 17:00～22:00
         * = 300分
         */
        $businessMinutes =
            $businessStart
                ->diffInMinutes(
                    $businessEnd
                );


        /*
         * 席一覧
         */
        $seatModels =
            Seat::query()
                ->where(
                    'is_active',
                    true
                )
                ->orderBy(
                    'display_order'
                )
                ->get();


        /*
         * 選択日の予約
         */
        $reservationModels =
            Reservation::with('seats')
                ->whereDate(
                    'reservation_date',
                    $this->date
                )
                ->where(
                    'status',
                    '!=',
                    'cancelled'
                )
                ->orderBy(
                    'start_time'
                )
                ->get();


        /*
         * Bladeへ渡す予約データ
         */
        $reservations = [];


        foreach (
            $reservationModels
            as $reservation
        ) {

            /*
             * 開始時間
             */
            $startTime =
                substr(
                    $reservation->start_time,
                    0,
                    5
                );


            /*
             * 終了時間
             */
            $endTime =
                substr(
                    $reservation->end_time,
                    0,
                    5
                );


            /*
             * Carbonへ変換
             */
            $start =
                Carbon::createFromFormat(
                    'H:i',
                    $startTime
                );


            $end =
                Carbon::createFromFormat(
                    'H:i',
                    $endTime
                );


            /*
             * 営業開始から予約開始までの分数
             */
            $startMinutes =
                $businessStart
                    ->diffInMinutes(
                        $start,
                        false
                    );


            /*
             * 予約時間
             */
            $durationMinutes =
                $start
                    ->diffInMinutes(
                        $end
                    );


            /*
             * Grid左位置 %
             */
            $left =
                (
                    $startMinutes
                    /
                    $businessMinutes
                )
                *
                100;


            /*
             * Grid横幅 %
             */
            $width =
                (
                    $durationMinutes
                    /
                    $businessMinutes
                )
                *
                100;


            /*
             * 予約されている各席へ
             * 同じ予約ブロックを表示
             */
            foreach (
                $reservation->seats
                as $seat
            ) {

                $reservations[] = [

                    'id' =>
                        $reservation->id,

                    'seat_id' =>
                        $seat->id,

                    'seat' =>
                        $seat->seat_name,

                    'name' =>
                        $reservation
                            ->customer_name,

                    'people' =>
                        $reservation->people,

                    'start_time' =>
                        $startTime,

                    'end_time' =>
                        $endTime,

                    'left' =>
                        max(
                            0,
                            $left
                        ),

                    'width' =>
                        max(
                            1,
                            $width
                        ),
                ];
            }
        }


        /*
         * 店舗設定
         */
        $setting =
            ShopSetting::first();


        /*
         * ドラッグ単位
         */
        $slotMinutes =
            $setting
                ? (int) $setting->slot_minutes
                : 15;


        return view(
            'livewire.reservation-status',

            [
                'seats' =>
                    $seatModels,

                'reservations' =>
                    $reservations,

                'businessStart' =>
                    $businessStart
                        ->format('H:i'),

                'businessEnd' =>
                    $businessEnd
                        ->format('H:i'),

                'businessMinutes' =>
                    $businessMinutes,

                'slotMinutes' =>
                    $slotMinutes,
            ]
        );
    }
}