<?php

namespace App\Livewire;

use App\Models\Seat;
use App\Models\ShopSetting;
use App\Services\ReservationService;
use Carbon\Carbon;
use Livewire\Component;

class ReservationCreate extends Component
{
    // お客様名
    public string $customerName = '';

    // 人数
    public string $people = '';

    // 電話番号
    public string $phone = '';

    // 予約日
    public string $reservationDate = '';

    // 開始時間
    public string $startTime = '';

    // 終了時間
    public string $endTime = '';

    // 選択した席
    public string $seat = '';

    // 備考
    public string $description = '';

    /**
     * 画面を開いたときに実行される
     */
    public function mount(): void
    {
        // 予約日は今日の日付を初期値にする
        $this->reservationDate = now()->format('Y-m-d');
    }

    /**
     * 予約を登録する
     */
    public function save(ReservationService $service): void
    {
        // ① 入力チェック
        $this->validate(
            [
                'customerName' => [
                    'required',
                    'string',
                    'max:255',
                ],

                'people' => [
                    'required',
                    'integer',
                    'min:1',
                ],

                'phone' => [
                    'nullable',
                    'string',
                    'max:20',
                ],

                'reservationDate' => [
                    'required',
                    'date',
                ],

                'startTime' => [
                    'required',
                    'date_format:H:i',
                ],

                'endTime' => [
                    'required',
                    'date_format:H:i',
                    'after:startTime',
                ],

                'seat' => [
                    'required',
                    'integer',
                    'exists:seats,id',
                ],

                'description' => [
                    'nullable',
                    'string',
                ],
            ],
            [
                'endTime.after' => '終了時間は開始時間より後の時間を選択してください。',
            ]
        );

        // ② ReservationServiceに渡すデータを作る
        $service->create(
            [
                'customer_name' => $this->customerName,
                'people' => $this->people,
                'reservation_date' => $this->reservationDate,
                'start_time' => $this->startTime,
                'end_time' => $this->endTime,
                'phone' => $this->phone,
                'status' => 'reserved',
                'description' => $this->description,
            ],
            [
                (int) $this->seat,
            ]
        );

        // ③ 登録完了メッセージ
        session()->flash(
            'message',
            '予約を登録しました。'
        );

        // ④ 予約一覧へ戻る
        $this->redirectRoute('reservations.index');
    }

    /**
     * 入力内容をクリアする
     */
    public function clear(): void
    {
        $this->customerName = '';
        $this->people = '';
        $this->phone = '';
        $this->reservationDate = now()->format('Y-m-d');
        $this->startTime = '';
        $this->endTime = '';
        $this->seat = '';
        $this->description = '';

        // エラーメッセージも消す
        $this->resetValidation();
    }

    /**
     * 画面を表示する
     */
    public function render()
    {
        // 席情報取得
        $seats = Seat::query()
            ->where('is_active', true)
            ->orderBy('display_order')
            ->get();

        // 店舗設定を取得
        $shopSetting = ShopSetting::first();

        // 初期値
        $start = null;
        $end = null;
        $slotMinutes = 0;

        // 店舗設定が存在する場合
        if ($shopSetting) {
            // 営業開始時間
            $start = Carbon::createFromFormat(
                'H:i:s',
                $shopSetting->business_start
            );

            // 営業終了時間
            $end = Carbon::createFromFormat(
                'H:i:s',
                $shopSetting->business_end
            );

            // 予約時間の単位
            $slotMinutes = (int) $shopSetting->slot_minutes;
        }

        return view('livewire.reservation-create', [
            'seats' => $seats,
            'shopSetting' => $shopSetting,
            'start' => $start,
            'end' => $end,
            'slotMinutes' => $slotMinutes,
        ]);
    }
}
