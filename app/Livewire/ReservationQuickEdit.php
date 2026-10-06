<?php

namespace App\Livewire;

use App\Models\Reservation;
use App\Models\Seat;
use App\Services\ReservationService;
use Livewire\Component;

class ReservationQuickEdit extends Component
{
    /*
    |--------------------------------------------------------------------------
    | 編集対象
    |--------------------------------------------------------------------------
    */
    public int $reservationId;


    /*
    |--------------------------------------------------------------------------
    | フォーム
    |--------------------------------------------------------------------------
    */
    public string $customerName = '';

    public string $people = '';

    public string $phone = '';

    public string $reservationDate = '';

    public string $startTime = '';

    public string $endTime = '';

    public string $status = '';

    public string $description = '';

    public array $selectedSeatIds = [];


    /*
    |--------------------------------------------------------------------------
    | 初期表示
    |--------------------------------------------------------------------------
    |
    | 予約状況から渡されたIDを使用して
    | DBから予約内容を取得します。
    |
    */
    public function mount(int $reservationId): void
    {
        $this->reservationId = $reservationId;

        $reservation = Reservation::with('seats')
            ->findOrFail($reservationId);


        // お客様名
        $this->customerName = $reservation->customer_name;

        // 人数
        $this->people = (string) $reservation->people;

        // 電話番号
        $this->phone = $reservation->phone ?? '';

        // 予約日
        $this->reservationDate =
            $reservation->reservation_date->format('Y-m-d');

        // 開始時間
        $this->startTime =
            substr($reservation->start_time, 0, 5);

        // 終了時間
        $this->endTime =
            substr($reservation->end_time, 0, 5);

        // 状態
        $this->status = $reservation->status;

        // 備考
        $this->description = $reservation->description ?? '';


        /*
        |--------------------------------------------------------------------------
        | 現在の席
        |--------------------------------------------------------------------------
        */
        $this->selectedSeatIds = $reservation->seats
            ->pluck('id')
            ->map(fn($id) => (string) $id)
            ->toArray();
    }


    /*
    |--------------------------------------------------------------------------
    | 更新
    |--------------------------------------------------------------------------
    */
    public function update(ReservationService $service): void
    {
        $this->validate([
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

            'status' => [
                'required',
                'in:temporary,reserved,cancelled',
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
                'string',
            ],
        ]);


        /*
        |--------------------------------------------------------------------------
        | 更新対象取得
        |--------------------------------------------------------------------------
        */
        $reservation = Reservation::findOrFail(
            $this->reservationId
        );


        /*
        |--------------------------------------------------------------------------
        | 既存ReservationServiceを再利用
        |--------------------------------------------------------------------------
        */
        $service->update(
            $reservation,

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

            $this->selectedSeatIds
        );


        /*
        |--------------------------------------------------------------------------
        | TOPへ更新を通知
        |--------------------------------------------------------------------------
        |
        | ページ遷移はしません。
        |
        */
        $this->dispatch('reservation-updated');


        session()->flash(
            'message',
            '予約を更新しました。'
        );
    }


    /*
    |--------------------------------------------------------------------------
    | 閉じる
    |--------------------------------------------------------------------------
    */
    public function close(): void
    {
        $this->dispatch('reservation-edit-closed');
    }


    /*
    |--------------------------------------------------------------------------
    | 表示
    |--------------------------------------------------------------------------
    */
    public function render()
    {
        $seats = Seat::query()
            ->where('is_active', true)
            ->orderBy('display_order')
            ->get();


        $totalCapacity = $seats->sum('capacity');


        return view(
            'livewire.reservation-quick-edit',
            [
                'seats' => $seats,
                'totalCapacity' => $totalCapacity,
            ]
        );
    }
}
