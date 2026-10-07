<div class="min-h-screen bg-slate-50 text-base leading-relaxed text-slate-800">

    <x-header />

    <main class="mx-auto w-full max-w-screen-2xl px-4 py-4 sm:px-6 lg:px-8 lg:py-5">

        {{-- タイトル --}}
        <div class="mb-4 overflow-hidden rounded-xl border border-blue-100 bg-white shadow-sm">
            <div class="bg-blue-50 px-4 py-3">
                <h1 class="text-xl font-bold text-blue-950">
                    ✏️ 予約編集
                </h1>
            </div>
        </div>

        {{-- メッセージ --}}
        @if (session()->has('message'))
            <div class="mb-4 rounded-lg border border-green-200 bg-green-50 px-4 py-2.5 text-sm text-green-700">
                {{ session('message') }}
            </div>
        @endif

        {{-- 編集フォーム --}}
        <div class="overflow-hidden rounded-xl border border-blue-100 bg-white p-4 shadow-sm sm:p-5">

            {{-- 基本情報 --}}
            <div class="mb-4">
                <h2 class="border-b border-blue-100 bg-blue-50 px-3 py-2 font-bold text-blue-950">
                    予約情報
                </h2>
            </div>

            {{-- 基本項目：2列 --}}
            <div class="grid grid-cols-1 gap-x-6 gap-y-3 md:grid-cols-2">

                {{-- お客様名 --}}
                <div>
                    <label for="customerName" class="mb-1 block font-semibold text-slate-700">
                        お客様名
                    </label>

                    <input id="customerName" type="text" wire:model="customerName"
                        class="min-h-11 w-full rounded-md border border-slate-300 bg-white px-3 py-2 text-base text-slate-900 outline-none transition focus:border-blue-600 focus:ring-2 focus:ring-blue-100">

                    @error('customerName')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                {{-- 人数 --}}
                <div>
                    <label for="people" class="mb-1 block font-semibold text-slate-700">
                        人数
                    </label>

                    <select id="people" wire:model="people"
                        class="min-h-11 w-full rounded-md border border-slate-300 bg-white px-3 py-2 text-base text-slate-900 outline-none transition focus:border-blue-600 focus:ring-2 focus:ring-blue-100">
                        <option value="">選択してください</option>

                        @for ($i = 1; $i <= $totalCapacity; $i++)
                            <option value="{{ $i }}">
                                {{ $i }}名
                            </option>
                        @endfor
                    </select>

                    @error('people')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                {{-- 電話番号 --}}
                <div>
                    <label for="phone" class="mb-1 block font-semibold text-slate-700">
                        電話番号
                    </label>

                    <input id="phone" type="text" wire:model="phone"
                        class="min-h-11 w-full rounded-md border border-slate-300 bg-white px-3 py-2 text-base text-slate-900 outline-none transition focus:border-blue-600 focus:ring-2 focus:ring-blue-100">

                    @error('phone')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                {{-- 予約日 --}}
                <div>
                    <label for="reservationDate" class="mb-1 block font-semibold text-slate-700">
                        予約日
                    </label>

                    <input id="reservationDate" type="date" wire:model="reservationDate"
                        class="min-h-11 w-full rounded-md border border-slate-300 bg-white px-3 py-2 text-base text-slate-900 outline-none transition focus:border-blue-600 focus:ring-2 focus:ring-blue-100">

                    @error('reservationDate')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                {{-- 開始時間 --}}
                <div>
                    <label for="startTime" class="mb-1 block font-semibold text-slate-700">
                        開始時間
                    </label>

                    <select id="startTime" wire:model="startTime"
                        class="min-h-11 w-full rounded-md border border-slate-300 bg-white px-3 py-2 text-base text-slate-900 outline-none transition focus:border-blue-600 focus:ring-2 focus:ring-blue-100">
                        <option value="">選択してください</option>

                        @for ($hour = 12; $hour <= 21; $hour++)
                            @for ($minute = 0; $minute < 60; $minute += 15)
                                @php
                                    $time = sprintf('%02d:%02d', $hour, $minute);
                                @endphp

                                <option value="{{ $time }}">
                                    {{ $time }}
                                </option>
                            @endfor
                        @endfor

                        <option value="22:00">22:00</option>
                    </select>

                    @error('startTime')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                {{-- 終了時間 --}}
                <div>
                    <label for="endTime" class="mb-1 block font-semibold text-slate-700">
                        終了時間
                    </label>

                    <select id="endTime" wire:model="endTime"
                        class="min-h-11 w-full rounded-md border border-slate-300 bg-white px-3 py-2 text-base text-slate-900 outline-none transition focus:border-blue-600 focus:ring-2 focus:ring-blue-100">
                        <option value="">選択してください</option>

                        @for ($hour = 12; $hour <= 21; $hour++)
                            @for ($minute = 0; $minute < 60; $minute += 15)
                                @php
                                    $time = sprintf('%02d:%02d', $hour, $minute);
                                @endphp

                                <option value="{{ $time }}">
                                    {{ $time }}
                                </option>
                            @endfor
                        @endfor

                        <option value="22:00">22:00</option>
                    </select>

                    @error('endTime')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                {{-- 状態 --}}
                <div>
                    <label for="status" class="mb-1 block font-semibold text-slate-700">
                        状態
                    </label>

                    <select id="status" wire:model="status"
                        class="min-h-11 w-full rounded-md border border-slate-300 bg-white px-3 py-2 text-base text-slate-900 outline-none transition focus:border-blue-600 focus:ring-2 focus:ring-blue-100">
                        <option value="temporary">仮予約</option>
                        <option value="reserved">確定</option>
                        <option value="visited">来店済</option>
                        <option value="paid">会計済</option>
                        <option value="cancelled">キャンセル</option>
                    </select>

                    @error('status')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

            </div>

            {{-- 席 --}}
            <div class="mt-4 border-t border-slate-200 pt-4">

                <h2 class="mb-3 border-b border-blue-100 bg-blue-50 px-3 py-2 font-bold text-blue-950">
                    席選択
                </h2>

                {{-- 席一覧 --}}
                <div class="grid grid-cols-1 gap-2 sm:grid-cols-2 lg:grid-cols-3">

                    @foreach ($seats as $seatItem)
                        <label
                            class="flex cursor-pointer items-center gap-2 rounded-md border border-slate-300 bg-white px-3 py-2.5 transition hover:border-blue-300 hover:bg-blue-50">
                            <input type="checkbox" wire:model="selectedSeatIds" value="{{ $seatItem->id }}"
                                class="h-4 w-4 rounded border-slate-300 text-blue-600 focus:ring-blue-500">

                            <span class="font-medium text-slate-700">
                                {{ $seatItem->seat_name }}
                                <span class="text-sm text-slate-500">
                                    （{{ $seatItem->capacity }}名）
                                </span>
                            </span>
                        </label>
                    @endforeach

                </div>

                @error('selectedSeatIds')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror

                @error('selectedSeatIds.*')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror

                {{-- 自動配置 --}}
                <div class="mt-3">
                    <button type="button" wire:click="autoAssignSeats" wire:loading.attr="disabled"
                        class="inline-flex min-h-10 items-center justify-center rounded-md bg-blue-600 px-5 py-2 text-sm font-semibold text-white transition hover:bg-blue-700 focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-blue-500 focus-visible:ring-offset-2 disabled:cursor-not-allowed disabled:opacity-50">
                        🪑 自動配置
                    </button>
                </div>

                {{-- 自動配置エラー --}}
                @if ($autoAssignError)
                    <div class="mt-2 rounded-md border border-red-200 bg-red-50 px-3 py-2">
                        <p class="text-sm text-red-600">
                            {{ $autoAssignError }}
                        </p>
                    </div>
                @endif

                {{-- 現在選択されている席 --}}
                @if (!empty($selectedSeats))
                    <div class="mt-2 rounded-md border border-blue-100 bg-blue-50 px-3 py-2">

                        <p class="font-semibold text-blue-900">
                            選択中の席
                        </p>

                        <div class="mt-1 flex flex-wrap gap-x-4 gap-y-1">
                            @foreach ($selectedSeats as $selectedSeat)
                                <span class="text-sm text-slate-700">
                                    ・{{ $selectedSeat->seat_name }}
                                    （{{ $selectedSeat->capacity }}名）
                                </span>
                            @endforeach
                        </div>

                    </div>
                @endif

            </div>

            {{-- 備考 --}}
            <div class="mt-4 border-t border-slate-200 pt-4">

                <label for="description" class="mb-1 block font-semibold text-slate-700">
                    備考
                </label>

                <textarea id="description" wire:model="description" rows="2"
                    class="w-full rounded-md border border-slate-300 bg-white px-3 py-2 text-base text-slate-900 outline-none transition focus:border-blue-600 focus:ring-2 focus:ring-blue-100"
                    placeholder="アレルギー、ご要望など"></textarea>

                @error('description')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror

            </div>

            {{-- ボタン --}}
            <div class="mt-4 flex flex-wrap justify-center gap-2 border-t border-slate-200 pt-4">

                {{-- 更新 --}}
                <button type="button" wire:click="update" wire:loading.attr="disabled"
                    class="inline-flex min-h-11 items-center justify-center rounded-md bg-blue-600 px-7 py-2 text-sm font-semibold text-white transition hover:bg-blue-700 focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-blue-500 focus-visible:ring-offset-2 disabled:cursor-not-allowed disabled:opacity-50">
                    更新
                </button>

                {{-- 変更を元に戻す --}}
                <button type="button" wire:click="clear" wire:confirm="編集前の状態に戻しますか？"
                    class="inline-flex min-h-11 items-center justify-center gap-2 rounded-md border border-slate-300 bg-white px-6 py-2 text-sm font-semibold text-slate-700 transition hover:bg-slate-100 focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-blue-500 focus-visible:ring-offset-2">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 shrink-0" viewBox="0 0 24 24"
                        fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                        stroke-linejoin="round" aria-hidden="true">
                        <path d="M3 10a9 9 0 1 1 2.6 8.4M3 4v6h6" />
                    </svg>

                    クリア
                </button>

                {{-- 一覧に戻る --}}
                <a href="{{ route('reservations.index') }}"
                    class="inline-flex min-h-11 items-center justify-center rounded-md border border-slate-300 bg-white px-6 py-2 text-sm font-semibold text-slate-700 transition hover:bg-slate-100 focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-blue-500 focus-visible:ring-offset-2">
                    戻る
                </a>

            </div>

        </div>

    </main>

    <x-footer />

</div>
