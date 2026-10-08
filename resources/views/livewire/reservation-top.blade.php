<div class="min-h-screen bg-slate-50">

    <div class="mx-auto max-w-[1800px] px-4 py-4">

        <div class="grid grid-cols-1 items-start gap-4 lg:grid-cols-12" >

            <section class="lg:col-span-9">

                <div class="rounded-xl border border-slate-200 bg-white p-4 shadow-sm" >

                    <livewire:reservation-status wire:model.live="selectedDate" />

                </div>

            </section>

            <section class="lg:col-span-3">

                <div class="rounded-xl border border-slate-200 bg-white shadow-sm" >

                    <div class="border-b border-slate-200 px-4 py-3" >

                        <h2 class="font-bold text-blue-900" >

                            {{ \Carbon\Carbon::parse($selectedDate)->format('m月d日') }}
                            の予約

                        </h2>

                    </div>

                    <div class="space-y-2 p-3">

                        @forelse ($todayReservations as $reservation)

                            <div
                                wire:key="reservation-list-{{ $reservation->id }}"
                                class="relative rounded-lg border border-slate-200 bg-white p-3 transition hover:border-blue-400 hover:bg-blue-50"
                            >

                                <button
                                    type="button"
                                    wire:click="openEditModal({{ $reservation->id }})"
                                    class="block w-full text-left"
                                    aria-label="{{ $reservation->customer_name }}の予約を編集"
                                >

                                <div class="flex items-center justify-between gap-2 pr-14" >

                                    <span class="font-bold text-slate-800" >

                                        {{ substr($reservation->start_time, 0, 5) }}

                                        ～

                                        {{ substr($reservation->end_time, 0, 5) }}

                                    </span>

                                </div>

                                <div class="mt-1 flex items-center justify-between gap-2 pr-14" >
                                    <span class="truncate font-semibold text-blue-900">
                                        {{ $reservation->customer_name }}
                                    </span>
                                    <span class="shrink-0 font-semibold text-blue-900">
                                        {{ $reservation->people }}名
                                    </span>
                                </div>

                                <div class="mt-1 text-xs text-slate-500" >

                                    @forelse ($reservation->seats as $seat)

                                        {{ $seat->seat_name }}

                                        @unless ($loop->last)
                                            /
                                        @endunless

                                    @empty

                                        席未設定

                                    @endforelse

                                </div>

                                </button>

                                <button
                                    type="button"
                                    wire:click.stop="deleteReservation({{ $reservation->id }})"
                                    wire:confirm="この予約を削除します。よろしいですか？"
                                    aria-label="{{ $reservation->customer_name }}の予約を削除"
                                    class="absolute right-2 top-2 inline-flex h-7 items-center justify-center rounded-md bg-red-600 px-2 text-xs font-semibold text-white transition hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-red-300"
                                >
                                    削除
                                </button>
                            </div>

                        @empty

                            <div class="py-10 text-center text-sm text-slate-400" >

                                この日の予約はありません。

                            </div>

                        @endforelse

                    </div>

                </div>

            </section>

        </div>

    </div>

    @if ($showCreateModal || $showEditModal)

        <div class="fixed inset-0 z-50 flex items-center justify-center bg-black/40 p-4 backdrop-blur-sm" >

            <div class="flex max-h-[90vh] w-full max-w-3xl flex-col overflow-hidden rounded-2xl bg-white shadow-2xl" >

                <div class="flex shrink-0 items-center justify-between border-b border-slate-200 px-6 py-4" >

                    <h2 class="text-xl font-bold text-blue-900" >

                        @if ($showCreateModal)
                            新規予約
                        @else
                            予約編集
                        @endif

                    </h2>

                    <button
                        type="button"

                        wire:click="closeModal"

                        class="flex h-9 w-9 items-center justify-center rounded-full text-xl text-slate-500 transition hover:bg-slate-100"
                    >
                        ×
                    </button>

                </div>

                <div class="min-h-0 flex-1 overflow-y-auto p-6" >

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
                                            class="min-h-10 rounded-lg border px-4 py-2 text-sm font-semibold transition focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-blue-500 focus-visible:ring-offset-2 {{ $status === $statusValue ? 'border-blue-700 bg-blue-700 text-white' : 'border-slate-300 bg-white text-slate-700 hover:bg-slate-50' }}"
                                        >
                                            {{ $statusLabel }}
                                        </button>
                                    @endforeach
                                </div>

                                @error('status')
                                    <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                                @enderror
                            </div>
                        @endif

                        <div>

                            <label for="customerName" class="mb-1 block text-sm font-semibold" >
                                お客様名
                            </label>

                            <input
                                id="customerName"
                                type="text"

                                wire:model="customerName"

                                class="w-full rounded-lg border border-slate-300 focus:border-blue-500 focus:ring-2 focus:ring-blue-100"
                            >

                            @error('customerName')

                                <p class="mt-1 text-xs text-red-600">
                                    {{ $message }}
                                </p>

                            @enderror

                        </div>

                        <div>

                            <label for="people" class="mb-1 block text-sm font-semibold" >
                                人数
                            </label>

                            <select id="people" wire:model.live="people" class="w-full rounded-lg border border-slate-300 focus:border-blue-500 focus:ring-2 focus:ring-blue-100" >
                                <option value="">選択してください</option>
                                @for ($count = 1; $count <= $totalCapacity; $count++)
                                    <option value="{{ $count }}">{{ $count }}名</option>
                                @endfor
                            </select>

                            @error('people')

                                <p class="mt-1 text-xs text-red-600">
                                    {{ $message }}
                                </p>

                            @enderror

                        </div>

                        <div>
                            <label for="phone" class="mb-1 block font-semibold text-slate-700">
                                電話番号
                            </label>

                            <input id="phone" type="tel" wire:model="phone" inputmode="numeric" pattern="[0-9]*"
                                maxlength="11"
                                class="min-h-11 w-full rounded-md border border-slate-300 bg-white px-3 py-2 text-base text-slate-900 outline-none transition focus:border-blue-600 focus:ring-2 focus:ring-blue-100"
                                placeholder="09012345678">

                            @error('phone')
                                <p class="mt-1 text-sm text-red-600">
                                    {{ $message }}
                                </p>
                            @enderror
                        </div>

                        <div>

                            <label for="reservationDate" class="mb-1 block text-sm font-semibold" >
                                予約日
                            </label>

                            <input
                                id="reservationDate"
                                type="date"

                                wire:model="reservationDate"

                                class="w-full rounded-lg border border-slate-300 focus:border-blue-500 focus:ring-2 focus:ring-blue-100"
                            >

                            @error('reservationDate')

                                <p class="mt-1 text-xs text-red-600">
                                    {{ $message }}
                                </p>

                            @enderror

                        </div>

                        <div>

                            <label for="startTime" class="mb-1 block text-sm font-semibold" >
                                開始時間
                            </label>

                            <select id="startTime" wire:model="startTime" class="w-full rounded-lg border border-slate-300 focus:border-blue-500 focus:ring-2 focus:ring-blue-100" >

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

                                <p class="mt-1 text-xs text-red-600">
                                    {{ $message }}
                                </p>

                            @enderror

                        </div>

                        <div>

                            <label for="endTime" class="mb-1 block text-sm font-semibold" >
                                終了時間
                            </label>

                            <select id="endTime" wire:model="endTime" class="w-full rounded-lg border border-slate-300 focus:border-blue-500 focus:ring-2 focus:ring-blue-100" >

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

                                <p class="mt-1 text-xs text-red-600">
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

                                <label class="flex cursor-pointer items-center gap-2 rounded-lg border border-slate-200 p-3 transition hover:bg-slate-50" >

                                    <input
                                        type="checkbox"

                                        wire:model="selectedSeatIds"

                                        value="{{ $seat->id }}"

                                        class="rounded border border-slate-300 focus:border-blue-500 focus:ring-2 focus:ring-blue-100"
                                    >

                                    <span class="text-sm">

                                        {{ $seat->seat_name }}

                                        <span class="text-slate-400">
                                            （{{ $seat->capacity }}名）
                                        </span>

                                    </span>

                                </label>

                            @endforeach

                        </div>

                        @error('selectedSeatIds')
                            <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                        @enderror

                        @error('selectedSeatIds.*')
                            <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                        @enderror

                            <div class="mt-3">
                                <button
                                    type="button"
                                    wire:click="autoAssignSeats"
                                    wire:loading.attr="disabled"
                                    @disabled(!$people || !$reservationDate || !$startTime || !$endTime)
                                    class="rounded-lg bg-emerald-600 px-4 py-2 text-sm font-semibold text-white hover:bg-emerald-700 disabled:cursor-not-allowed disabled:opacity-50"
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
                            <div class="mt-3 rounded-lg bg-blue-50 p-3 text-sm text-blue-900">
                                <span class="font-semibold">選択中：</span>
                                {{ $selectedSeats->pluck('seat_name')->join(' / ') }}
                                （定員合計 {{ $selectedSeats->sum('capacity') }}名）
                            </div>
                        @endif

                    </div>

                    <div class="mt-5">

                        <label for="description" class="mb-1 block text-sm font-semibold" >
                            備考
                        </label>

                        <textarea
                            id="description"

                            wire:model="description"

                            rows="3"

                            class="w-full rounded-lg border border-slate-300 focus:border-blue-500 focus:ring-2 focus:ring-blue-100"
                        ></textarea>

                    </div>

                </div>

                <div class="flex shrink-0 justify-end gap-3 border-t border-slate-200 px-6 py-4" >

                    <button
                        type="button"

                        wire:click="closeModal"

                        class="rounded-lg border border border-slate-300 focus:border-blue-500 focus:ring-2 focus:ring-blue-100 px-5 py-2 font-semibold text-slate-600 hover:bg-slate-50"
                    >
                        キャンセル
                    </button>

                    @if ($showCreateModal)

                        <button
                            type="button"

                            wire:click="createReservation"

                            wire:loading.attr="disabled"

                            class="rounded-lg bg-blue-700 px-5 py-2 font-semibold text-white hover:bg-blue-800 disabled:opacity-50"
                        >
                            予約登録
                        </button>

                    @else

                        <button
                            type="button"

                            wire:click="updateReservation"

                            wire:loading.attr="disabled"

                            class="rounded-lg bg-blue-700 px-5 py-2 font-semibold text-white hover:bg-blue-800 disabled:opacity-50"
                        >
                            更新
                        </button>

                        <button
                            type="button"

                            wire:click="deleteReservation({{ $editingReservationId }})"

                            wire:confirm="この予約を削除します。よろしいですか？"

                            wire:loading.attr="disabled"

                            class="rounded-lg bg-red-600 px-5 py-2 font-semibold text-white hover:bg-red-700 disabled:opacity-50"
                        >
                            削除
                        </button>

                    @endif

                </div>

            </div>

        </div>

    @endif

</div>
