<?php

namespace App\Livewire;

use App\Models\Seat;
use App\Models\ShopSetting;
use App\Services\ReservationService;
use App\Services\SeatAssignmentService;
use Carbon\Carbon;
use Livewire\Component;
use Livewire\Attributes\On;

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

    // 選択可能な席のID
    public array $availableSeatIds = [];

    // 空席状況を確認済みかどうか
    public bool $availabilityChecked = false;

    // 空席状況のメッセージ
    public string $seatAvailabilityMessage = '';


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

        // 自動配置された席を selectedSeatIds に入れる
        $this->selectedSeatIds = $result['selectedSeatIds'];

        // 空席情報も更新する
        $this->availableSeatIds = $result['availableSeatIds'];
        $this->availabilityChecked = true;
        $this->seatAvailabilityMessage = '';

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
     * 予約日時に応じて空席を更新する
     */
    public function updateAvailableSeats(
        SeatAssignmentService $service
    ): void {
        // 日付・開始時間・終了時間が揃っていなければ、
        // 空席状況の確認はまだ行わない
        if (
            $this->reservationDate === '' ||
            $this->startTime === '' ||
            $this->endTime === '' ||
            $this->endTime <= $this->startTime
        ) {
            $this->availabilityChecked = false;
            $this->availableSeatIds = [];
            $this->seatAvailabilityMessage = '';

            return;
        }

        $result = $service->searchAvailableSeatIds(
            $this->reservationDate,
            $this->startTime,
            $this->endTime
        );

        // 営業時間外などで確認できない場合
        if ($result['error'] !== null) {
            $this->availabilityChecked = false;
            $this->availableSeatIds = [];
            $this->seatAvailabilityMessage = $result['error'];

            return;
        }

        // 空いている席のIDを保存
        $this->availableSeatIds = $result['availableSeatIds'];
        $this->availabilityChecked = true;
        $this->seatAvailabilityMessage = '';

        // 空席状況の更新後、選択中の席が予約済みなら選択を解除
        $unavailableSelectedIds = array_diff(
            $this->selectedSeatIds,
            $this->availableSeatIds
        );

        if (! empty($unavailableSelectedIds)) {
            $this->selectedSeatIds = array_values(
                array_diff(
                    $this->selectedSeatIds,
                    $unavailableSelectedIds
                )
            );

            $this->seatAvailabilityMessage =
                '予約済みになった席の選択を解除しました。空いている席を選び直してください。';
        }
    }

    /**
     * 予約日・開始時間・終了時間が変更されたら空席を更新する
     */
    public function updatedReservationDate(
        SeatAssignmentService $service
    ): void {
        $this->updateAvailableSeats($service);
    }

    public function updatedStartTime(
        SeatAssignmentService $service
    ): void {
        $this->updateAvailableSeats($service);
    }

    public function updatedEndTime(
        SeatAssignmentService $service
    ): void {
        $this->updateAvailableSeats($service);
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
                    'regex:/^[0-9]{10,11}$/',
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

                'phone.regex' => '電話番号は半角数字10～11桁で入力してください。',

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
        // 空席情報も初期化する
        $this->availableSeatIds = [];
        $this->availabilityChecked = false;
        $this->seatAvailabilityMessage = '';

        // エラーメッセージも消す
        $this->resetValidation();
    }

    // :笹渕追記: 予約状況から空き枠を受け取る (selectReservationSlot)
    // TOP画面から予約枠の情報を受け取る
    #[On('reservation-slot-selected')]
    public function selectReservationSlot(
        string $date,
        int $seatId,
        string $time
    ): void {
        // 予約日
        $this->reservationDate = $date;

        // 開始時間
        $this->startTime = $time;

        // TOPでクリックした席を初期選択する
        $this->selectedSeatIds = [$seatId];
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

        // 全席の収容人数を合計
        $totalCapacity = $seats->sum('capacity');

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
            'totalCapacity' => $totalCapacity,
            'availableSeatIds' => $this->availableSeatIds,
            'availabilityChecked' => $this->availabilityChecked,
            'seatAvailabilityMessage' => $this->seatAvailabilityMessage,
        ]);
    }
}
