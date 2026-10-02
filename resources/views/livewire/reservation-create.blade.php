<div class="max-w-4xl mx-auto p-6">

    {{-- タイトル --}}
    <div class="bg-blue-50 border-b border-gray-200 px-4 py-3 mb-6">
        <h1 class="text-xl font-bold text-blue-700">
            ＋ 新規予約登録
        </h1>
    </div>


    {{-- 登録成功メッセージ --}}
    @if (session()->has('message'))
        <div class="mb-4 rounded bg-green-100 px-4 py-3 text-green-700">
            {{ session('message') }}
        </div>
    @endif


    {{-- 入力フォーム --}}
    <div class="bg-white border rounded-lg p-6 shadow-sm">

        {{-- お客様名 --}}
        <div class="grid grid-cols-4 items-center gap-4 mb-4">
            <label for="customerName" class="font-semibold">
                お客様名
            </label>

            <div class="col-span-3">
                <input id="customerName" type="text" wire:model="customerName" class="w-full rounded border-gray-300"
                    placeholder="山田 太郎">

                @error('customerName')
                    <p class="mt-1 text-sm text-red-600">
                        {{ $message }}
                    </p>
                @enderror
            </div>
        </div>


        {{-- 人数 --}}
        <div class="grid grid-cols-4 items-center gap-4 mb-4">
            <label for="people" class="font-semibold">
                人数
            </label>

            <div class="col-span-3 flex items-center gap-2">
                <select id="people" wire:model="people" class="rounded border-gray-300">
                    <option value="">選択してください</option>

                    @for ($i = 1; $i <= 20; $i++)
                        <option value="{{ $i }}">
                            {{ $i }}名
                        </option>
                    @endfor
                </select>

                @error('people')
                    <p class="text-sm text-red-600">
                        {{ $message }}
                    </p>
                @enderror
            </div>
        </div>


        {{-- 電話番号 --}}
        <div class="grid grid-cols-4 items-center gap-4 mb-4">
            <label for="phone" class="font-semibold">
                電話番号
            </label>

            <div class="col-span-3">
                <input id="phone" type="text" wire:model="phone" class="w-full rounded border-gray-300"
                    placeholder="090-1234-5678">

                @error('phone')
                    <p class="mt-1 text-sm text-red-600">
                        {{ $message }}
                    </p>
                @enderror
            </div>
        </div>


        {{-- 予約日 --}}
        <div class="grid grid-cols-4 items-center gap-4 mb-4">
            <label for="reservationDate" class="font-semibold">
                予約日
            </label>

            <div class="col-span-3">
                <input id="reservationDate" type="date" wire:model="reservationDate" class="rounded border-gray-300">

                @error('reservationDate')
                    <p class="mt-1 text-sm text-red-600">
                        {{ $message }}
                    </p>
                @enderror
            </div>
        </div>

        {{-- 席 --}}
        <div class="grid grid-cols-4 items-start gap-4 mb-4">

            <label class="font-semibold">
                席
            </label>

            <div class="col-span-3">

                {{-- 手動で複数選択 --}}
                <div class="grid grid-cols-2 gap-3">

                    @foreach ($seats as $seatItem)
                        <label class="flex items-center gap-2 rounded border p-3 cursor-pointer hover:bg-gray-50">

                            <input type="checkbox" wire:model="selectedSeatIds" value="{{ $seatItem->id }}"
                                class="rounded border-gray-300">

                            <span>
                                {{ $seatItem->seat_name }}
                                （{{ $seatItem->capacity }}名）
                            </span>

                        </label>
                    @endforeach

                </div>

                {{-- 手動選択のエラー --}}
                @error('selectedSeatIds')
                    <p class="mt-1 text-sm text-red-600">
                        {{ $message }}
                    </p>
                @enderror

                @error('selectedSeatIds.*')
                    <p class="mt-1 text-sm text-red-600">
                        {{ $message }}
                    </p>
                @enderror

                {{-- 自動配置ボタン --}}
                <div class="mt-4">

                    <button type="button" wire:click="autoAssignSeats" wire:loading.attr="disabled"
                        class="rounded bg-green-600 px-5 py-2 font-semibold text-white hover:bg-green-700 disabled:opacity-50">
                        🪑 自動配置
                    </button>

                </div>

                {{-- 自動配置エラー --}}
                @if ($autoAssignError)
                    <div class="mt-3 rounded bg-red-50 p-3">
                        <p class="text-sm text-red-600">
                            {{ $autoAssignError }}
                        </p>
                    </div>
                @endif

                {{-- 現在選択されている席 --}}
                @if (!empty($selectedSeats))

                    <div class="mt-3 rounded bg-blue-50 p-3">

                        <p class="font-semibold text-blue-700">
                            選択中の席
                        </p>

                        <ul class="mt-2 list-disc pl-5">

                            @foreach ($selectedSeats as $selectedSeat)
                                <li>
                                    {{ $selectedSeat->seat_name }}
                                    （{{ $selectedSeat->capacity }}名）
                                </li>
                            @endforeach

                        </ul>

                    </div>

                @endif

            </div>

        </div>


        {{-- 開始時間 --}}
        <div class="grid grid-cols-4 items-center gap-4 mb-4">
            <label for="startTime" class="font-semibold">
                開始時間
            </label>

            <div class="col-span-3">
                <select id="startTime" wire:model="startTime" class="rounded border-gray-300">
                    <option value="">選択してください</option>

                    @if ($start && $end && $slotMinutes > 0)
                        @for ($time = $start->copy(); $time <= $end; $time->addMinutes($slotMinutes))
                            <option value="{{ $time->format('H:i') }}">
                                {{ $time->format('H:i') }}
                            </option>
                        @endfor
                    @endif
                </select>

                @error('startTime')
                    <p class="mt-1 text-sm text-red-600">
                        {{ $message }}
                    </p>
                @enderror
            </div>
        </div>


        {{-- 終了時間 --}}
        <div class="grid grid-cols-4 items-center gap-4 mb-4">
            <label for="endTime" class="font-semibold">
                終了時間
            </label>

            <div class="col-span-3">
                <select id="endTime" wire:model="endTime" class="rounded border-gray-300">
                    <option value="">選択してください</option>

                    @if ($start && $end && $slotMinutes > 0)
                        @for ($time = $start->copy(); $time <= $end; $time->addMinutes($slotMinutes))
                            <option value="{{ $time->format('H:i') }}">
                                {{ $time->format('H:i') }}
                            </option>
                        @endfor
                    @endif
                </select>

                @error('endTime')
                    <p class="mt-1 text-sm text-red-600">
                        {{ $message }}
                    </p>
                @enderror
            </div>
        </div>


        {{-- 備考 --}}
        <div class="grid grid-cols-4 items-start gap-4 mb-6">
            <label for="description" class="font-semibold pt-2">
                備考
            </label>

            <div class="col-span-3">
                <textarea id="description" wire:model="description" rows="3" class="w-full rounded border-gray-300"
                    placeholder="アレルギー、ご要望など"></textarea>

                @error('description')
                    <p class="mt-1 text-sm text-red-600">
                        {{ $message }}
                    </p>
                @enderror
            </div>
        </div>


        {{-- ボタン --}}
        <div class="flex justify-center gap-4">

            {{-- 登録 --}}
            <button type="button" wire:click="save" wire:loading.attr="disabled"
                class="rounded bg-blue-600 px-8 py-3 font-semibold text-white hover:bg-blue-700 disabled:opacity-50">
                登録
            </button>

            {{-- クリア --}}
            <button type="button" wire:click="clear"
                class="rounded border border-gray-300 bg-white px-8 py-3 font-semibold text-gray-700 hover:bg-gray-100">
                クリア
            </button>

            {{-- 一覧リンク --}}
            <div class="flex justify-center">
                <a href="{{ route('reservations.index') }}"
                    class="rounded border border-gray-300 bg-white px-8 py-3 font-semibold text-gray-700 hover:bg-gray-100">
                    一覧に戻る
                </a>
            </div>

        </div>

    </div>


</div>
