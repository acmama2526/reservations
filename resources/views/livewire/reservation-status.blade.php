<div class="min-w-0 w-full xl:flex xl:h-full xl:min-h-0 xl:flex-col" x-data="reservationTimeline({ businessStart: '{{ $businessStart }}', businessEnd: '{{ $businessEnd }}', businessMinutes: {{ $businessMinutes }}, slotMinutes: {{ $slotMinutes }} })">
    @php
        $timelineStatusStyles = [
            'temporary' => ['class' => 'border-amber-400 bg-amber-100 text-amber-950', 'label' => '仮予約'],
            'reserved' => ['class' => 'border-blue-400 bg-blue-100 text-blue-950', 'label' => '確定'],
            'visited' => ['class' => 'border-emerald-400 bg-emerald-100 text-emerald-950', 'label' => '来店済'],
            'paid' => ['class' => 'border-violet-400 bg-violet-100 text-violet-950', 'label' => '会計済'],
            'cancelled' => ['class' => 'border-stone-400 bg-stone-100 text-stone-700', 'label' => 'キャンセル'],
        ];
    @endphp
    <div class="mb-3 flex shrink-0 flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
        <div class="flex flex-wrap items-center gap-2">
            <button type="button" wire:click="previousDay" aria-label="前日の予約を表示"
                class="flex h-12 w-12 shrink-0 cursor-pointer items-center justify-center rounded-lg border border-stone-400 bg-white text-lg font-semibold text-stone-700 transition hover:bg-stone-100 focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-emerald-700">
                ←
            </button>
            <input type="date" wire:model.live="date" aria-label="予約を表示する日付"
                class="h-12 min-w-0 rounded-lg border border-stone-400 bg-white px-3 text-base text-stone-900 outline-none focus:border-emerald-700 focus:ring-2 focus:ring-emerald-700/20">
            <button type="button" wire:click="nextDay" aria-label="翌日の予約を表示"
                class="flex h-12 w-12 shrink-0 cursor-pointer items-center justify-center rounded-lg border border-stone-400 bg-white text-lg font-semibold text-stone-700 transition hover:bg-stone-100 focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-emerald-700">
                →
            </button>
            <select wire:model.live="displayMinutes" aria-label="タイムテーブルの表示単位"
                class="h-12 min-w-0 rounded-lg border border-stone-400 bg-white px-3 text-base text-stone-900 outline-none focus:border-emerald-700 focus:ring-2 focus:ring-emerald-700/20">
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
        <button type="button" wire:click="$parent.openCreateModalFromButton"
            class="inline-flex min-h-12 self-end shrink-0 cursor-pointer items-center justify-center rounded-lg bg-emerald-700 px-5 text-sm font-semibold text-white transition hover:bg-emerald-800 focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-emerald-700 focus-visible:ring-offset-2">
            ＋ 新規予約
        </button>
    </div>
    <div class="mb-3 flex shrink-0 flex-wrap items-center gap-2 text-xs" aria-label="予約状態の色分け">
        <span class="mr-1 font-medium text-stone-600">状態</span>
        <span
            class="inline-flex items-center gap-1.5 rounded-md border border-amber-400 bg-amber-50 px-2.5 py-1 font-semibold text-amber-950"><span
                class="h-2 w-2 rounded-full bg-amber-500" aria-hidden="true"></span>仮予約</span>
        <span
            class="inline-flex items-center gap-1.5 rounded-md border border-blue-400 bg-blue-50 px-2.5 py-1 font-semibold text-blue-950"><span
                class="h-2 w-2 rounded-full bg-blue-500" aria-hidden="true"></span>確定</span>
        <span
            class="inline-flex items-center gap-1.5 rounded-md border border-emerald-400 bg-emerald-50 px-2.5 py-1 font-semibold text-emerald-950"><span
                class="h-2 w-2 rounded-full bg-emerald-500" aria-hidden="true"></span>来店済</span>
        <span
            class="inline-flex items-center gap-1.5 rounded-md border border-violet-400 bg-violet-50 px-2.5 py-1 font-semibold text-violet-950"><span
                class="h-2 w-2 rounded-full bg-violet-500" aria-hidden="true"></span>会計済</span>
        <span
            class="inline-flex items-center gap-1.5 rounded-md border border-stone-400 bg-stone-100 px-2.5 py-1 font-semibold text-stone-700"><span
                class="h-2 w-2 rounded-full bg-stone-500" aria-hidden="true"></span>キャンセル</span>
    </div>
    <div role="region" aria-label="席別の予約タイムテーブル" tabindex="0"
        class="w-full overflow-x-auto rounded-lg xl:min-h-0 xl:flex-1 xl:overflow-y-hidden border border-stone-300 bg-white focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-emerald-700">
        <div class="xl:flex xl:h-full xl:min-h-0 xl:flex-col"
            style="min-width: {{ max(960, count($times) * 36 + 112) }}px;">
            <div class="sticky top-0 z-40 flex h-10 shrink-0 border-b border-stone-300 bg-stone-100">
                <div
                    class="sticky left-0 z-30 flex w-28 shrink-0 items-center justify-center border-r border-stone-300 bg-stone-100 text-sm font-semibold text-stone-700">
                    席
                </div>
                <div class="relative flex-1">
                    @foreach ($times as $time)
                        @php
                            $headerStart = \Carbon\Carbon::createFromFormat('H:i', $businessStart);
                            $headerTime = \Carbon\Carbon::createFromFormat('H:i', $time);
                            $minutes = $headerStart->diffInMinutes($headerTime, false);
                            $position = $businessMinutes > 0 ? ($minutes / $businessMinutes) * 100 : 0;
                        @endphp
                        <div class="pointer-events-none absolute top-0 h-full border-l border-stone-300"
                            style="left: {{ $position }}%;">
                            <span
                                class="absolute top-2 whitespace-nowrap text-xs tabular-nums text-stone-700 {{ $loop->last ? '-left-1 -translate-x-full' : 'left-1' }}">
                                {{ $time }}
                            </span>
                        </div>
                    @endforeach
                </div>
            </div>
            @foreach ($seats as $seat)
                <div
                    class="flex h-12 border-b border-stone-300 last:border-b-0 xl:h-auto xl:min-h-0 xl:max-h-14 xl:flex-1">
                    <div
                        class="sticky left-0 z-30 flex w-28 shrink-0 items-center justify-center border-r border-stone-300 bg-stone-50 px-2 text-center text-sm font-semibold text-stone-800">
                        {{ $seat->seat_name }}
                    </div>
                    <div class="timeline-row relative flex-1 bg-white" data-seat-id="{{ $seat->id }}">
                        <button type="button"
                            class="absolute inset-0 z-0 block h-full w-full cursor-pointer border-0 bg-transparent p-0 hover:bg-emerald-50/30"
                            title="クリックして新規予約" @click="emptyClick( $event, {{ $seat->id }} )"></button>
                        @foreach ($times as $time)
                            @php
                                $gridStart = \Carbon\Carbon::createFromFormat('H:i', $businessStart);
                                $gridTime = \Carbon\Carbon::createFromFormat('H:i', $time);
                                $minutes = $gridStart->diffInMinutes($gridTime, false);
                                $position = $businessMinutes > 0 ? ($minutes / $businessMinutes) * 100 : 0;
                            @endphp
                            <div class="pointer-events-none absolute inset-y-0 z-[1] border-l border-stone-300"
                                style="left: {{ $position }}%;"></div>
                        @endforeach
                        @foreach ($reservations as $reservation)
                            @if ((int) $reservation['seat_id'] === (int) $seat->id)
                                <div wire:key="reservation-block-{{ $reservation['id'] }}-{{ $seat->id }}-{{ $reservation['start_time'] }}-{{ $reservation['end_time'] }}"
                                    class="reservation-block absolute bottom-1 top-1 z-20 cursor-grab select-none overflow-hidden rounded-lg border shadow-sm active:cursor-grabbing {{ $timelineStatusStyles[$reservation['status'] ?? '']['class'] ?? 'border-stone-400 bg-stone-100 text-stone-800' }}"
                                    title="{{ $reservation['name'] }}／{{ $reservation['start_time'] }}～{{ $reservation['end_time'] }}／{{ $reservation['people'] }}名／{{ $timelineStatusStyles[$reservation['status'] ?? '']['label'] ?? '状態未取得' }}"
                                    style="left: {{ $reservation['left'] }}%; width: {{ $reservation['width'] }}%;"
                                    data-id="{{ $reservation['id'] }}" data-seat-id="{{ $reservation['seat_id'] }}"
                                    @pointerdown="beginMove( $event, $el )"
                                    @click.stop="reservationClick( {{ $reservation['id'] }} )">
                                    <div class="absolute bottom-0 left-0 top-0 z-30 w-2 cursor-ew-resize hover:bg-emerald-500/30"
                                        title="開始時間を変更"
                                        @pointerdown.stop="beginResize( $event, $el.parentElement, 'start' )"></div>
                                    <div
                                        class="pointer-events-none flex h-full min-w-0 items-center gap-2 overflow-hidden px-3 text-xs leading-4">
                                        <span class="min-w-0 max-w-full shrink-0 truncate font-semibold">
                                            {{ $reservation['name'] }}
                                        </span>
                                        <span class="shrink-0 whitespace-nowrap tabular-nums">
                                            {{ $reservation['start_time'] }}～{{ $reservation['end_time'] }}
                                        </span>
                                        <span class="shrink-0 whitespace-nowrap tabular-nums">
                                            {{ $reservation['people'] }}名
                                        </span>
                                        <span
                                            class="shrink-0 whitespace-nowrap rounded border border-current/20 bg-white/50 px-1 text-[10px] font-semibold leading-4">
                                            {{ $timelineStatusStyles[$reservation['status'] ?? '']['label'] ?? '状態未取得' }}
                                        </span>
                                    </div>
                                    <div class="absolute bottom-0 right-0 top-0 z-30 w-2 cursor-ew-resize hover:bg-emerald-500/30"
                                        title="終了時間を変更"
                                        @pointerdown.stop="beginResize( $event, $el.parentElement, 'end' )"></div>
                                </div>
                            @endif
                        @endforeach
                    </div>
                </div>
            @endforeach
        </div>
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
                            return (String(hour).padStart(2, '0') + ':' + String(minute).padStart(2, '0'));
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
                                this.pointerUp, {
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
                                mode: side === 'start' ?
                                    'resize-start' : 'resize-end',
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
                                this.pointerUp, {
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
                                window.alert(event.message ?? event.detail?.message ??
                                    '予約を更新できませんでした。');
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
                                    if (width >= slotPercent && (this.active.originalLeft + width) <=
                                        100.000001) {
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
                                    seatId = Number(targetRow.dataset.seatId);
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
