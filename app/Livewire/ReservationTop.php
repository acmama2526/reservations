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
    /*
    |--------------------------------------------------------------------------
    | TOPで現在表示している日付
    |--------------------------------------------------------------------------
    */
    public string $selectedDate = '';


    /*
    |--------------------------------------------------------------------------
    | モーダル表示状態
    |--------------------------------------------------------------------------
    */
    public bool $showCreateModal = false;

    public bool $showEditModal = false;


    /*
    |--------------------------------------------------------------------------
    | 編集対象予約ID
    |--------------------------------------------------------------------------
    */
    public ?int $editingReservationId = null;


    /*
    |--------------------------------------------------------------------------
    | 予約フォーム
    |--------------------------------------------------------------------------
    */
    public string $customerName = '';

    public string $people = '';

    public string $phone = '';

    public string $reservationDate = '';

    public string $startTime = '';

    public string $endTime = '';

    public string $status = 'reserved';

    public string $description = '';

    /*
     * 複数席予約へ対応するため、
     * 席IDは配列で管理します。
     */
    public array $selectedSeatIds = [];

    public string $autoAssignError = '';


    /*
    |--------------------------------------------------------------------------
    | 初期表示
    |--------------------------------------------------------------------------
    */
    public function mount(): void
    {
        $this->selectedDate =
            today()->format('Y-m-d');

        $this->reservationDate =
            $this->selectedDate;
    }


    /*
    |--------------------------------------------------------------------------
    | 予約状況の日付変更を受け取る
    |--------------------------------------------------------------------------
    */
    #[On('reservation-date-changed')]
    public function changeReservationDate(
        string $date
    ): void {
        $this->selectedDate = $date;

        /*
         * 日付変更時はモーダルを閉じます。
         */
        $this->closeModal();
    }


    /*
    |--------------------------------------------------------------------------
    | 予約データ更新
    |--------------------------------------------------------------------------
    |
    | ReservationStatusで
    |
    | ・ドラッグ
    | ・リサイズ
    |
    | が完了した場合に呼ばれます。
    |
    | このメソッドが実行されることで
    | ReservationTopも再描画され、
    | 右側の予約一覧が最新状態になります。
    |
    */
    #[On('reservation-updated')]
    public function refreshTopReservations(): void
    {
        /*
         * 処理不要。
         *
         * Livewireがこのメソッドを実行するだけで
         * render()が再実行されます。
         */
    }


    /*
    |--------------------------------------------------------------------------
    | 「＋ 新規予約」ボタン
    |--------------------------------------------------------------------------
    */
    public function openCreateModalFromButton(): void
    {
        [$businessStart, $businessEnd] =
            $this->businessHours();

        $seats =
            Seat::query()
                ->where('is_active', true)
                ->orderBy('display_order')
                ->get();

        $reservations =
            Reservation::with('seats:id')
                ->whereDate(
                    'reservation_date',
                    $this->selectedDate
                )
                ->where('status', '!=', 'cancelled')
                ->get();

        /*
         * ＋ボタンでも空き枠クリックと同じ初期化処理を使い、
         * 選択日の最初の空き15分枠を初期値にします。
         */
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
                $isOccupied =
                    $reservations->contains(
                        fn ($reservation) =>
                            substr($reservation->start_time, 0, 5) < $endTime
                            && substr($reservation->end_time, 0, 5) > $startTime
                            && $reservation->seats->contains('id', $seat->id)
                    );

                if (!$isOccupied) {
                    $this->openCreateModal(
                        $this->selectedDate,
                        (int) $seat->id,
                        $startTime
                    );

                    return;
                }
            }
        }

        /*
         * 空き枠がない日でもモーダルは開き、
         * 利用者が時間を変更できるようにします。
        */
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

        $this->autoAssignError =
            '選択日の営業時間内に空き枠がありません。時間を変更してください。';
    }


    /*
    |--------------------------------------------------------------------------
    | 予約グリッドの空き部分クリック
    |--------------------------------------------------------------------------
    */
    #[On('reservation-slot-selected')]
    public function openCreateModal(
        string $date,
        int $seatId,
        string $time
    ): void {
        /*
         * フォーム初期化
         */
        $this->resetForm();


        /*
         * クリックした情報をセット
         */
        $this->reservationDate = $date;

        $this->startTime = $time;

        $this->selectedSeatIds = [
            (string) $seatId,
        ];


        /*
         * 新規予約の初期終了時間は
         * 開始時間 + 15分とします。
         *
         * Gridの表示単位が30分や60分でも
         * 新規予約自体は15分単位で登録できます。
         */
        $end =
            Carbon::createFromFormat(
                'H:i',
                $time
            )
                ->addMinutes(15);


        /*
         * 営業時間取得
         */
        [
            ,
            $businessEnd
        ] = $this->businessHours();


        /*
         * 営業終了時間を超えないようにします。
         */
        if ($end->gt($businessEnd)) {
            $end = $businessEnd->copy();
        }


        $this->endTime =
            $end->format('H:i');


        /*
         * 新規予約モーダル表示
         */
        $this->showCreateModal = true;

        $this->showEditModal = false;
    }


    /*
    |--------------------------------------------------------------------------
    | 既存予約クリック
    |--------------------------------------------------------------------------
    */
    #[On('reservation-selected')]
    public function openEditModal(
        int $reservationId
    ): void {
        $this->resetForm();

        $reservation =
            Reservation::with('seats')
                ->findOrFail(
                    $reservationId
                );


        /*
         * 編集対象ID
         */
        $this->editingReservationId =
            $reservation->id;


        /*
         * 基本情報
         */
        $this->customerName =
            $reservation->customer_name;

        $this->people =
            (string) $reservation->people;

        $this->phone =
            $reservation->phone ?? '';

        $this->reservationDate =
            $reservation
                ->reservation_date
                ->format('Y-m-d');

        $this->startTime =
            substr(
                $reservation->start_time,
                0,
                5
            );

        $this->endTime =
            substr(
                $reservation->end_time,
                0,
                5
            );

        $this->status =
            $reservation->status;

        $this->description =
            $reservation->description ?? '';


        /*
         * 現在予約されている席
         */
        $this->selectedSeatIds =
            $reservation
                ->seats
                ->pluck('id')
                ->map(
                    fn ($id) =>
                        (string) $id
                )
                ->toArray();


        /*
         * 編集モーダル表示
         */
        $this->showEditModal = true;

        $this->showCreateModal = false;
    }


    /*
    |--------------------------------------------------------------------------
    | 当日予約一覧から予約を削除
    |--------------------------------------------------------------------------
    */
    public function deleteReservation(
        int $reservationId
    ): void {
        $reservation =
            Reservation::findOrFail(
                $reservationId
            );


        // 予約と席の関連を先に解除してから予約本体を削除します。
        $reservation
            ->seats()
            ->detach();

        $reservation->delete();


        if ($this->editingReservationId === $reservationId) {
            $this->closeModal();
        }


        $this->dispatch(
            'reservation-updated'
        );
    }


    /*
    |--------------------------------------------------------------------------
    | 新規予約登録
    |--------------------------------------------------------------------------
    */
    public function autoAssignSeats(
        SeatAssignmentService $seatAssignment
    ): void {
        $this->autoAssignError = '';

        if ((int) $this->people < 1) {
            $this->addError('people', '人数を選択してください。');
            return;
        }

        if (!$this->reservationDate || !$this->startTime || !$this->endTime) {
            $this->autoAssignError = '予約日、開始時間、終了時間を選択してください。';
            return;
        }

        $assignment = $seatAssignment->assign(
            (int) $this->people,
            $this->reservationDate,
            $this->startTime,
            $this->endTime
        );

        if ($assignment['error']) {
            $this->selectedSeatIds = [];
            $this->autoAssignError = $assignment['error'];
            return;
        }

        $this->selectedSeatIds = array_map(
            'strval',
            $assignment['selectedSeatIds']
        );

        $this->autoAssignError = '';
        $this->resetErrorBag('selectedSeatIds');
    }


    public function createReservation(
        ReservationService $service,
        SeatAssignmentService $seatAssignment
    ): void {
        /*
         * 入力チェック
         */
        $this->validateForm();

        $assignment = $seatAssignment->assign(
            (int) $this->people,
            $this->reservationDate,
            $this->startTime,
            $this->endTime
        );

        if ($assignment['error']) {
            $this->addError('selectedSeatIds', $assignment['error']);
            return;
        }

        if (empty($this->selectedSeatIds)) {
            $this->selectedSeatIds = array_map(
                'strval',
                $assignment['selectedSeatIds']
            );
        }

        $selectedCapacity = Seat::query()
            ->where('is_active', true)
            ->whereIn('id', array_map('intval', $this->selectedSeatIds))
            ->sum('capacity');

        if ($selectedCapacity < (int) $this->people) {
            $this->addError('selectedSeatIds', '選択した席の定員合計が予約人数に足りません。');
            return;
        }

        $selectedSeatIds = array_map('intval', $this->selectedSeatIds);
        $hasConflict = Reservation::query()
            ->whereDate('reservation_date', $this->reservationDate)
            ->where('status', '!=', 'cancelled')
            ->where('start_time', '<', $this->endTime)
            ->where('end_time', '>', $this->startTime)
            ->whereHas('seats', function ($query) use ($selectedSeatIds) {
                $query->whereIn('seats.id', $selectedSeatIds);
            })
            ->exists();

        if ($hasConflict) {
            $this->addError('selectedSeatIds', '選択した席はこの時間帯に使用中です。空いている席へ自動配置し直してください。');
            return;
        }

        /*
         * 予約登録
         */
        $service->create(
            [
                'customer_name' =>
                    $this->customerName,

                'people' =>
                    (int) $this->people,

                'phone' =>
                    $this->phone,

                'reservation_date' =>
                    $this->reservationDate,

                'start_time' =>
                    $this->startTime,

                'end_time' =>
                    $this->endTime,

                'status' =>
                    'reserved',

                'description' =>
                    $this->description,
            ],

            array_map(
                'intval',
                $this->selectedSeatIds
            )
        );

        $createdDate = $this->reservationDate;
        $this->selectedDate = $createdDate;

        $this->dispatch(
            'reservation-date-updated',
            date: $createdDate
        );

        /*
         * モーダルを閉じる
         */
        $this->closeModal();


        /*
         * 予約状況へ更新通知
         */
        $this->dispatch(
            'reservation-updated'
        );
    }


    /*
    |--------------------------------------------------------------------------
    | 予約編集
    |--------------------------------------------------------------------------
    */
    public function updateReservation(
        ReservationService $service,
        SeatAssignmentService $seatAssignment
    ): void {
        /*
         * 入力チェック
         */
        $this->validateForm();


        /*
         * 編集対象予約
         */
        $reservation =
            Reservation::findOrFail(
                $this->editingReservationId
            );

        $assignment = $seatAssignment->assign(
            (int) $this->people,
            $this->reservationDate,
            $this->startTime,
            $this->endTime,
            (int) $reservation->id
        );

        if ($assignment['error']) {
            $this->addError('selectedSeatIds', $assignment['error']);
            return;
        }

        if (empty($this->selectedSeatIds)) {
            $this->selectedSeatIds = array_map(
                'strval',
                $assignment['selectedSeatIds']
            );
        }

        $selectedSeatIds = array_map('intval', $this->selectedSeatIds);
        $selectedCapacity = Seat::query()
            ->where('is_active', true)
            ->whereIn('id', $selectedSeatIds)
            ->sum('capacity');

        if ($selectedCapacity < (int) $this->people) {
            $this->addError('selectedSeatIds', '選択した席の定員合計が予約人数に足りません。');
            return;
        }

        $hasConflict = Reservation::query()
            ->where('id', '!=', $reservation->id)
            ->whereDate('reservation_date', $this->reservationDate)
            ->where('status', '!=', 'cancelled')
            ->where('start_time', '<', $this->endTime)
            ->where('end_time', '>', $this->startTime)
            ->whereHas('seats', function ($query) use ($selectedSeatIds) {
                $query->whereIn('seats.id', $selectedSeatIds);
            })
            ->exists();

        if ($hasConflict) {
            $this->addError('selectedSeatIds', '選択した席はこの時間帯に使用中です。空いている席へ自動配置し直してください。');
            return;
        }

        /*
         * 更新
         */
        $service->update(
            $reservation,

            [
                'customer_name' =>
                    $this->customerName,

                'people' =>
                    (int) $this->people,

                'phone' =>
                    $this->phone,

                'reservation_date' =>
                    $this->reservationDate,

                'start_time' =>
                    $this->startTime,

                'end_time' =>
                    $this->endTime,

                'status' =>
                    $this->status,

                'description' =>
                    $this->description,
            ],

            array_map(
                'intval',
                $this->selectedSeatIds
            )
        );


        /*
         * モーダルを閉じる
         */
        $this->closeModal();


        /*
         * 予約状況へ更新通知
         */
        $this->dispatch(
            'reservation-updated'
        );
    }


    /*
    |--------------------------------------------------------------------------
    | 入力チェック
    |--------------------------------------------------------------------------
    */
    private function validateForm(): void
    {
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
                    'max:' . max(1, (int) Seat::query()->where('is_active', true)->sum('capacity')),
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

                'selectedSeatIds' => [
                    'array',
                ],

                'selectedSeatIds.*' => [
                    'integer',
                    'exists:seats,id',
                ],

                'status' => [
                    'required',
                    'in:temporary,reserved,cancelled',
                ],

                'description' => [
                    'nullable',
                    'string',
                ],
            ],

            [
                'customerName.required' =>
                    'お客様名を入力してください。',

                'people.required' =>
                    '人数を入力してください。',

                'people.integer' =>
                    '人数は数字で入力してください。',

                'people.min' =>
                    '人数は1人以上にしてください。',

                'reservationDate.required' =>
                    '予約日を選択してください。',

                'startTime.required' =>
                    '開始時間を選択してください。',

                'endTime.required' =>
                    '終了時間を選択してください。',

                'endTime.after' =>
                    '終了時間は開始時間より後にしてください。',

            ]
        );
    }


    /*
    |--------------------------------------------------------------------------
    | モーダルを閉じる
    |--------------------------------------------------------------------------
    */
    public function closeModal(): void
    {
        $this->showCreateModal = false;

        $this->showEditModal = false;

        $this->resetForm();
    }


    /*
    |--------------------------------------------------------------------------
    | フォーム初期化
    |--------------------------------------------------------------------------
    */
    private function resetForm(): void
    {
        $this->customerName = '';

        $this->people = '';

        $this->phone = '';

        $this->reservationDate =
            $this->selectedDate;

        $this->startTime = '';

        $this->endTime = '';

        $this->status = 'reserved';

        $this->description = '';

        $this->selectedSeatIds = [];

        $this->autoAssignError = '';

        $this->editingReservationId = null;

        $this->resetValidation();
    }


    /*
    |--------------------------------------------------------------------------
    | 営業時間取得
    |--------------------------------------------------------------------------
    */
    private function businessHours(): array
    {
        $setting =
            ShopSetting::first();


        if ($setting) {
            return [
                Carbon::parse(
                    $setting->business_start
                ),

                Carbon::parse(
                    $setting->business_end
                ),
            ];
        }


        /*
         * 設定が存在しない場合
         */
        return [
            Carbon::createFromTime(
                17,
                0
            ),

            Carbon::createFromTime(
                22,
                0
            ),
        ];
    }


    /*
    |--------------------------------------------------------------------------
    | モーダル用時間選択肢
    |--------------------------------------------------------------------------
    |
    | 必ず15分刻みです。
    |
    | Gridの表示単位
    |
    | 15分
    | 30分
    | 60分
    |
    | とは独立しています。
    |
    */
    private function makeModalTimes(): array
    {
        [
            $businessStart,
            $businessEnd
        ] = $this->businessHours();


        /*
        |--------------------------------------------------------------------------
        | 開始時間
        |--------------------------------------------------------------------------
        */
        $startTimeOptions = [];

        $current =
            $businessStart->copy();


        while (
            $current->lt(
                $businessEnd
            )
        ) {
            $startTimeOptions[] =
                $current->format('H:i');

            $current->addMinutes(15);
        }


        /*
        |--------------------------------------------------------------------------
        | 終了時間
        |--------------------------------------------------------------------------
        */
        $endTimeOptions = [];

        $current =
            $businessStart
                ->copy()
                ->addMinutes(15);


        while (
            $current->lte(
                $businessEnd
            )
        ) {
            $endTimeOptions[] =
                $current->format('H:i');

            $current->addMinutes(15);
        }


        return [
            $startTimeOptions,
            $endTimeOptions,
        ];
    }


    /*
    |--------------------------------------------------------------------------
    | 画面表示
    |--------------------------------------------------------------------------
    */
    public function render()
    {
        /*
         * 選択日の予約一覧
         */
        $reservations =
            Reservation::with('seats')
                ->whereDate(
                    'reservation_date',
                    $this->selectedDate
                )
                ->where(
                    'status',
                    '!=',
                    'cancelled'
                )
                ->orderBy(
                    'start_time'
                )
                ->get();


        /*
         * 有効な席
         */
        $seats =
            Seat::query()
                ->where(
                    'is_active',
                    true
                )
                ->orderBy(
                    'display_order'
                )
                ->get();


        /*
         * モーダル用時間
         */
        [
            $startTimeOptions,
            $endTimeOptions
        ] = $this->makeModalTimes();


        return view(
            'livewire.reservation-top',

            [
                'todayReservations' =>
                    $reservations,

                'seats' =>
                    $seats,

                'totalCapacity' =>
                    (int) $seats->sum('capacity'),

                'selectedSeats' =>
                    Seat::query()->whereIn('id', array_map('intval', $this->selectedSeatIds))->get(),

                'startTimeOptions' =>
                    $startTimeOptions,

                'endTimeOptions' =>
                    $endTimeOptions,
            ]
        )
            ->layout(
                'components.layout'
            );
    }
}
