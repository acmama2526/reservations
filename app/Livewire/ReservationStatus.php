<?php

namespace App\Livewire;

use Livewire\Component;
use Carbon\Carbon; // 日付をインポート
use Livewire\Attributes\On;

class ReservationStatus extends Component
{
    public $date;
    public $times;
    public $seats = [
        "テーブルA",
        "テーブルB",
        "テーブルC",
        "座席A",
        "座席B",
        "カウンターA",
        "カウンターB",
        "カウンターC",
        "カウンターD",
        "カウンターE",
    ];
    public $reservations;

    public function mount()
    {
        // 今日の日付
        $this->date = Carbon::today()->format('Y-m-d');

        // タイムテーブルに表示する時間
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

        // 仮の予約データ
        $this->reservations = [
            [
                'seat' => 'テーブルA',
                'start_time' => '18:00',
                'end_time' => '20:00',
                'name' => '山田太郎',
                'people' => 4,
            ],
            [
                'seat' => 'テーブルB',
                'start_time' => '19:30',
                'end_time' => '20:30',
                'name' => '佐藤花子',
                'people' => 2,
            ],
        ];

        // 予約ごとにバーの長さを計算
        foreach ($this->reservations as &$reservation) {

            $startIndex = array_search(
                $reservation['start_time'],
                $this->times
            );

            $endIndex = array_search(
                $reservation['end_time'],
                $this->times
            );

            // 終了時間を含めるため +1
            $span = $endIndex - $startIndex + 1;

            // 計算結果をその予約に追加
            $reservation['span'] = $span;
        }

        // foreachの参照を解除
        unset($reservation);
    }

    /*
|--------------------------------------------------------------------------
| 新規予約を受け取る
|--------------------------------------------------------------------------
|
| #[On('reservation-created')]
|
| によって、
| ReservationFormからreservation-createdイベントが送られてきたら
| このメソッドが自動的に実行されます。
|
*/

    #[On('reservation-created')]
    public function addReservation(
        $seat,
        $startTime,
        $endTime,
        $name,
        $people
    ) {
        /*
     * 開始時間がtimes配列の何番目なのか調べます。
     */
        $startIndex = array_search(
            $startTime,
            $this->times
        );

        /*
     * 終了時間も同じように調べます。
     */
        $endIndex = array_search(
            $endTime,
            $this->times
        );


        /*
     * 予約バーの長さを計算します。
     *
     * 例：
     *
     * 18:00 → index 2
     * 19:00 → index 4
     *
     * 4 - 2 + 1 = 3マス
     */
        $span = $endIndex - $startIndex + 1;


        /*
     * reservations配列の最後に
     * 新しい予約を追加します。
     *
     * [] を付けることで、
     *
     * $this->reservations の最後に追加
     *
     * という意味になります。
     */
        $this->reservations[] = [
            'seat' => $seat,
            'start_time' => $startTime,
            'end_time' => $endTime,
            'name' => $name,
            'people' => $people,
            'span' => $span,
        ];
    }

    public function previousDay()
    {
        //$dateを1日戻す
        $this->date = Carbon::parse($this->date)->subDay()->format('Y-m-d');
    }

    public function nextDay()
    {
        //$dateを1日進める
        $this->date = Carbon::parse($this->date)->addDay()->format('Y-m-d');
    }

    public function render()
    {
        return view('livewire.reservation-status');
    }
}
