<?php

namespace App\Livewire;

use App\Models\Seat;
use App\Models\ShopSetting;
use App\Services\ReservationService;
use App\Services\SeatAssignmentService;
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

    // 手動で選択した席
    // public string $seat = '';

    // 手動選択・自動配置の両方で使用
    public array $selectedSeatIds = [];

    // 自動配置のエラー
    public string $autoAssignError = '';

    // 備考
    public string $description = '';


    /**
     * 初期表示
     */
    public function mount(): void
    {
        // 予約日は今日の日付を初期値にする
        $this->reservationDate = now()->format('Y-m-d');
    }


    /**
     * 自動で席を配置する
     */
    public function autoAssignSeats(SeatAssignmentService $service): void
    {
        // 前回の自動配置エラーを消す
        $this->autoAssignError = '';

        // 自動配置に必要な入力を先にチェック
        $this->validate(
            [
                'people' => [
                    'required',
                    'integer',
                    'min:1',
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
            ],

            [
                'people.required' => '人数を選択してください。',
                'people.integer' => '人数は数字で入力してください。',
                'people.min' => '人数は1人以上を選択してください。',

                'reservationDate.required' => '予約日を入力してください。',
                'reservationDate.date' => '正しい予約日を入力してください。',

                'startTime.required' => '開始時間を選択してください。',
                'startTime.date_format' => '開始時間は正しい形式で選択してください。',

                'endTime.required' => '終了時間を選択してください。',
                'endTime.date_format' => '終了時間は正しい形式で選択してください。',
                'endTime.after' => '終了時間は開始時間より後の時間を選択してください。',
            ]
        );

        // Cさんの自動配置処理を呼び出す
        $result = $service->assign(
            (int) $this->people,
            $this->reservationDate,
            $this->startTime,
            $this->endTime
        );

        // エラーの場合
        if ($result['error']) {
            $this->selectedSeatIds = [];
            $this->autoAssignError = $result['error'];

            return;
        }

        // 自動配置された席selectedSeatIds に入れる
        $this->selectedSeatIds = $result['selectedSeatIds'];

        // エラーメッセージを消す
        $this->autoAssignError = '';

        // 自動配置した場合は手動選択を解除
        // $this->seat = '';

        session()->flash(
            'message',
            '席を自動配置しました。'
        );
    }

    /**
     * 予約を登録する
     */
    public function save(ReservationService $service): void
    {
        // 入力チェック
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

                // 'seat' => [
                //     'required_without:selectedSeatIds',
                //     'nullable',
                //     'integer',
                //     'exists:seats,id',
                // ],
                'selectedSeatIds' => [
                    'required',
                    'array',
                    'min:1',
                ],

                'selectedSeatIds.*' => [
                    'integer',
                    'exists:seats,id',
                ],

                'description' => [
                    'nullable',
                    'string',
                ],
            ],
            [
                'customerName.required' => 'お客様名を入力してください。',
                'customerName.string' => 'お客様名は文字で入力してください。',
                'customerName.max' => 'お客様名は255文字以内で入力してください。',

                'people.required' => '人数を選択してください。',
                'people.integer' => '人数は数字で入力してください。',
                'people.min' => '人数は1人以上を選択してください。',

                'phone.max' => '電話番号は20文字以内で入力してください。',

                'reservationDate.required' => '予約日を入力してください。',
                'reservationDate.date' => '正しい予約日を入力してください。',

                'startTime.required' => '開始時間を選択してください。',
                'startTime.date_format' => '開始時間は正しい形式で選択してください。',

                'endTime.required' => '終了時間を選択してください。',
                'endTime.date_format' => '終了時間は正しい形式で選択してください。',
                'endTime.after' => '終了時間は開始時間より後の時間を選択してください。',

                'selectedSeatIds.required' => '席を1つ以上選択してください。',
                'selectedSeatIds.array' => '席の選択形式が正しくありません。',
                'selectedSeatIds.min' => '席を1つ以上選択してください。',
                'selectedSeatIds.*.exists' => '選択した席が存在しません。',

                'description.string' => 'メモの形式が正しくありません。',

            ]
        );

        // // 自動配置された席があれば、それを使用
        // if (!empty($this->selectedSeatIds)) {
        //     $seatIds = $this->selectedSeatIds;
        // } else {
        //     // 自動配置していなければ、手動選択した席を使用
        //     $seatIds = [(int) $this->seat,];
        // }


        /*
         * 手動選択でも自動配置でも
         * selectedSeatIds をそのまま使用
         */
        $seatIds = $this->selectedSeatIds;

        // ReservationServiceに渡すデータを作る
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
            $seatIds
        );

        // 登録完了メッセージ
        session()->flash(
            'message',
            '予約を登録しました。'
        );


        // // ④ 予約一覧へ戻る
        // $this->redirectRoute('reservations.index');

        // 他のLivewireコンポーネントへ通知
        $this->dispatch('reservation-created');


        // フォームを初期化
        $this->clear();

        // 予約一覧へ戻る
        $this->redirectRoute('reservations.index');

    }

    // /**
    //  * 手動で席を選択したら、自動配置の選択を解除する
    //  */
    // public function updatedSeat($value): void
    // {
    //     if ($value !== '') {
    //         $this->selectedSeatIds = [];
    //     }
    // }

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
        // $this->seat = '';
        $this->selectedSeatIds = [];
        $this->description = '';
        $this->autoAssignError = '';

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

        /*
         * 選択されている席を取得
         *
         * 手動選択でも自動配置でもselectedSeatIds に入っている席を表示する
         */
        $selectedSeats = [];

        if (!empty($this->selectedSeatIds)) {
            $selectedSeats = Seat::whereIn(
                'id',
                $this->selectedSeatIds
            )
                ->orderBy('display_order')
                ->get();

        }

        return view('livewire.reservation-create', [
            'seats' => $seats,
            'selectedSeats' => $selectedSeats,
            'shopSetting' => $shopSetting,
            'start' => $start,
            'end' => $end,
            'slotMinutes' => $slotMinutes,
        ]);
    }
}
