<?php

namespace App\Livewire;

use App\Models\Reservation;
use App\Models\Seat;
use App\Models\ShopSetting;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Livewire\Attributes\On;
use Livewire\Attributes\Modelable;
use Livewire\Component;

class ReservationStatus extends Component
{
    #[Modelable]
    public string $date = '';
    public int $displayMinutes = 30;
    public array $times = [];

    public function mount(): void
    {
        if ($this->date === '') {
            $this->date = today()->format('Y-m-d');
        }

        $this->makeTimes();
    }

    public function previousDay(): void
    {
        $this->date = Carbon::parse($this->date)->subDay()->format('Y-m-d');

        $this->dateChanged();
    }

    public function nextDay(): void
    {
        $this->date = Carbon::parse($this->date)->addDay()->format('Y-m-d');

        $this->dateChanged();
    }

    public function updatedDate(): void
    {
        $this->dateChanged();
    }

    private function dateChanged(): void
    {
        $this->dispatch('reservation-date-changed', date: $this->date);
    }

    public function updatedDisplayMinutes($value): void
    {
        $value = (int) $value;

        if (!in_array($value, [15, 30, 60,], true)) {
            $value = 30;
        }

        $this->displayMinutes = $value;

        $this->makeTimes();
    }

    private function makeTimes(): void
    {
        [$start, $end] = $this->businessHours();

        $this->times = [];

        $current = $start->copy();

        while ($current->lte($end)) {
            $this->times[] = $current->format('H:i');

            $current->addMinutes($this->displayMinutes);
        }

        $endString = $end->format('H:i');

        if (end($this->times) !== $endString) {
            $this->times[] = $endString;
        }
    }

    private function businessHours(): array
    {
        $setting = ShopSetting::first();

        if ($setting) {
            return [
                Carbon::createFromFormat('H:i:s', $setting->business_start),

                Carbon::createFromFormat('H:i:s', $setting->business_end),
            ];
        }

        return [Carbon::createFromTime(17, 0), Carbon::createFromTime(22, 0),];
    }

    public function selectReservation(int $reservationId): void
    {
        $this->dispatch('reservation-selected', reservationId: $reservationId);
    }

    public function selectEmptySlot(int $seatId, string $time): void
    {
        $this->dispatch('reservation-slot-selected', date: $this->date, seatId: $seatId, time: $time);
    }

    public function moveReservation(
        int $reservationId,
        int $seatId,
        string $startTime,
        string $endTime,
        string $mode = 'move',
        int $sourceSeatId = 0
    ): void {
        $reservation = Reservation::with('seats')->findOrFail($reservationId);

        try {
            $newStart = Carbon::createFromFormat('H:i', $startTime);

            $newEnd = Carbon::createFromFormat('H:i', $endTime);
        } catch (\Throwable $e) {
            $this->dispatch('reservation-drag-failed', message: '時間が正しくありません。');

            return;
        }

        if ($newEnd->lte($newStart)) {
            $this->dispatch('reservation-drag-failed', message: '終了時間は開始時間より後にしてください。');

            return;
        }

        [$businessStart, $businessEnd] = $this->businessHours();

        if ($newStart->lt($businessStart) || $newEnd->gt($businessEnd)) {
            $this->dispatch('reservation-drag-failed', message: '営業時間外には移動できません。');

            return;
        }

        $targetSeat = Seat::query()->where('id', $seatId)->where('is_active', true)->first();

        if (!$targetSeat) {
            $this->dispatch('reservation-drag-failed', message: '移動先の席が存在しません。');

            return;
        }

        if (
            $mode === 'move'
            && $sourceSeatId !== $seatId
            && (int) $reservation->people > (int) $targetSeat->capacity
        ) {
            $this->dispatch('reservation-drag-failed', message: "移動先の席「{$targetSeat->seat_name}」は{$targetSeat->capacity}名までです。");

            return;
        }

        $seatIds = $reservation->seats->pluck('id')->map(fn ($id) => (int) $id)->toArray();

        if ($mode === 'move') {
            if (count($seatIds) <= 1) {
                $seatIds = [$seatId];
            } elseif ($sourceSeatId > 0 && in_array($sourceSeatId, $seatIds, true)) {
                $seatIds = array_values(array_unique(array_map(static fn (int $id): int => $id === $sourceSeatId ? $seatId : $id, $seatIds)));
            } else {
                $this->dispatch('reservation-drag-failed', message: '移動元の席を確認できません。');
                return;
            }
        }

        $conflict = Reservation::query()->where('id', '!=', $reservation->id)->whereDate('reservation_date', $this->date)->where('status', '!=', 'cancelled')->whereHas(
            'seats',

            function ($query) use ($seatIds)
            {
                $query->whereIn('seats.id', $seatIds);
            }
        )->where('start_time', '<', $endTime)->where('end_time', '>', $startTime)->exists();

        if ($conflict) {
            $this->dispatch('reservation-drag-failed', message: 'その時間帯には既に予約があります。');

            return;
        }

        DB::transaction(
            function () use ($reservation, $startTime, $endTime, $seatIds)
            {
                $reservation->update([ 'start_time' => $startTime, 'end_time' => $endTime, ]);

                $reservation->seats()->sync($seatIds);
            }
        );

        $this->dispatch('reservation-updated');
    }

    #[On('reservation-updated')]
    public function refreshReservations(): void
    {
        // このイベント受信で予約グリッドを再描画します。
    }

    public function render()
    {
        [$businessStart, $businessEnd] = $this->businessHours();

        $businessMinutes = $businessStart->diffInMinutes($businessEnd);

        $seatModels = Seat::query()->where('is_active', true)->orderBy('display_order')->get();

        $reservationModels = Reservation::with('seats')->whereDate('reservation_date', $this->date)->where('status', '!=', 'cancelled')->orderBy('start_time')->get();

        $reservations = [];

        foreach ($reservationModels as $reservation) {
            $startTime = substr($reservation->start_time, 0, 5);

            $endTime = substr($reservation->end_time, 0, 5);

            $start = Carbon::createFromFormat('H:i', $startTime);

            $end = Carbon::createFromFormat('H:i', $endTime);

            $startMinutes = $businessStart->diffInMinutes($start, false);

            $durationMinutes = $start->diffInMinutes($end);

            $left = ($startMinutes / $businessMinutes)
            *
            100;

            $width = ($durationMinutes / $businessMinutes)
            *
            100;

            foreach ($reservation->seats as $seat) {
                $reservations[] = [
                    'id' => $reservation->id,

                    'seat_id' => $seat->id,

                    'name' => $reservation->customer_name,

                    'people' => $reservation->people,

                    'status' => $reservation->status,

                    'start_time' => $startTime,

                    'end_time' => $endTime,

                    'left' => max(0, $left),

                    'width' => max(1, $width),
                ];
            }
        }

        $setting = ShopSetting::first();

        $slotMinutes = $setting
        ? (int) $setting->slot_minutes
        : 15;

        return view(
            'livewire.reservation-status',

            [
                'seats' => $seatModels,

                'reservations' => $reservations,

                'businessStart' => $businessStart->format('H:i'),

                'businessEnd' => $businessEnd->format('H:i'),

                'businessMinutes' => $businessMinutes,

                'slotMinutes' => $slotMinutes,
            ]
        );
    }
}
