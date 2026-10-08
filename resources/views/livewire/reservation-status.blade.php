<div
    class="w-full"

    x-data="reservationTimeline({ businessStart: '{{ $businessStart }}', businessEnd: '{{ $businessEnd }}', businessMinutes: {{ $businessMinutes }}, slotMinutes: {{ $slotMinutes }} })"
>

    <div class="mb-3 flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between" >

        <div class="flex flex-wrap items-center gap-2" >

            <button
                type="button"

                wire:click="previousDay"

                class="flex h-9 w-9 items-center justify-center rounded-lg border border-slate-300 bg-white transition hover:bg-slate-50"
            >
                ←
            </button>

            <input type="date" wire:model.live="date" class="h-9 border rounded-lg border-slate-300 text-sm" >

            <button
                type="button"

                wire:click="nextDay"

                class="flex h-9 w-9 items-center justify-center rounded-lg border border-slate-300 bg-white transition hover:bg-slate-50"
            >
                →
            </button>

            <select wire:model.live="displayMinutes" class="h-9 border rounded-lg border-slate-300 text-sm" >

                <option value="15">
                    15分表示
                </option>

                <option value="30">
                    30分表示
                </option>

                <option value="60">
                    1時間表示
                </option>

            </select>

        </div>

        <button
            type="button"
            wire:click="$parent.openCreateModalFromButton"
            class="h-9 self-end shrink-0 rounded-lg bg-blue-700 px-3 text-sm font-semibold text-white shadow-sm transition hover:bg-blue-800 focus:outline-none focus:ring-2 focus:ring-blue-300 focus:ring-offset-1"
        >
            ＋ 新規予約
        </button>

    </div>

    <div class="w-full overflow-hidden rounded-lg border border-slate-200 bg-white" >

        <div class="flex h-10 border-b border-slate-200 bg-slate-100" >

            <div class="flex w-24 shrink-0 items-center justify-center border-r border-slate-200 text-xs font-bold text-slate-700" >
                席
            </div>

            <div class="relative flex-1">

                @foreach ($times as $time)

                    @php

                        $headerStart = \Carbon\Carbon::createFromFormat('H:i', $businessStart);

                        $headerTime = \Carbon\Carbon::createFromFormat('H:i', $time);

                        $minutes = $headerStart->diffInMinutes($headerTime, false);

                        $position = $businessMinutes > 0

                                ? ($minutes / $businessMinutes)
                                *
                                100

                                : 0;

                    @endphp

                    <div class="pointer-events-none absolute top-0 h-full border-l border-slate-300" style="left: {{ $position }}%;" >

                        <span class="absolute left-1 top-2 whitespace-nowrap text-[10px] text-slate-500" >

                            {{ $time }}

                        </span>

                    </div>

                @endforeach

            </div>

        </div>

        @foreach ($seats as $seat)

            <div class="flex h-12 border-b border-slate-200 last:border-b-0" >

                <div class="flex w-24 shrink-0 items-center justify-center border-r border-slate-200 bg-slate-50 px-1 text-center text-xs font-bold" >

                    {{ $seat->seat_name }}

                </div>

                <div
                    class="timeline-row relative flex-1 bg-white"

                    data-seat-id="{{ $seat->id }}"
                >

                    <button
                        type="button"

                        class="absolute inset-0 z-0 block h-full w-full cursor-pointer border-0 bg-transparent p-0 hover:bg-blue-50/30"

                        title="クリックして新規予約"

                        @click="emptyClick( $event, {{ $seat->id }} )"
                    ></button>

                    @foreach ($times as $time)

                        @php

                            $gridStart = \Carbon\Carbon::createFromFormat('H:i', $businessStart);

                            $gridTime = \Carbon\Carbon::createFromFormat('H:i', $time);

                            $minutes = $gridStart->diffInMinutes($gridTime, false);

                            $position = $businessMinutes > 0

                                    ? ($minutes / $businessMinutes)
                                    *
                                    100

                                    : 0;

                        @endphp

                        <div class="pointer-events-none absolute inset-y-0 z-[1] border-l border-slate-200" style="left: {{ $position }}%;" ></div>

                    @endforeach

                    @foreach ($reservations as $reservation)

                        @if (
                            (int) $reservation['seat_id']
                            ===
                            (int) $seat->id
                        )

                            <div
                                wire:key="reservation-block-{{ $reservation['id'] }}-{{ $seat->id }}-{{ $reservation['start_time'] }}-{{ $reservation['end_time'] }}"

                                class="reservation-block absolute bottom-1 top-1 z-20 cursor-grab select-none overflow-hidden rounded-md border border-blue-300 bg-blue-100 shadow-sm active:cursor-grabbing"

                                style="left: {{ $reservation['left'] }}%; width: {{ $reservation['width'] }}%;"

                                data-id="{{ $reservation['id'] }}"

                                data-seat-id="{{ $reservation['seat_id'] }}"

                                @pointerdown="beginMove( $event, $el )"

                                @click.stop="reservationClick( {{ $reservation['id'] }} )"
                            >

                                <div
                                    class="absolute bottom-0 left-0 top-0 z-30 w-2 cursor-ew-resize hover:bg-blue-500/30"

                                    title="開始時間を変更"

                                    @pointerdown.stop="beginResize( $event, $el.parentElement, 'start' )"
                                ></div>

                                <div class="pointer-events-none h-full overflow-hidden px-3 py-1" >

                                    <div class="truncate text-xs font-bold text-blue-900" >

                                        {{ $reservation['name'] }}

                                    </div>

                                    <div class="whitespace-nowrap text-[10px] text-blue-700" >

                                        {{ $reservation['start_time'] }}

                                        ～

                                        {{ $reservation['end_time'] }}

                                        ・

                                        {{ $reservation['people'] }}名

                                    </div>

                                </div>

                                <div
                                    class="absolute bottom-0 right-0 top-0 z-30 w-2 cursor-ew-resize hover:bg-blue-500/30"

                                    title="終了時間を変更"

                                    @pointerdown.stop="beginResize( $event, $el.parentElement, 'end' )"
                                ></div>

                            </div>

                        @endif

                    @endforeach

                </div>

            </div>

        @endforeach

    </div>

    <script>
    document.addEventListener(
        'alpine:init',

        () => {
            Alpine.data(
                'reservationTimeline',

                (config) => ({
                        active: null,

                        saving: false,
                        stopFailureListener: null,

                        dragged: false,

                        pointerMove: null,

                        pointerUp: null,

                        timeToMinutes(time) {
                            const [hour, minute] = time.split(':').map(Number);

                            return (hour * 60 + minute);
                        },

                        minutesToTime(minutes) {
                            minutes = Math.round(minutes);

                            const hour = Math.floor(minutes / 60);

                            const minute = minutes % 60;

                            return (String(hour) .padStart(2, '0') + ':' + String(minute) .padStart(2, '0'));
                        },

                        snap(minutes) {
                            return (Math.round(minutes / config.slotMinutes) * config.slotMinutes);
                        },

                        emptyClick(event, seatId) {
                            const rect = event.currentTarget.getBoundingClientRect();

                            const clickedX = event.clientX - rect.left;

                            const ratio = clickedX / rect.width;

                            const businessStart = this.timeToMinutes(config.businessStart);

                            const businessEnd = this.timeToMinutes(config.businessEnd);

                            let minutes = businessStart + (ratio * config.businessMinutes);

                            minutes = Math.round(minutes / 15) * 15;

                            minutes = Math.max(businessStart, Math.min(minutes, businessEnd - 15));

                            this.$wire.selectEmptySlot(seatId, this.minutesToTime(minutes));
                        },

                        reservationClick(id) {
                            if (this.dragged || this.saving) {
                                return;
                            }

                            this.$wire.selectReservation(id);
                        },

                        beginMove(event, element) {
                            if (event.button !== 0 || this.saving) {
                                return;
                            }

                            event.preventDefault();

                            const row = element.closest('.timeline-row');

                            if (!row) {
                                return;
                            }

                            const rect = row.getBoundingClientRect();

                            this.active = {
                                mode: 'move',

                                element: element,

                                reservationId: Number(element.dataset.id),

                                originalSeatId: Number(element.dataset.seatId),

                                startX: event.clientX,

                                originalLeft: parseFloat(element.style.left),

                                originalWidth: parseFloat(element.style.width),

                                rowWidth: rect.width,
                            };

                            this.dragged = false;

                            window.addEventListener('pointermove', this.pointerMove);

                            window.addEventListener(
                                'pointerup',
                                this.pointerUp,
                                {
                                    once: true
                                }
                            );
                        },

                        beginResize(event, element, side) {
                            if (this.saving) return;
                            event.preventDefault();

                            const row = element.closest('.timeline-row');

                            if (!row) {
                                return;
                            }

                            const rect = row.getBoundingClientRect();

                            this.active = {
                                mode: side === 'start'
                                ? 'resize-start'
                                : 'resize-end',

                                element: element,

                                reservationId: Number(element.dataset.id),

                                originalSeatId: Number(element.dataset.seatId),

                                startX: event.clientX,

                                originalLeft: parseFloat(element.style.left),

                                originalWidth: parseFloat(element.style.width),

                                rowWidth: rect.width,
                            };

                            this.dragged = false;

                            window.addEventListener('pointermove', this.pointerMove);

                            window.addEventListener(
                                'pointerup',
                                this.pointerUp,
                                {
                                    once: true
                                }
                            );
                        },

                        destroy() {
                            window.removeEventListener('pointermove', this.pointerMove);
                            window.removeEventListener('pointerup', this.pointerUp);
                            this.stopFailureListener?.();
                        },

                        init() {
                            this.stopFailureListener = this.$wire.on('reservation-drag-failed', (event) => {
                                    window.alert(event.message ?? event.detail?.message ?? '予約を更新できませんでした。');
                            });

                            this.pointerMove = (event) => {
                                if (!this.active) {
                                    return;
                                }

                                const deltaPixels = event.clientX - this.active.startX;

                                if (Math.abs(deltaPixels) > 3) {
                                    this.dragged = true;
                                }

                                const deltaPercent = (deltaPixels / this.active.rowWidth) * 100;

                                const slotPercent = (config.slotMinutes / config.businessMinutes) * 100;

                                const snappedDelta = Math.round(deltaPercent / slotPercent) * slotPercent;

                                if (this.active.mode === 'move') {
                                    let left = this.active.originalLeft + snappedDelta;

                                    left = Math.max(0, Math.min(left, 100 - this.active.originalWidth));

                                    this.active.element.style.left = `${left}%`;
                                }

                                if (this.active.mode === 'resize-start') {
                                    let left = this.active.originalLeft + snappedDelta;

                                    let width = this.active.originalWidth - snappedDelta;

                                    if (width >= slotPercent && left >= 0) {
                                        this.active.element.style.left = `${left}%`;

                                        this.active.element.style.width = `${width}%`;
                                    }
                                }

                                if (this.active.mode === 'resize-end') {
                                    let width = this.active.originalWidth + snappedDelta;

                                    if (width >= slotPercent && (this.active.originalLeft + width) <= 100) {
                                        this.active.element.style.width = `${width}%`;
                                    }
                                }
                            };

                            this.pointerUp = (event) => {
                                window.removeEventListener('pointermove', this.pointerMove);

                                if (!this.active) {
                                    return;
                                }

                                if (!this.dragged) {
                                    this.active = null;

                                    return;
                                }

                                const element = this.active.element;

                                const target = document.elementFromPoint(event.clientX, event.clientY);

                                const targetRow = target
                                ?.closest('.timeline-row');

                                let seatId = this.active.originalSeatId;

                                if (this.active.mode === 'move' && targetRow) {
                                    seatId = Number(targetRow .dataset .seatId);
                                }

                                const businessStart = this.timeToMinutes(config.businessStart);

                                const left = parseFloat(element.style.left);

                                const width = parseFloat(element.style.width);

                                let start = businessStart + (left / 100 * config.businessMinutes);

                                let duration = (width / 100 * config.businessMinutes);

                                start = this.snap(start);

                                duration = Math.max(config.slotMinutes, this.snap(duration));

                                let end = start + duration;

                                const businessEnd = this.timeToMinutes(config.businessEnd);

                                if (end > businessEnd) {
                                    end = businessEnd;

                                    start = Math.max(businessStart, end - duration);
                                }

                                const operation = this.active;
                                // DOMの仮変更を戻してから、DBの結果でLivewireに再描画させます。
                                element.style.left = `${operation.originalLeft}%`;
                                element.style.width = `${operation.originalWidth}%`;
                                this.saving = true;

                                this.$wire.moveReservation(
                                    operation.reservationId,
                                    seatId,
                                    this.minutesToTime(start),
                                    this.minutesToTime(end),
                                    operation.mode,
                                    operation.originalSeatId
                                ).catch((error) => {
                                        console.error('Reservation update failed', error);
                                        window.alert('予約を保存できませんでした。もう一度操作してください。');
                                }).finally(() => {
                                        this.saving = false;
                                });

                                this.active = null;

                                setTimeout(
                                    () => {
                                        this.dragged = false;
                                    },
                                    100
                                );
                            };
                        }
                })
            );
        }
    );
</script>

</div>
