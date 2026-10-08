<?php

namespace App\Livewire;

use App\Models\Reservation;
use App\Models\Seat;
use App\Models\ShopSetting;
use App\Services\ReservationService;
use App\Services\SeatAssignmentService;
use Carbon\Carbon;
use Livewire\Attributes\On;
use Livewire\Component;

class ReservationTop extends Component
{
    public string $selectedDate = '';
    public bool $showCreateModal = false;
    public bool $showEditModal = false;
    public ?int $editingReservationId = null;
    public string $customerName = '';
    public string $people = '';
    public string $phone = '';
    public string $reservationDate = '';
    public string $startTime = '';
    public string $endTime = '';
    public string $status = 'reserved';
    public string $description = '';
    public array $selectedSeatIds = [];
    public int $seatSelectionResetKey = 0;
    public string $autoAssignError = '';

    public function mount(): void
    {
        $this->selectedDate = today()->format('Y-m-d');

        $this->reservationDate = $this->selectedDate;
    }

    #[On('reservation-date-changed')]
    public function changeReservationDate(string $date): void
    {
        $this->selectedDate = $date;

        $this->closeModal();
    }

    #[On('reservation-updated')]
    public function refreshTopReservations(): void
    {
        // このイベント受信で予約一覧を再描画します。
    }

    public function openCreateModalFromButton(): void
    {
        [$businessStart, $businessEnd] = $this->businessHours();

        $seats = Seat::query()->where('is_active', true)->orderBy('display_order')->get();

        $reservations = Reservation::with('seats:id')->whereDate('reservation_date', $this->selectedDate)->where('status', '!=', 'cancelled')->get();

        for (
            $slotStart = $businessStart->copy();
            $slotStart->lt($businessEnd);
            $slotStart->addMinutes(15)
        ) {
            $slotEnd = $slotStart->copy()->addMinutes(15);

            if ($slotEnd->gt($businessEnd)) {
                break;
            }

            $startTime = $slotStart->format('H:i');
            $endTime = $slotEnd->format('H:i');

            foreach ($seats as $seat) {
                $isOccupied = $reservations->contains(
                    fn ($reservation) => substr($reservation->start_time, 0, 5) < $endTime
                    && substr($reservation->end_time, 0, 5) > $startTime
                    && $reservation->seats->contains('id', $seat->id)
                );

                if (!$isOccupied) {
                    $this->openCreateModal($this->selectedDate, (int) $seat->id, $startTime);

                    return;
                }
            }
        }

        $this->resetForm();
        $this->reservationDate = $this->selectedDate;
        $this->startTime = $businessStart->format('H:i');
        $defaultEnd = $businessStart->copy()->addMinutes(15);
        if ($defaultEnd->gt($businessEnd)) {
            $defaultEnd = $businessEnd->copy();
        }
        $this->endTime = $defaultEnd->format('H:i');
        $this->showCreateModal = true;
        $this->showEditModal = false;

        $this->autoAssignError = '選択日の営業時間内に空き枠がありません。時間を変更してください。';
    }

    #[On('reservation-slot-selected')]
    public function openCreateModal(string $date, int $seatId, string $time): void
    {
        // 新規予約は、直前の席選択を引き継がず全チェックを外して開きます。
        // seatIdは空き枠イベントの引数として受け取り、席の初期選択には使いません。
        $this->resetForm();

        $this->reservationDate = $date;

        $this->startTime = $time;

        $end = Carbon::createFromFormat('H:i', $time)->addMinutes(15);

        [, $businessEnd] = $this->businessHours();

        if ($end->gt($businessEnd)) {
            $end = $businessEnd->copy();
        }

        $this->endTime = $end->format('H:i');

        $this->showCreateModal = true;

        $this->showEditModal = false;
    }

    #[On('reservation-selected')]
    public function openEditModal(int $reservationId): void
    {
        $this->resetForm();

        $reservation = Reservation::with('seats')->findOrFail($reservationId);

        $this->editingReservationId = $reservation->id;

        $this->customerName = $reservation->customer_name;

        $this->people = (string) $reservation->people;

        $this->phone = $reservation->phone ?? '';

        $this->reservationDate = $reservation->reservation_date->format('Y-m-d');

        $this->startTime = substr($reservation->start_time, 0, 5);

        $this->endTime = substr($reservation->end_time, 0, 5);

        $this->status = $reservation->status;

        $this->description = $reservation->description ?? '';

        $this->selectedSeatIds = $reservation->seats->pluck('id')->map(fn ($id) => (string) $id)->toArray();

        $this->showEditModal = true;

        $this->showCreateModal = false;
    }

    public function deleteReservation(int $reservationId): void
    {
        $reservation = Reservation::findOrFail($reservationId);

        // 予約と席の関連を先に解除してから予約本体を削除します。
        $reservation->seats()->detach();

        $reservation->delete();

        if ($this->editingReservationId === $reservationId) {
            $this->closeModal();
        }

        $this->dispatch('reservation-updated');
    }

    public function autoAssignSeats(SeatAssignmentService $seatAssignment): void
    {
        $this->autoAssignError = '';

        if ((int) $this->people < 1) {
            $this->addError('people', '人数を選択してください。');
            return;
        }

        if (!$this->reservationDate || !$this->startTime || !$this->endTime) {
            $this->autoAssignError = '予約日、開始時間、終了時間を選択してください。';
            return;
        }

        $assignment = $seatAssignment->assign((int) $this->people, $this->reservationDate, $this->startTime, $this->endTime, $this->editingReservationId);

        if ($assignment['error']) {
            $this->selectedSeatIds = [];
            $this->autoAssignError = $assignment['error'];
            return;
        }

        $this->selectedSeatIds = array_map('strval', $assignment['selectedSeatIds']);

        $this->autoAssignError = '';
        $this->resetErrorBag('selectedSeatIds');
    }

    public function createReservation(ReservationService $service): void
    {
        $this->validateForm();

        $selectedCapacity = Seat::query()->where('is_active', true)->whereIn('id', array_map('intval', $this->selectedSeatIds))->sum('capacity');

        if ($selectedCapacity < (int) $this->people) {
            $this->addError('selectedSeatIds', '選択した席の定員合計が予約人数に足りません。');
            return;
        }

        $selectedSeatIds = array_map('intval', $this->selectedSeatIds);
        $hasConflict = Reservation::query()->whereDate('reservation_date', $this->reservationDate)->where('status', '!=', 'cancelled')->where('start_time', '<', $this->endTime)->where('end_time', '>', $this->startTime)->whereHas('seats', function ($query) use ($selectedSeatIds)
            {
                $query->whereIn('seats.id', $selectedSeatIds);
        })->exists();

        if ($hasConflict) {
            $this->addError('selectedSeatIds', '選択した席はこの時間帯に使用中です。空いている席へ自動配置し直してください。');
            return;
        }

        $service->create(
            [
                'customer_name' => $this->customerName,

                'people' => (int) $this->people,

                'phone' => $this->phone,

                'reservation_date' => $this->reservationDate,

                'start_time' => $this->startTime,

                'end_time' => $this->endTime,

                'status' => 'reserved',

                'description' => $this->description,
            ],

            array_map('intval', $this->selectedSeatIds)
        );

        $this->selectedDate = $this->reservationDate;

        $this->closeModal();

        $this->dispatch('reservation-updated');
    }

    public function updateReservation(ReservationService $service): void
    {
        $this->validateForm();

        $reservation = Reservation::findOrFail($this->editingReservationId);

        $selectedSeatIds = array_map('intval', $this->selectedSeatIds);
        $selectedCapacity = Seat::query()->where('is_active', true)->whereIn('id', $selectedSeatIds)->sum('capacity');

        if ($selectedCapacity < (int) $this->people) {
            $this->addError('selectedSeatIds', '選択した席の定員合計が予約人数に足りません。');
            return;
        }

        $hasConflict = Reservation::query()->where('id', '!=', $reservation->id)->whereDate('reservation_date', $this->reservationDate)->where('status', '!=', 'cancelled')->where('start_time', '<', $this->endTime)->where('end_time', '>', $this->startTime)->whereHas('seats', function ($query) use ($selectedSeatIds)
            {
                $query->whereIn('seats.id', $selectedSeatIds);
        })->exists();

        if ($hasConflict) {
            $this->addError('selectedSeatIds', '選択した席はこの時間帯に使用中です。空いている席へ自動配置し直してください。');
            return;
        }

        $service->update(
            $reservation,

            [
                'customer_name' => $this->customerName,

                'people' => (int) $this->people,

                'phone' => $this->phone,

                'reservation_date' => $this->reservationDate,

                'start_time' => $this->startTime,

                'end_time' => $this->endTime,

                'status' => $this->status,

                'description' => $this->description,
            ],

            array_map('intval', $this->selectedSeatIds)
        );

        $this->closeModal();

        $this->dispatch('reservation-updated');
    }

    private function validateForm(): void
    {
        $this->validate(
            [
                'customerName' => ['required', 'string', 'max:255',],

                'people' => [
                    'required',
                    'integer',
                    'min:1',
                    'max:' . max(1, (int) Seat::query()->where('is_active', true)->sum('capacity')),
                ],

                'phone' => ['nullable', 'regex:/^[0-9]{10,11}$/',],

                'reservationDate' => ['required', 'date',],

                'startTime' => ['required', 'date_format:H:i',],

                'endTime' => ['required', 'date_format:H:i', 'after:startTime',],

                'selectedSeatIds' => ['required', 'array', 'min:1',],

                'selectedSeatIds.*' => ['integer', 'exists:seats,id',],

                'status' => ['required', 'in:temporary,reserved,visited,paid,cancelled',],

                'description' => ['nullable', 'string',],
            ],

            [
                'customerName.required' => 'お客様名を入力してください。',

                'people.required' => '人数を入力してください。',

                'people.integer' => '人数は数字で入力してください。',

                'phone.regex' => '電話番号は半角数字10～11桁で入力してください。',

                'people.min' => '人数は1人以上にしてください。',

                'reservationDate.required' => '予約日を選択してください。',

                'startTime.required' => '開始時間を選択してください。',

                'endTime.required' => '終了時間を選択してください。',

                'endTime.after' => '終了時間は開始時間より後にしてください。',

                'selectedSeatIds.required' => '席を選択するか、自動配置を実行してください。',

                'selectedSeatIds.min' => '席を1つ以上選択するか、自動配置を実行してください。',
            ]
        );
    }

    public function closeModal(): void
    {
        $this->showCreateModal = false;

        $this->showEditModal = false;

        $this->resetForm();
    }

    private function resetForm(): void
    {
        $this->seatSelectionResetKey++;

        $this->customerName = '';

        $this->people = '';

        $this->phone = '';

        $this->reservationDate = $this->selectedDate;

        $this->startTime = '';

        $this->endTime = '';

        $this->status = 'reserved';

        $this->description = '';

        $this->selectedSeatIds = [];

        $this->autoAssignError = '';

        $this->editingReservationId = null;

        $this->resetValidation();
    }

    private function businessHours(): array
    {
        $setting = ShopSetting::first();

        if ($setting) {
            return [Carbon::parse($setting->business_start), Carbon::parse($setting->business_end),];
        }

        return [Carbon::createFromTime(17, 0), Carbon::createFromTime(22, 0),];
    }

    private function makeModalTimes(): array
    {
        [$businessStart, $businessEnd] = $this->businessHours();

        $startTimeOptions = [];

        $current = $businessStart->copy();

        while ($current->lt($businessEnd)) {
            $startTimeOptions[] = $current->format('H:i');

            $current->addMinutes(15);
        }

        $endTimeOptions = [];

        $current = $businessStart->copy()->addMinutes(15);

        while ($current->lte($businessEnd)) {
            $endTimeOptions[] = $current->format('H:i');

            $current->addMinutes(15);
        }

        return [$startTimeOptions, $endTimeOptions,];
    }

    public function render()
    {
        $reservations = Reservation::with('seats')->whereDate('reservation_date', $this->selectedDate)->where('status', '!=', 'cancelled')->orderBy('start_time')->get();

        $seats = Seat::query()->where('is_active', true)->orderBy('display_order')->get();

        [$startTimeOptions, $endTimeOptions] = $this->makeModalTimes();

        return view(
            'livewire.reservation-top',

            [
                'todayReservations' => $reservations,

                'seats' => $seats,

                'totalCapacity' => (int) $seats->sum('capacity'),

                'selectedSeats' => Seat::query()->whereIn('id', array_map('intval', $this->selectedSeatIds))->get(),

                'startTimeOptions' => $startTimeOptions,

                'endTimeOptions' => $endTimeOptions,
            ]
        )->layout('components.layout');
    }
}
