<?php

namespace App\Livewire;

use App\Models\Reservation;
use App\Models\Seat;
use Livewire\Component;
use Livewire\WithPagination;

class ReservationList extends Component
{
    use WithPagination;

    // 検索条件
    public string $dateFrom = '';

    public string $dateTo = '';

    public string $customerName = '';

    public string $people = '';

    public string $status = '';

    public string $seat = '';

    // 1ページあたりの表示件数
    public int $perPage = 10;

    /**
     * 初期表示
     */
    public function mount(): void
    {
        $this->dateFrom = now()->format('Y-m-d');
        $this->dateTo = now()->format('Y-m-d');
    }

    /**
     * 検索
     */
    public function search(): void
    {
        $this->resetPage();
    }

    /**
     * 検索条件をクリア
     */
    public function clearSearch(): void
    {
        $this->dateFrom = '';
        $this->dateTo = '';
        $this->customerName = '';
        $this->people = '';
        $this->status = '';
        $this->seat = '';

        $this->resetPage();
    }

    /**
     * 表示件数変更
     */
    public function updatedPerPage(): void
    {
        $this->resetPage();
    }

    /**
     * 予約を削除
     */
    public function deleteReservation(int $id): void
    {
        $reservation = Reservation::findOrFail($id);

        $reservation->delete();

        session()->flash(
            'message',
            '予約を削除しました。'
        );
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

            ->orderBy('reservation_date')
            ->orderBy('start_time')
            ->paginate($this->perPage);

        $seats = Seat::query()
            ->where('is_active', true)
            ->orderBy('display_order')
            ->get();

        // 全席の収容人数を合計
        $totalCapacity = $seats->sum('capacity');

        return view(
            'livewire.reservation-list',
            [
                'reservations' => $reservations,
                'seats' => $seats,
                'totalCapacity' => $totalCapacity,
            ]
        );
    }
}
