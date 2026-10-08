<?php

namespace App\Livewire;

use App\Models\Reservation;
use Livewire\Component;

class ReservationPrint extends Component
{
    // 検索条件
    public string $dateFrom = '';

    public string $dateTo = '';

    public string $customerName = '';

    public string $people = '';

    public string $status = '';

    public string $seat = '';

    /**
     * 印刷ページを開いたとき
     * URLの検索条件を受け取る
     */
    public function mount(): void
    {
        $this->dateFrom = request('dateFrom', '');
        $this->dateTo = request('dateTo', '');
        $this->customerName = request('customerName', '');
        $this->people = request('people', '');
        $this->status = request('status', '');
        $this->seat = request('seat', '');
    }

    /**
     * 画面表示
     */
    public function render()
    {
        $reservations = Reservation::query()
            ->with('seats')

            // 日付：開始
            ->when(
                $this->dateFrom,
                function ($query) {
                    $query->whereDate(
                        'reservation_date',
                        '>=',
                        $this->dateFrom
                    );
                }
            )

            // 日付：終了
            ->when(
                $this->dateTo,
                function ($query) {
                    $query->whereDate(
                        'reservation_date',
                        '<=',
                        $this->dateTo
                    );
                }
            )

            // 名前
            ->when(
                $this->customerName,
                function ($query) {
                    $query->where(
                        'customer_name',
                        'like',
                        '%' . $this->customerName . '%'
                    );
                }
            )

            // 人数
            ->when(
                $this->people,
                function ($query) {
                    $query->where(
                        'people',
                        $this->people
                    );
                }
            )

            // 状態
            ->when(
                $this->status,
                function ($query) {
                    $query->where(
                        'status',
                        $this->status
                    );
                }
            )

            // 席
            ->when(
                $this->seat,
                function ($query) {
                    $query->whereHas(
                        'seats',
                        function ($query) {
                            $query->where(
                                'seats.id',
                                $this->seat
                            );
                        }
                    );
                }
            )

            // 日付・開始時間順
            ->orderBy('reservation_date')
            ->orderBy('start_time')

            // 印刷なのでページネーションしない
            ->get();

        return view(
            'livewire.reservation-print',
            [
                'reservations' => $reservations,
            ]
        );
    }
}