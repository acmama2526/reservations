<?php

namespace App\Livewire;

use App\Models\Reservation;
use App\Models\Seat;
use App\Services\ReservationService;
use App\Services\SeatAssignmentService;
use Livewire\Component;

class ReservationEdit extends Component
{
    public Reservation $reservation;

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

    // 状態
    public string $status = '';

    // 備考
    public string $description = '';

    // 手動選択・自動配置で選択された席
    public array $selectedSeatIds = [];

    // 自動配置のエラー
    public string $autoAssignError = '';


    /*
     * 初期表示
     */
    public function mount(Reservation $reservation): void
    {
        // 編集する予約を取得
        $this->reservation = $reservation->load('seats');

        // 予約情報を画面にセット
        $this->customerName = $this->reservation->customer_name;
        $this->people = (string) $this->reservation->people;
        $this->phone = $this->reservation->phone ?? '';
        $this->reservationDate = $this->reservation->reservation_date->format('Y-m-d');
        $this->startTime = substr($this->reservation->start_time, 0, 5);
        $this->endTime = substr($this->reservation->end_time, 0, 5);
        $this->status = $this->reservation->status;
        $this->description = $this->reservation->description ?? '';

        // 現在登録されている席を取得
        $this->selectedSeatIds = $this->reservation->seats()
            ->pluck('seats.id')
            ->map(fn($id) => (string) $id)
            ->toArray();
    }

    /*
     * 自動で席を配置する
     *※この処理は既存の手動席選択・更新処理とは分離する
     */
    public function autoAssignSeats(SeatAssignmentService $service): void
    {
        // 前回の自動配置エラーを消す
        $this->autoAssignError = '';

        // 自動配置に必要な入力をチェック
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

        // 編集画面なので、現在編集中の予約IDを渡す
        $result = $service->assign(
            (int) $this->people,
            $this->reservationDate,
            $this->startTime,
            $this->endTime,
            $this->reservation->id
        );

        // エラーの場合
        if ($result['error']) {
            $this->selectedSeatIds = [];
            $this->autoAssignError = $result['error'];

            return;
        }

        // 自動配置された席をセット
        $this->selectedSeatIds = array_map(
            'strval',
            $result['selectedSeatIds']
        );

        $this->autoAssignError = '';

        session()->flash(
            'message',
            '席を自動配置しました。'
        );

        // // 自動配置結果を保存
        // $this->selectedSeatIds = $result['selectedSeatIds'];

        // // 自動配置結果がある場合
        // if (!empty($this->selectedSeatIds)) {
        //     // 自動配置したことが分かるように
        //     // 手動選択欄は変更しない
        //     $this->autoAssignError = '';
        // }
    }

    /*
     * ReservationServiceに予約変更を依頼
     */
    public function update(ReservationService $service): void
    {

        $this->validate(
            [
                'customerName' => [
                    'required',
                    'string',
                    'max:255'
                ],

                'people' => [
                    'required',
                    'integer',
                    'min:1'
                ],

                'phone' => [
                    'nullable',
                    'regex:/^[0-9]{10,11}$/',
                ],

                'reservationDate' => [
                    'required',
                    'date'
                ],

                'startTime' => [
                    'required',
                    'date_format:H:i'
                ],

                'endTime' => [
                    'required',
                    'date_format:H:i',
                    'after:startTime'
                ],

                'status' => [
                    'required',
                    'in:temporary,reserved,visited,paid,cancelled',
                ],

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
                    'string'
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

                'status.required' => '状態を選択してください。',
                'status.in' => '状態の指定が正しくありません。',

                'selectedSeatIds.required' => '席を1つ以上選択してください。',
                'selectedSeatIds.array' => '席の選択形式が正しくありません。',
                'selectedSeatIds.min' => '席を1つ以上選択してください。',
                'selectedSeatIds.*.integer' => '選択した席の形式が正しくありません。',
                'selectedSeatIds.*.exists' => '選択した席が存在しません。',

                'description.string' => 'メモの形式が正しくありません。',
            ]
        );

        // 手動・自動どちらでも同じ配列を使用
        $seatIds = $this->selectedSeatIds;

        // ReservationServiceで更新
        $service->update(
            $this->reservation,
            [
                'customer_name' => $this->customerName,
                'people' => $this->people,
                'reservation_date' => $this->reservationDate,
                'start_time' => $this->startTime,
                'end_time' => $this->endTime,
                'phone' => $this->phone,
                'status' => $this->status,
                'description' => $this->description,
            ],
            $seatIds
        );

        // 完了メッセージ
        session()->flash('message', '予約を更新しました。');

        // 一覧画面へ戻る
        $this->redirectRoute('reservations.index');
    }


    /*
     * 編集中の内容を元の予約内容に戻す
     */
    public function clear(): void
    {
        // 現在の予約内容に戻す
        $this->mount($this->reservation);

        // バリデーションエラーを消す
        $this->resetValidation();

        // 自動配置エラーを消す
        $this->autoAssignError = '';
    }


    /*
     * 編集画面を表示
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

        // 選択されている席を取得
        $selectedSeats = [];

        if (!empty($this->selectedSeatIds)) {
            $selectedSeats = Seat::whereIn(
                'id',
                $this->selectedSeatIds
            )
                ->orderBy('display_order')
                ->get();
        }

        return view('livewire.reservation-edit', [
            'seats' => $seats,
            'selectedSeats' => $selectedSeats,
            'totalCapacity' => $totalCapacity,
        ]);
    }
}
