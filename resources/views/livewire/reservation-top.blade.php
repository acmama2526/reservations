<div class="min-h-screen bg-stone-50">
    @php
        $dailyStatusStyles = [
            'temporary' => ['class' => 'border-amber-400 bg-amber-50 text-amber-950', 'label' => '仮予約'],
            'reserved' => ['class' => 'border-blue-400 bg-blue-50 text-blue-950', 'label' => '確定'],
            'visited' => ['class' => 'border-emerald-400 bg-emerald-50 text-emerald-950', 'label' => '来店済'],
            'paid' => ['class' => 'border-violet-400 bg-violet-50 text-violet-950', 'label' => '会計済'],
            'cancelled' => ['class' => 'border-stone-400 bg-stone-100 text-stone-700', 'label' => 'キャンセル'],
        ];
    @endphp
    <div class="mx-auto max-w-[1800px] px-4 py-5 text-stone-900 sm:px-6 lg:px-8">
        <div class="grid grid-cols-1 items-start gap-4 xl:grid-cols-12" >
            <section class="min-w-0 xl:col-span-9">
                <div class="rounded-xl border border-stone-300 bg-white p-4 shadow-sm" >
                    <livewire:reservation-status wire:model.live="selectedDate" />
                </div>
            </section>
            <section class="min-w-0 xl:col-span-3">
                <div class="rounded-xl border border-stone-300 bg-white shadow-sm" >
                    <div class="border-b border-emerald-200 bg-emerald-50 px-4 py-4" >
                        <h2 class="text-base font-semibold text-stone-900" >
                            {{ \Carbon\Carbon::parse($selectedDate)->format('m月d日') }}
                            の予約
                        </h2>
                    </div>
                    <div class="space-y-2 p-3">
                        @forelse ($todayReservations as $reservation)
                            <div
                                wire:key="reservation-list-{{ $reservation->id }}"
                                class="relative rounded-lg border border-l-4 p-3 shadow-sm {{ $dailyStatusStyles[$reservation->status]['class'] ?? 'border-stone-300 bg-white text-stone-900' }}"
                            >
                                <button
                                    type="button"
                                    wire:click="openEditModal({{ $reservation->id }})"
                                    class="block min-h-12 w-full cursor-pointer rounded-md text-left outline-none focus-visible:ring-2 focus-visible:ring-emerald-700 focus-visible:ring-offset-2"
                                    aria-label="{{ $reservation->customer_name }}の予約を編集"
                                >
                                <div class="flex items-center justify-between gap-2" >
                                    <span class="font-bold text-stone-800" >
                                        {{ substr($reservation->start_time, 0, 5) }}
                                        ～
                                        {{ substr($reservation->end_time, 0, 5) }}
                                    </span>
                                </div>
                                <div class="mt-1 flex items-center justify-between gap-2" >
                                    <span class="min-w-0 break-words text-base font-semibold text-stone-900">
                                        {{ $reservation->customer_name }}
                                    </span>
                                    <span class="shrink-0 font-semibold text-stone-900">
                                        {{ $reservation->people }}名
                                    </span>
                                </div>
                                <div class="mt-1 text-xs text-stone-600" >
                                    @forelse ($reservation->seats as $seat)
                                        {{ $seat->seat_name }}
                                        @unless ($loop->last)
                                            /
                                        @endunless
                                    @empty
                                        席未設定
                                    @endforelse
                                </div>
                                    <div class="mt-3 flex flex-wrap items-center justify-between gap-2 border-t border-black/10 pt-2">
                                        <span class="rounded-md border border-black/10 bg-white/70 px-2 py-1 text-xs font-semibold">
                                            {{ $dailyStatusStyles[$reservation->status]['label'] ?? '状態未取得' }}
                                        </span>
                                        <span class="text-sm font-semibold text-emerald-800">編集する →</span>
                                    </div>
                                </button>
                                <button
                                    type="button"
                                    wire:click.stop="deleteReservation({{ $reservation->id }})"
                                    wire:confirm="この予約を削除します。よろしいですか？"
                                    aria-label="{{ $reservation->customer_name }}の予約を削除"
                                    class="ml-auto mt-3 flex min-h-12 min-w-[64px] cursor-pointer items-center justify-center rounded-lg border border-rose-300 bg-white px-3 text-sm font-semibold text-rose-700 transition hover:bg-rose-50 focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-rose-700 focus-visible:ring-offset-2"
                                >
                                    削除
                                </button>
                            </div>
                        @empty
                            <div class="py-10 text-center text-sm text-stone-600" >
                                この日の予約はありません。
                            </div>
                        @endforelse
                    </div>
                </div>
            </section>
        </div>
    </div>
    @if ($showCreateModal || $showEditModal)
        <div class="fixed inset-0 z-50 flex items-center justify-center bg-stone-950/50 p-4 backdrop-blur-sm" >
            <div class="flex max-h-[90dvh] w-full max-w-3xl flex-col overflow-hidden rounded-2xl bg-white shadow-2xl" >
                <div class="flex shrink-0 items-center justify-between border-b border-emerald-200 bg-emerald-50 px-4 py-4 sm:px-6" >
                    <h2 class="text-xl font-bold text-stone-900" >
                        @if ($showCreateModal)
                            新規予約
                        @else
                            予約編集
                        @endif
                    </h2>
                    <button
                        type="button"
                        wire:click="closeModal"
                        aria-label="予約フォームを閉じる" class="flex h-12 w-12 shrink-0 cursor-pointer items-center justify-center rounded-lg border border-stone-300 bg-white text-2xl text-stone-700 transition hover:bg-stone-100 focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-emerald-700"
                    >
                        ×
                    </button>
                </div>
                <div class="min-h-0 flex-1 overflow-y-auto p-4 sm:p-6" >
                    <div class="grid grid-cols-1 gap-4 md:grid-cols-2" >
                        @if ($showEditModal)
                            <div class="md:col-span-2">
                                <span class="mb-2 block text-sm font-semibold">状態</span>
                                <div class="flex flex-wrap gap-2" role="group" aria-label="予約状態">
                                    @foreach ([
                                        'temporary' => '仮予約',
                                        'reserved' => '確定',
                                        'visited' => '来店済',
                                        'paid' => '会計済',
                                        'cancelled' => 'キャンセル',
                                    ] as $statusValue => $statusLabel)
                                        <button
                                            type="button"
                                            wire:click="$set('status', '{{ $statusValue }}')"
                                            aria-pressed="{{ $status === $statusValue ? 'true' : 'false' }}"
                                            class="min-h-12 cursor-pointer rounded-lg border px-4 py-2 text-sm font-semibold transition focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-emerald-700 focus-visible:ring-offset-2 {{ $dailyStatusStyles[$statusValue]['class'] }} {{ $status === $statusValue ? 'ring-2 ring-current ring-offset-2 shadow-sm' : 'opacity-75 hover:opacity-100' }}"
                                        >
                                            {{ $statusLabel }}
                                        </button>
                                    @endforeach
                                </div>
                                @error('status')
                                    <p class="mt-2 text-sm text-red-700">{{ $message }}</p>
                                @enderror
                            </div>
                        @endif
                        <div>
                            <label for="customerName" class="mb-2 block text-sm font-semibold text-stone-700" >
                                お客様名
                            </label>
                            <input
                                id="customerName"
                                type="text"
                                wire:model="customerName"
                                class="min-h-12 w-full rounded-lg border border-stone-400 bg-white px-3 py-2.5 text-base text-stone-900 outline-none focus:border-emerald-700 focus:ring-2 focus:ring-emerald-700/20"
                            >
                            @error('customerName')
                                <p class="mt-2 text-sm text-red-700">
                                    {{ $message }}
                                </p>
                            @enderror
                        </div>
                        <div>
                            <label for="people" class="mb-2 block text-sm font-semibold text-stone-700" >
                                人数
                            </label>
                            <select id="people" wire:model.live="people" class="min-h-12 w-full rounded-lg border border-stone-400 bg-white px-3 py-2.5 text-base text-stone-900 outline-none focus:border-emerald-700 focus:ring-2 focus:ring-emerald-700/20" >
                                <option value="">選択してください</option>
                                @for ($count = 1; $count <= $totalCapacity; $count++)
                                    <option value="{{ $count }}">{{ $count }}名</option>
                                @endfor
                            </select>
                            @error('people')
                                <p class="mt-2 text-sm text-red-700">
                                    {{ $message }}
                                </p>
                            @enderror
                        </div>
                        <div>
                            <label for="phone" class="mb-1 block font-semibold text-stone-700">
                                電話番号
                            </label>
                            <input id="phone" type="tel" wire:model="phone" inputmode="numeric" pattern="[0-9]*"
                                maxlength="11"
                                class="min-h-12 w-full rounded-lg border border-stone-400 bg-white px-3 py-2 text-base text-stone-900 outline-none transition focus:border-emerald-700 focus:ring-2 focus:ring-emerald-700/20"
                                placeholder="09012345678">
                            @error('phone')
                                <p class="mt-1 text-sm text-red-600">
                                    {{ $message }}
                                </p>
                            @enderror
                        </div>
                        <div>
                            <label for="reservationDate" class="mb-2 block text-sm font-semibold text-stone-700" >
                                予約日
                            </label>
                            <input
                                id="reservationDate"
                                type="date"
                                wire:model="reservationDate"
                                class="min-h-12 w-full rounded-lg border border-stone-400 bg-white px-3 py-2.5 text-base text-stone-900 outline-none focus:border-emerald-700 focus:ring-2 focus:ring-emerald-700/20"
                            >
                            @error('reservationDate')
                                <p class="mt-2 text-sm text-red-700">
                                    {{ $message }}
                                </p>
                            @enderror
                        </div>
                        <div>
                            <label for="startTime" class="mb-2 block text-sm font-semibold text-stone-700" >
                                開始時間
                            </label>
                            <select id="startTime" wire:model="startTime" class="min-h-12 w-full rounded-lg border border-stone-400 bg-white px-3 py-2.5 text-base text-stone-900 outline-none focus:border-emerald-700 focus:ring-2 focus:ring-emerald-700/20" >
                                <option value="">
                                    選択してください
                                </option>
                                @foreach ($startTimeOptions as $time)
                                    <option value="{{ $time }}">
                                        {{ $time }}
                                    </option>
                                @endforeach
                            </select>
                            @error('startTime')
                                <p class="mt-2 text-sm text-red-700">
                                    {{ $message }}
                                </p>
                            @enderror
                        </div>
                        <div>
                            <label for="endTime" class="mb-2 block text-sm font-semibold text-stone-700" >
                                終了時間
                            </label>
                            <select id="endTime" wire:model="endTime" class="min-h-12 w-full rounded-lg border border-stone-400 bg-white px-3 py-2.5 text-base text-stone-900 outline-none focus:border-emerald-700 focus:ring-2 focus:ring-emerald-700/20" >
                                <option value="">
                                    選択してください
                                </option>
                                @foreach ($endTimeOptions as $time)
                                    <option value="{{ $time }}">
                                        {{ $time }}
                                    </option>
                                @endforeach
                            </select>
                            @error('endTime')
                                <p class="mt-2 text-sm text-red-700">
                                    {{ $message }}
                                </p>
                            @enderror
                        </div>
                    </div>
                    <div class="mt-5">
                        <label class="mb-2 block text-sm font-semibold" >
                            席
                        </label>
                        <div class="grid grid-cols-2 gap-2 md:grid-cols-3" wire:key="seat-selection-{{ $seatSelectionResetKey }}" >
                            @foreach ($seats as $seat)
                                <label class="flex min-h-12 cursor-pointer items-center gap-3 rounded-lg border border-stone-400 bg-white p-3 transition hover:bg-emerald-50 has-[:checked]:border-emerald-700 has-[:checked]:bg-emerald-50 has-[:checked]:ring-1 has-[:checked]:ring-emerald-700" >
                                    <input
                                        type="checkbox"
                                        wire:model="selectedSeatIds"
                                        value="{{ $seat->id }}"
                                        class="h-5 w-5 shrink-0 rounded border border-stone-400 text-emerald-700 focus:ring-2 focus:ring-emerald-700/20"
                                    >
                                    <span class="text-sm">
                                        {{ $seat->seat_name }}
                                        <span class="text-stone-600">
                                            （{{ $seat->capacity }}名）
                                        </span>
                                    </span>
                                </label>
                            @endforeach
                        </div>
                        @error('selectedSeatIds')
                            <p class="mt-2 text-sm text-red-700">{{ $message }}</p>
                        @enderror
                        @error('selectedSeatIds.*')
                            <p class="mt-2 text-sm text-red-700">{{ $message }}</p>
                        @enderror
                            <div class="mt-3">
                                <button
                                    type="button"
                                    wire:click="autoAssignSeats"
                                    wire:loading.attr="disabled"
                                    @disabled(!$people || !$reservationDate || !$startTime || !$endTime)
                                    class="inline-flex min-h-12 cursor-pointer items-center justify-center rounded-lg border border-emerald-700 bg-white px-4 py-2 text-sm font-semibold text-emerald-800 hover:bg-emerald-50 focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-emerald-700 disabled:cursor-not-allowed disabled:opacity-50"
                                >
                                    🪑 空いている席へ自動配置
                                </button>
                                @if ($autoAssignError)
                                    <p class="mt-2 rounded-lg bg-red-50 p-2 text-sm text-red-700">
                                        {{ $autoAssignError }}
                                    </p>
                                @endif
                            </div>
                        @if ($selectedSeats->isNotEmpty())
                            <div class="mt-3 rounded-lg bg-emerald-50 p-3 text-sm text-stone-900">
                                <span class="font-semibold">選択中：</span>
                                {{ $selectedSeats->pluck('seat_name')->join(' / ') }}
                                （定員合計 {{ $selectedSeats->sum('capacity') }}名）
                            </div>
                        @endif
                    </div>
                    <div class="mt-5">
                        <label for="description" class="mb-2 block text-sm font-semibold text-stone-700" >
                            備考
                        </label>
                        <textarea
                            id="description"
                            wire:model="description"
                            rows="3"
                            class="min-h-12 w-full rounded-lg border border-stone-400 bg-white px-3 py-2.5 text-base text-stone-900 outline-none focus:border-emerald-700 focus:ring-2 focus:ring-emerald-700/20"
                        ></textarea>
                    </div>
                </div>
                <div class="flex shrink-0 flex-wrap items-center justify-end gap-3 border-t border-stone-300 bg-stone-50 px-4 py-4 sm:px-6" >
                    <button
                        type="button"
                        wire:click="closeModal"
                        class="inline-flex min-h-12 cursor-pointer items-center justify-center rounded-lg border border-stone-400 bg-white px-5 py-2 text-sm font-medium text-stone-700 hover:bg-stone-100 focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-emerald-700"
                    >
                        キャンセル
                    </button>
                    @if ($showCreateModal)
                        <button
                            type="button"
                            wire:click="createReservation"
                            wire:loading.attr="disabled"
                            class="inline-flex min-h-12 min-w-[112px] cursor-pointer items-center justify-center rounded-lg bg-emerald-700 px-5 py-2 text-sm font-semibold text-white hover:bg-emerald-800 focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-emerald-700 focus-visible:ring-offset-2 disabled:cursor-wait disabled:opacity-50"
                        >
                            予約登録
                        </button>
                    @else
                        <button
                            type="button"
                            wire:click="updateReservation"
                            wire:loading.attr="disabled"
                            class="inline-flex min-h-12 min-w-[112px] cursor-pointer items-center justify-center rounded-lg bg-emerald-700 px-5 py-2 text-sm font-semibold text-white hover:bg-emerald-800 focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-emerald-700 focus-visible:ring-offset-2 disabled:cursor-wait disabled:opacity-50"
                        >
                            更新
                        </button>
                        <button
                            type="button"
                            wire:click="deleteReservation({{ $editingReservationId }})"
                            wire:confirm="この予約を削除します。よろしいですか？"
                            wire:loading.attr="disabled"
                            class="inline-flex min-h-12 cursor-pointer items-center justify-center rounded-lg border border-rose-300 bg-white px-5 py-2 text-sm font-semibold text-rose-700 hover:bg-rose-50 focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-rose-700 disabled:cursor-wait disabled:opacity-50"
                        >
                            削除
                        </button>
                    @endif
                </div>
            </div>
        </div>
    @endif
</div>
