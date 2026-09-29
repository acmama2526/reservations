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

    public string $customerName = '';
    public string $people = '';
    public string $phone = '';
    public string $reservationDate = '';
    public string $startTime = '';
    public string $endTime = '';
    public string $seat = '';
    public array $selectedSeatIds = [];
    public string $status = '';
    public string $description = '';


    /*
    *編集画面を開いたときの初期値
    */
    public function mount(Reservation $reservation): void
    {
        // 編集する予約を取得
        $this->reservation = $reservation->load('seats');

        // 予約情報を画面にセット
        $this->customerName = $reservation->customer_name;
        $this->people = (string) $reservation->people;
        $this->phone = $reservation->phone ?? '';
        $this->reservationDate = $reservation->reservation_date->format('Y-m-d');
        $this->startTime = substr($reservation->start_time, 0, 5);
        $this->endTime = substr($reservation->end_time, 0, 5);
        $this->status = $reservation->status;
        $this->description = $reservation->description ?? '';

        // 現在設定されている席
        $this->seat = (string) ($reservation->seats->first()?->id ?? '');

        $this->selectedSeatIds = $reservation->seats
            ->pluck('id')
            ->map(fn($id) => (int) $id)
            ->all();
    }

    /*
    *ReservationServiceに予約変更を依頼
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
                    'string',
                    'max:20'
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

                'seat' => [
                    'nullable',
                    'integer',
                    'exists:seats,id'
                ],

                'status' => [
                    'required',
                    'in:temporary,reserved,cancelled'
                ],

                'description' => [
                    'nullable',
                    'string'
                ],

            ],

            [
                'endTime.after' => '終了時間は開始時間より後の時間を選択してください。',
            ]
        );


        // 自動配置された席があれば、それを使用
        // なければ手動で選択した席を使用
        $seatIds = !empty($this->selectedSeatIds)
            ? $this->selectedSeatIds
            : [(int) $this->seat];

        // 予約情報を更新
        $this->reservation = $service->update(
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

        session()->flash('message', '予約を更新しました。');

        $this->redirectRoute(
            'reservations.show',
            $this->reservation
        );
    }

    /*
    *自動配置の処理
    */
    public function autoAssignSeats(SeatAssignmentService $service): void
    {
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
                'endTime.after' => '終了時間は開始時間より後の時間を選択してください。',
            ]
        );

        // 自動配置を実行
        $result = $service->assign(
            (int) $this->people,
            $this->reservationDate,
            $this->startTime,
            $this->endTime,
            $this->reservation->id
        );

        // 自動配置できなかった場合
        if ($result['error']) {
            $this->selectedSeatIds = [];

            session()->flash('error', $result['error']);

            return;
        }

        // 自動配置された席をセット
        $this->selectedSeatIds = $result['selectedSeatIds'];

        // 手動選択の席を解除
        $this->seat = '';

        session()->flash('message', '席を自動配置しました。');
    }


    /*
    *編集中の内容を元の予約内容に戻す
    */
    public function clear(): void
    {
        // 編集画面では「クリア」ではなく、
        // 現在の予約内容に戻す
        $this->mount($this->reservation);

        $this->resetValidation();
    }

    /*
    *編集画面を表示
    */
    public function render()
    {
        $seats = Seat::query()
            ->where('is_active', true)
            ->orderBy('display_order')
            ->get();

        return view('livewire.reservation-edit', [
            'seats' => $seats,
        ]);
    }
}
