<div class="min-h-screen bg-slate-50">

    {{-- ================================================================
         TOPページ
    ================================================================= --}}
    <div class="mx-auto max-w-[1800px] px-4 py-4">

        {{-- ============================================================
             タイトル行

             左：予約管理
             右：＋新規予約
        ============================================================= --}}
        <div
            class="
                mb-4
                flex
                items-center
                justify-between
                gap-4
            "
        >

            <h1
                class="
                    text-xl
                    font-bold
                    text-blue-900
                "
            >
                予約管理
            </h1>


            {{-- ========================================================
                 新規予約ボタン
            ========================================================= --}}
            <button
                type="button"

                wire:click="openCreateModalFromButton"

                class="
                    inline-flex
                    items-center
                    gap-1
                    rounded-lg
                    bg-blue-700
                    px-4 py-2
                    text-sm
                    font-semibold
                    text-white
                    shadow-sm
                    transition

                    hover:bg-blue-800

                    focus:outline-none
                    focus:ring-2
                    focus:ring-blue-300
                "
            >

                <span class="text-lg leading-none">
                    ＋
                </span>

                新規予約

            </button>

        </div>


        {{-- ============================================================
             メイン

             左：予約状況
             右：選択日の予約一覧

             高さを固定しないため、
             ページ全体でスクロールできます。
        ============================================================= --}}
        <div
            class="
                grid
                grid-cols-1
                items-start
                gap-4
                lg:grid-cols-12
            "
        >

            {{-- ========================================================
                 左：予約状況
            ========================================================= --}}
            <section class="lg:col-span-9">

                <div
                    class="
                        rounded-xl
                        border
                        border-slate-200
                        bg-white
                        p-4
                        shadow-sm
                    "
                >

                    <livewire:reservation-status />

                </div>

            </section>


            {{-- ========================================================
                 右：予約一覧
            ========================================================= --}}
            <section class="lg:col-span-3">

                <div
                    class="
                        rounded-xl
                        border
                        border-slate-200
                        bg-white
                        shadow-sm
                    "
                >

                    {{-- =================================================
                         一覧タイトル
                    ================================================== --}}
                    <div
                        class="
                            border-b
                            border-slate-200
                            px-4 py-3
                        "
                    >

                        <h2
                            class="
                                font-bold
                                text-blue-900
                            "
                        >

                            {{ \Carbon\Carbon::parse($selectedDate)->format('m月d日') }}
                            の予約

                        </h2>

                    </div>


                    {{-- =================================================
                         予約一覧

                         内部スクロールは使用しません。
                    ================================================== --}}
                    <div class="space-y-2 p-3">

                        @forelse ($todayReservations as $reservation)

                            <button
                                type="button"

                                wire:click="
                                    openEditModal(
                                        {{ $reservation->id }}
                                    )
                                "

                                wire:key="
                                    reservation-list-{{ $reservation->id }}
                                "

                                class="
                                    block
                                    w-full
                                    rounded-lg
                                    border
                                    border-slate-200
                                    bg-white
                                    p-3
                                    text-left
                                    transition

                                    hover:border-blue-400
                                    hover:bg-blue-50
                                "
                            >

                                {{-- =========================================
                                     時間・人数
                                ========================================== --}}
                                <div
                                    class="
                                        flex
                                        items-center
                                        justify-between
                                        gap-2
                                    "
                                >

                                    <span
                                        class="
                                            font-bold
                                            text-slate-800
                                        "
                                    >

                                        {{ substr($reservation->start_time, 0, 5) }}

                                        ～

                                        {{ substr($reservation->end_time, 0, 5) }}

                                    </span>


                                    <span
                                        class="
                                            whitespace-nowrap
                                            text-xs
                                            text-slate-500
                                        "
                                    >

                                        {{ $reservation->people }}名

                                    </span>

                                </div>


                                {{-- =========================================
                                     名前
                                ========================================== --}}
                                <div
                                    class="
                                        mt-1
                                        font-semibold
                                        text-blue-900
                                    "
                                >

                                    {{ $reservation->customer_name }}

                                </div>


                                {{-- =========================================
                                     席
                                ========================================== --}}
                                <div
                                    class="
                                        mt-1
                                        text-xs
                                        text-slate-500
                                    "
                                >

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

                        @empty

                            <div
                                class="
                                    py-10
                                    text-center
                                    text-sm
                                    text-slate-400
                                "
                            >

                                この日の予約はありません。

                            </div>

                        @endforelse

                    </div>

                </div>

            </section>

        </div>

    </div>


    {{-- ================================================================
         新規予約 / 編集モーダル
    ================================================================= --}}
    @if ($showCreateModal || $showEditModal)

        <div
            class="
                fixed
                inset-0
                z-50
                flex
                items-center
                justify-center
                bg-black/40
                p-4
                backdrop-blur-sm
            "
        >

            <div
                class="
                    flex
                    max-h-[90vh]
                    w-full
                    max-w-3xl
                    flex-col
                    overflow-hidden
                    rounded-2xl
                    bg-white
                    shadow-2xl
                "
            >

                {{-- ====================================================
                     モーダルヘッダー
                ===================================================== --}}
                <div
                    class="
                        flex
                        shrink-0
                        items-center
                        justify-between
                        border-b
                        border-slate-200
                        px-6 py-4
                    "
                >

                    <h2
                        class="
                            text-xl
                            font-bold
                            text-blue-900
                        "
                    >

                        @if ($showCreateModal)
                            新規予約
                        @else
                            予約編集
                        @endif

                    </h2>


                    <button
                        type="button"

                        wire:click="closeModal"

                        class="
                            flex
                            h-9
                            w-9
                            items-center
                            justify-center
                            rounded-full
                            text-xl
                            text-slate-500
                            transition

                            hover:bg-slate-100
                        "
                    >
                        ×
                    </button>

                </div>


                {{-- ====================================================
                     フォーム本体

                     モーダル内部だけは、
                     小さい画面で入力欄が入りきらない場合に
                     スクロール可能にします。
                ===================================================== --}}
                <div
                    class="
                        min-h-0
                        flex-1
                        overflow-y-auto
                        p-6
                    "
                >

                    <div
                        class="
                            grid
                            grid-cols-1
                            gap-4
                            md:grid-cols-2
                        "
                    >

                        {{-- =================================================
                             お客様名
                        ================================================== --}}
                        <div>

                            <label
                                for="customerName"
                                class="
                                    mb-1
                                    block
                                    text-sm
                                    font-semibold
                                "
                            >
                                お客様名
                            </label>


                            <input
                                id="customerName"
                                type="text"

                                wire:model="customerName"

                                class="
                                    w-full
                                    rounded-lg
                                    border-slate-300
                                "
                            >


                            @error('customerName')

                                <p class="mt-1 text-xs text-red-600">
                                    {{ $message }}
                                </p>

                            @enderror

                        </div>


                        {{-- =================================================
                             人数
                        ================================================== --}}
                        <div>

                            <label
                                for="people"
                                class="
                                    mb-1
                                    block
                                    text-sm
                                    font-semibold
                                "
                            >
                                人数
                            </label>


                            <input
                                id="people"
                                type="number"
                                min="1"

                                wire:model="people"

                                class="
                                    w-full
                                    rounded-lg
                                    border-slate-300
                                "
                            >


                            @error('people')

                                <p class="mt-1 text-xs text-red-600">
                                    {{ $message }}
                                </p>

                            @enderror

                        </div>


                        {{-- =================================================
                             電話番号
                        ================================================== --}}
                        <div>

                            <label
                                for="phone"
                                class="
                                    mb-1
                                    block
                                    text-sm
                                    font-semibold
                                "
                            >
                                電話番号
                            </label>


                            <input
                                id="phone"
                                type="text"

                                wire:model="phone"

                                class="
                                    w-full
                                    rounded-lg
                                    border-slate-300
                                "
                            >


                            @error('phone')

                                <p class="mt-1 text-xs text-red-600">
                                    {{ $message }}
                                </p>

                            @enderror

                        </div>


                        {{-- =================================================
                             予約日
                        ================================================== --}}
                        <div>

                            <label
                                for="reservationDate"
                                class="
                                    mb-1
                                    block
                                    text-sm
                                    font-semibold
                                "
                            >
                                予約日
                            </label>


                            <input
                                id="reservationDate"
                                type="date"

                                wire:model="reservationDate"

                                class="
                                    w-full
                                    rounded-lg
                                    border-slate-300
                                "
                            >


                            @error('reservationDate')

                                <p class="mt-1 text-xs text-red-600">
                                    {{ $message }}
                                </p>

                            @enderror

                        </div>


                        {{-- =================================================
                             開始時間

                             営業時間内だけを
                             15分刻みで表示します。
                        ================================================== --}}
                        <div>

                            <label
                                for="startTime"
                                class="
                                    mb-1
                                    block
                                    text-sm
                                    font-semibold
                                "
                            >
                                開始時間
                            </label>


                            <select
                                id="startTime"

                                wire:model="startTime"

                                class="
                                    w-full
                                    rounded-lg
                                    border-slate-300
                                "
                            >

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


                        {{-- =================================================
                             終了時間
                        ================================================== --}}
                        <div>

                            <label
                                for="endTime"
                                class="
                                    mb-1
                                    block
                                    text-sm
                                    font-semibold
                                "
                            >
                                終了時間
                            </label>


                            <select
                                id="endTime"

                                wire:model="endTime"

                                class="
                                    w-full
                                    rounded-lg
                                    border-slate-300
                                "
                            >

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


                    {{-- =================================================
                         席選択
                    ================================================== --}}
                    <div class="mt-5">

                        <label
                            class="
                                mb-2
                                block
                                text-sm
                                font-semibold
                            "
                        >
                            席
                        </label>


                        <div
                            class="
                                grid
                                grid-cols-2
                                gap-2
                                md:grid-cols-3
                            "
                        >

                            @foreach ($seats as $seat)

                                <label
                                    class="
                                        flex
                                        cursor-pointer
                                        items-center
                                        gap-2
                                        rounded-lg
                                        border
                                        border-slate-200
                                        p-3
                                        transition

                                        hover:bg-slate-50
                                    "
                                >

                                    <input
                                        type="checkbox"

                                        wire:model="selectedSeatIds"

                                        value="{{ $seat->id }}"

                                        class="
                                            rounded
                                            border-slate-300
                                        "
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

                            <p class="mt-1 text-xs text-red-600">
                                {{ $message }}
                            </p>

                        @enderror

                    </div>


                    {{-- =================================================
                         状態

                         編集時のみ表示
                    ================================================== --}}
                    @if ($showEditModal)

                        <div class="mt-5">

                            <label
                                for="status"
                                class="
                                    mb-1
                                    block
                                    text-sm
                                    font-semibold
                                "
                            >
                                状態
                            </label>


                            <select
                                id="status"

                                wire:model="status"

                                class="
                                    w-full
                                    rounded-lg
                                    border-slate-300
                                "
                            >

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

                    @endif


                    {{-- =================================================
                         備考
                    ================================================== --}}
                    <div class="mt-5">

                        <label
                            for="description"
                            class="
                                mb-1
                                block
                                text-sm
                                font-semibold
                            "
                        >
                            備考
                        </label>


                        <textarea
                            id="description"

                            wire:model="description"

                            rows="3"

                            class="
                                w-full
                                rounded-lg
                                border-slate-300
                            "
                        ></textarea>

                    </div>

                </div>


                {{-- ====================================================
                     モーダル下部
                ===================================================== --}}
                <div
                    class="
                        flex
                        shrink-0
                        justify-end
                        gap-3
                        border-t
                        border-slate-200
                        px-6 py-4
                    "
                >

                    <button
                        type="button"

                        wire:click="closeModal"

                        class="
                            rounded-lg
                            border
                            border-slate-300
                            px-5 py-2
                            font-semibold
                            text-slate-600

                            hover:bg-slate-50
                        "
                    >
                        キャンセル
                    </button>


                    @if ($showCreateModal)

                        <button
                            type="button"

                            wire:click="createReservation"

                            wire:loading.attr="disabled"

                            class="
                                rounded-lg
                                bg-blue-700
                                px-5 py-2
                                font-semibold
                                text-white

                                hover:bg-blue-800

                                disabled:opacity-50
                            "
                        >
                            予約登録
                        </button>

                    @else

                        <button
                            type="button"

                            wire:click="updateReservation"

                            wire:loading.attr="disabled"

                            class="
                                rounded-lg
                                bg-blue-700
                                px-5 py-2
                                font-semibold
                                text-white

                                hover:bg-blue-800

                                disabled:opacity-50
                            "
                        >
                            更新
                        </button>

                    @endif

                </div>

            </div>

        </div>

    @endif

</div>