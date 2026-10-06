<div class="
        rounded-xl
        border border-blue-200
        bg-white
        shadow-sm
    ">

    {{-- ============================================================
         ヘッダー
    ============================================================= --}}
    <div
        class="
            flex
            items-center
            justify-between
            border-b border-blue-100
            bg-blue-50
            px-5 py-4
        ">

        <div>

            <h2 class="font-bold text-blue-900">
                予約編集
            </h2>

            <p class="mt-1 text-xs text-blue-600">
                予約内容を変更できます
            </p>

        </div>


        {{-- 閉じる --}}
        <button type="button" wire:click="close"
            class="
                rounded-lg
                px-3 py-2
                text-sm
                text-slate-500
                transition
                hover:bg-white
                hover:text-slate-800
            "
            title="編集を閉じる">
            ✕
        </button>

    </div>


    {{-- ============================================================
         更新完了
    ============================================================= --}}
    @if (session()->has('message'))
        <div
            class="
                mx-5 mt-5
                rounded-lg
                bg-green-50
                px-4 py-3
                text-sm
                text-green-700
            ">
            {{ session('message') }}
        </div>
    @endif


    {{-- ============================================================
         編集フォーム
    ============================================================= --}}
    <div class="space-y-5 p-5">


        {{-- お客様名 --}}
        <div>

            <label for="quickCustomerName"
                class="
                    mb-1
                    block
                    text-sm font-semibold
                    text-slate-700
                ">
                お客様名
            </label>

            <input id="quickCustomerName" type="text" wire:model="customerName"
                class="
                    w-full
                    rounded-lg
                    border border-slate-300
                    px-3 py-2
                ">

            @error('customerName')
                <p class="mt-1 text-xs text-red-600">
                    {{ $message }}
                </p>
            @enderror

        </div>


        {{-- 人数 --}}
        <div>

            <label for="quickPeople"
                class="
                    mb-1
                    block
                    text-sm font-semibold
                    text-slate-700
                ">
                人数
            </label>

            <select id="quickPeople" wire:model="people"
                class="
                    w-full
                    rounded-lg
                    border border-slate-300
                    px-3 py-2
                ">

                @for ($i = 1; $i <= $totalCapacity; $i++)
                    <option value="{{ $i }}">
                        {{ $i }}名
                    </option>
                @endfor

            </select>

            @error('people')
                <p class="mt-1 text-xs text-red-600">
                    {{ $message }}
                </p>
            @enderror

        </div>


        {{-- 電話番号 --}}
        <div>

            <label for="quickPhone"
                class="
                    mb-1
                    block
                    text-sm font-semibold
                    text-slate-700
                ">
                電話番号
            </label>

            <input id="quickPhone" type="text" wire:model="phone"
                class="
                    w-full
                    rounded-lg
                    border border-slate-300
                    px-3 py-2
                ">

        </div>


        {{-- 予約日 --}}
        <div>

            <label for="quickReservationDate"
                class="
                    mb-1
                    block
                    text-sm font-semibold
                    text-slate-700
                ">
                予約日
            </label>

            <input id="quickReservationDate" type="date" wire:model="reservationDate"
                class="
                    w-full
                    rounded-lg
                    border border-slate-300
                    px-3 py-2
                ">

        </div>


        {{-- 席 --}}
        <div>

            <p
                class="
                    mb-2
                    text-sm font-semibold
                    text-slate-700
                ">
                席
            </p>


            <div class="grid grid-cols-2 gap-2">

                @foreach ($seats as $seatItem)
                    <label
                        class="
                            flex
                            cursor-pointer
                            items-center
                            gap-2
                            rounded-lg
                            border border-slate-200
                            p-2
                            text-sm
                            hover:bg-slate-50
                        ">

                        <input type="checkbox" wire:model="selectedSeatIds" value="{{ $seatItem->id }}" class="rounded">

                        <span>
                            {{ $seatItem->seat_name }}
                        </span>

                    </label>
                @endforeach

            </div>

            @error('selectedSeatIds')
                <p class="mt-1 text-xs text-red-600">
                    {{ $message }}
                </p>
            @enderror

        </div>


        {{-- 開始・終了 --}}
        <div class="grid grid-cols-2 gap-3">

            {{-- 開始 --}}
            <div>

                <label
                    class="
                        mb-1
                        block
                        text-sm font-semibold
                        text-slate-700
                    ">
                    開始時間
                </label>

                <input type="time" step="900" wire:model="startTime"
                    class="
                        w-full
                        rounded-lg
                        border border-slate-300
                        px-3 py-2
                    ">

            </div>


            {{-- 終了 --}}
            <div>

                <label
                    class="
                        mb-1
                        block
                        text-sm font-semibold
                        text-slate-700
                    ">
                    終了時間
                </label>

                <input type="time" step="900" wire:model="endTime"
                    class="
                        w-full
                        rounded-lg
                        border border-slate-300
                        px-3 py-2
                    ">

            </div>

        </div>


        {{-- 状態 --}}
        <div>

            <label
                class="
                    mb-1
                    block
                    text-sm font-semibold
                    text-slate-700
                ">
                状態
            </label>

            <select wire:model="status"
                class="
                    w-full
                    rounded-lg
                    border border-slate-300
                    px-3 py-2
                ">

                <option value="temporary">
                    仮予約
                </option>

                <option value="reserved">
                    確定
                </option>

                <option value="cancelled">
                    キャンセル
                </option>

            </select>

        </div>


        {{-- 備考 --}}
        <div>

            <label
                class="
                    mb-1
                    block
                    text-sm font-semibold
                    text-slate-700
                ">
                備考
            </label>

            <textarea wire:model="description" rows="3"
                class="
                    w-full
                    rounded-lg
                    border border-slate-300
                    px-3 py-2
                "></textarea>

        </div>


        {{-- ========================================================
             操作
        ========================================================= --}}
        <div class="flex gap-3 pt-2">

            <button type="button" wire:click="update" wire:loading.attr="disabled"
                class="
                    flex-1
                    rounded-lg
                    bg-blue-600
                    px-4 py-3
                    font-semibold
                    text-white
                    transition
                    hover:bg-blue-700
                    disabled:opacity-50
                ">
                更新
            </button>


            <button type="button" wire:click="close"
                class="
                    rounded-lg
                    border border-slate-300
                    bg-white
                    px-4 py-3
                    font-semibold
                    text-slate-600
                    transition
                    hover:bg-slate-50
                ">
                閉じる
            </button>

        </div>

    </div>

</div>
