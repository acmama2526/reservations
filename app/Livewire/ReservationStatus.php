<?php

namespace App\Livewire;

use App\Models\Reservation;
use App\Models\Seat;
use App\Models\ShopSetting;
use Carbon\Carbon;
use Livewire\Attributes\On;
use Livewire\Component;

class ReservationStatus extends Component
{
    // 日付
    public string $date = '';
    // 時間(配列)
    public array $times = [];


    // 今日の日付がデフォルト
    public function mount(): void
    {
        // 最初は今日を表示
        $this->date = Carbon::today()->format('Y-m-d');

        // 営業時間から予約枠を作成
        $this->makeTimes();
    }


    //　前の日へ
    public function previousDay(): void
    {
        $this->date = Carbon::parse($this->date)
            ->subDay()
            ->format('Y-m-d');
    }

    //　次の日へ
    public function nextDay(): void
    {
        $this->date = Carbon::parse($this->date)
            ->addDay()
            ->format('Y-m-d');
    }

    // 時間枠を作成

    // | ShopSettingから
    // |
    // | business_start
    // | business_end
    // | slot_minutes
    // |
    // | を取得して時間一覧を作ります。


    private function makeTimes(): void
    {
        $shopSetting = ShopSetting::first();


        /*
         * 店舗設定がまだ存在しない場合は、
         * 仮の営業時間を使用します。
         */
        if (!$shopSetting) {

            $this->times = [
                '17:00',
                '17:30',
                '18:00',
                '18:30',
                '19:00',
                '19:30',
                '20:00',
                '20:30',
                '21:00',
                '21:30',
                '22:00',
            ];

            return;
        }


        /*
         * DBの営業時間をCarbonへ変換
         */
        $start = Carbon::createFromFormat(
            'H:i:s',
            $shopSetting->business_start
        );

        $end = Carbon::createFromFormat(
            'H:i:s',
            $shopSetting->business_end
        );

        $slotMinutes = (int) $shopSetting->slot_minutes;


        /*
         * 時間一覧を一度空にする
         */
        $this->times = [];


        /*
         * 営業開始～営業終了まで
         * slot_minutes間隔で作成します。
         */
        $current = $start->copy();

        while ($current <= $end) {

            $this->times[] = $current->format('H:i');

            $current->addMinutes($slotMinutes);
        }
    }


    /*
    |--------------------------------------------------------------------------
    | 画面表示
    |--------------------------------------------------------------------------
    */

    public function render()
    {
        /*
        |--------------------------------------------------------------------------
        | 使用可能な席を取得
        |--------------------------------------------------------------------------
        |
        | is_active = true の席だけ表示します。
        |
        | display_order が設定されているので、
        | その順番で表示します。
        |
        */

        $seatModels = Seat::query()
            ->where('is_active', true)
            ->orderBy('display_order')
            ->get();


        /*
        |--------------------------------------------------------------------------
        | 選択日の予約を取得
        |--------------------------------------------------------------------------
        |
        | カレンダーで選択されている $date の予約だけ取得します。
        |
        | with('seats') によって
        | 予約に紐付いている席も一緒に取得します。
        |
        */

        $reservationModels = Reservation::with('seats')
            ->whereDate('reservation_date', $this->date)
            ->where('status', '!=', 'cancelled')
            ->orderBy('start_time')
            ->get();


        /*
        |--------------------------------------------------------------------------
        | Bladeで使いやすい席配列へ変換
        |--------------------------------------------------------------------------
        |
        | Bladeでは今まで
        |
        | @foreach ($seats as $seat)
        |
        | としていたので、その形を維持します。
        |
        */

        $seats = $seatModels
            ->pluck('seat_name')
            ->toArray();


        /*
        |--------------------------------------------------------------------------
        | Blade用予約データを作成
        |--------------------------------------------------------------------------
        */

        $reservations = [];


        foreach ($reservationModels as $reservation) {

            /*
             * DBでは1つの予約に複数の席を
             * 紐付けられる構造になっています。
             *
             * そのため席ごとに予約バーを作ります。
             */

            foreach ($reservation->seats as $seat) {

                /*
                 * DBのtime型は
                 *
                 * 18:00:00
                 *
                 * のようになっているため、
                 *
                 * 18:00
                 *
                 * に変換します。
                 */

                $startTime = substr(
                    $reservation->start_time,
                    0,
                    5
                );

                $endTime = substr(
                    $reservation->end_time,
                    0,
                    5
                );


                /*
                |--------------------------------------------------------------------------
                | 予約バーの長さを計算
                |--------------------------------------------------------------------------
                |
                | $times の何番目から何番目まで使うかを探します。
                |
                */

                $startIndex = array_search(
                    $startTime,
                    $this->times,
                    true
                );

                $endIndex = array_search(
                    $endTime,
                    $this->times,
                    true
                );


                /*
                 * 営業時間外などで時間が見つからない場合は
                 * タイムラインに表示しません。
                 */
                if (
                    $startIndex === false ||
                    $endIndex === false
                ) {
                    continue;
                }


                /*
                 * 現在のプロジェクトでは
                 * 「終了時間も1枠として含める」
                 * という仕様にしています。
                 *
                 * 18:00～19:00なら
                 *
                 * 18:00
                 * 18:30
                 * 19:00
                 *
                 * の3枠です。
                 */

                $span = $endIndex - $startIndex + 1;


                /*
                 * Bladeがこれまで使用していた形式へ変換します。
                 */

                $reservations[] = [

                    'seat' => $seat->seat_name,

                    'start_time' => $startTime,

                    'end_time' => $endTime,

                    'name' => $reservation->customer_name,

                    'people' => $reservation->people,

                    'span' => $span,
                ];
            }
        }


        /*
        |--------------------------------------------------------------------------
        | Bladeへ渡す
        |--------------------------------------------------------------------------
        */

        return view(
            'livewire.reservation-status',
            [
                'seats' => $seats,
                'reservations' => $reservations,
            ]
        );
    }
}