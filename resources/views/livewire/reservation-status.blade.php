<div class="w-full">

    {{-- 予約状況 --}}
    <div class="mb-6 flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
        <div>
            <h2 class="text-xl font-bold text-slate-800">
                予約状況
            </h2>

            <p class="mt-1 text-sm text-slate-500">
                席ごとの予約状況を確認できます
            </p>
        </div>


        {{-- 日付変更 --}}
        <div class="flex items-center gap-2">

            {{-- 前日 --}}
            <button
                type="button"
                wire:click="previousDay"
                class="
                    flex h-10 w-10
                    items-center justify-center
                    rounded-lg
                    border border-slate-200
                    bg-white
                    text-lg text-slate-600
                    shadow-sm
                    transition
                    hover:border-blue-300
                    hover:bg-blue-50
                    hover:text-blue-600
                    focus:outline-none
                    focus:ring-2
                    focus:ring-blue-100
                "
                title="前の日"
            >
                ←
            </button>


            <input
                type="date"
                wire:model.live="date"
                class="
                    h-10
                    cursor-pointer
                    rounded-lg
                    border border-slate-200
                    bg-white
                    px-4
                    text-sm font-semibold
                    text-slate-700
                    shadow-sm
                    outline-none
                    transition
                    hover:border-blue-300
                    focus:border-blue-500
                    focus:ring-2
                    focus:ring-blue-100
                "
            >


            {{-- 翌日 --}}
            <button
                type="button"
                wire:click="nextDay"
                class="
                    flex h-10 w-10
                    items-center justify-center
                    rounded-lg
                    border border-slate-200
                    bg-white
                    text-lg text-slate-600
                    shadow-sm
                    transition
                    hover:border-blue-300
                    hover:bg-blue-50
                    hover:text-blue-600
                    focus:outline-none
                    focus:ring-2
                    focus:ring-blue-100
                "
                title="次の日"
            >
                →
            </button>

        </div>

    </div>



    {{-- 予約状況表 --}}
    <div
        class="
            overflow-hidden
            rounded-xl
            border border-slate-200
            bg-white
        "
    >

        {{--時間数が増えた場合は、予約状況部分だけ横スクロール--}}
        <div class="overflow-x-auto">

            <div class="min-w-max p-4">
                {{-- 時間ヘッダー --}}

                <div
                    class="mb-2 grid gap-2 text-center"
                    style="
                        grid-template-columns:
                        80px repeat({{ count($times) }}, 80px);">
                    {{-- 席 --}}
                    <div
                        class="
                            flex h-10
                            items-center justify-center
                            rounded-md
                            bg-slate-100
                            text-xs font-bold
                            text-slate-500
                        "
                    >
                        席
                    </div>


                    {{-- 時間 --}}
                    @foreach ($times as $time)

                        <div
                            class="
                                flex h-10
                                items-center justify-center
                                rounded-md
                                bg-slate-100
                                text-xs font-semibold
                                text-slate-600
                            "
                        >
                            {{ $time }}
                        </div>

                    @endforeach

                </div>


                {{-- ==================================================
                     席ごとの予約状況
                =================================================== --}}
                <div class="space-y-2">

                    @foreach ($seats as $seat)

                        {{--
                            1列目：
                            席名

                            2列目以降：
                            時間枠
                        --}}

                        <div
                            class="grid gap-2"
                            style="
                                grid-template-columns:
                                80px repeat({{ count($times) }}, 80px);
                            "
                        >


                            {{-- 席名--}}
                            <div
                                class="
                                    flex h-12
                                    items-center justify-center
                                    rounded-md
                                    border border-slate-200
                                    bg-slate-50
                                    px-2
                                    text-center
                                    text-sm font-bold
                                    text-slate-700" >
                                {{ $seat }}
                            </div>


                            {{-- 時間枠 --}}
                            @foreach ($times as $time)

                                @php

                                    $foundReservation = null;

                                    $isOccupied = false;

                                @endphp


                                {{-- ==================================
                                     この席・時間に該当する予約を検索
                                =================================== --}}
                                @foreach ($reservations as $reservation)


                                    {{-- =================================
                                         この時間から予約が始まる
                                    ================================== --}}
                                    @if (
                                        $seat === $reservation['seat']
                                        && $time === $reservation['start_time']
                                    )

                                        @php

                                            $foundReservation = $reservation;

                                        @endphp


                                    {{-- =================================
                                         前の時間から始まった予約に
                                         この時間が含まれている
                                    ================================== --}}
                                    @elseif (
                                        $seat === $reservation['seat']
                                        && $time > $reservation['start_time']
                                        && $time < $reservation['end_time']
                                    )

                                        @php

                                            $isOccupied = true;

                                        @endphp

                                    @endif


                                @endforeach


                                {{-- ==================================
                                     予約あり
                                =================================== --}}
                                @if ($foundReservation)
                                    <div
                                        class="
                                            flex h-12
                                            items-center
                                            overflow-hidden
                                            rounded-md
                                            border border-blue-200
                                            bg-blue-50
                                            px-3
                                            text-sm
                                            shadow-sm
                                            transition
                                            hover:border-blue-300
                                            hover:bg-blue-100
                                        "
                                        style="
                                            grid-column:
                                            span {{ $foundReservation['span'] }}
                                            /
                                            span {{ $foundReservation['span'] }};
                                        "
                                    >

                                        {{-- 予約内容 --}}
                                        <div class="min-w-0">

                                            {{-- お客様名 --}}
                                            <div
                                                class="
                                                    truncate
                                                    font-bold
                                                    text-blue-900
                                                "
                                            >
                                                {{ $foundReservation['name'] }}
                                            </div>


                                            {{-- 人数 --}}
                                            <div
                                                class="
                                                    text-xs
                                                    font-medium
                                                    text-blue-600
                                                "
                                            >
                                                {{ $foundReservation['people'] }}名
                                            </div>

                                        </div>

                                    </div>


                                {{-- ==================================
                                     すでに予約バーに含まれている時間
                                =================================== --}}
                                @elseif ($isOccupied)

                                {{-- ==================================
                                     空き時間
                                =================================== --}}
                                @else

                                    <div
                                        class="
                                            group
                                            flex h-12
                                            items-center justify-center
                                            rounded-md
                                            border border-slate-200
                                            bg-white
                                            transition
                                            hover:border-blue-200
                                            hover:bg-blue-50/50
                                        "
                                    >

                                        {{-- 空き時間を示す点 --}}
                                        <span
                                            class="
                                                h-1.5 w-1.5
                                                rounded-full
                                                bg-slate-200
                                                transition
                                                group-hover:bg-blue-300
                                            "
                                        ></span>

                                    </div>

                                @endif


                            @endforeach

                        </div>

                    @endforeach

                </div>

            </div>

        </div>

    </div>


    {{-- ============================================================
         凡例
    ============================================================= --}}
    <div
        class="
            mt-3
            flex flex-wrap
            items-center
            gap-x-5 gap-y-2
            text-xs
            text-slate-500
        "
    >

        {{-- 予約あり --}}
        <div class="flex items-center gap-2">

            <span
                class="
                    h-3 w-3
                    rounded
                    border border-blue-200
                    bg-blue-50
                "
            ></span>

            <span>
                予約あり
            </span>

        </div>


        {{-- 空き --}}
        <div class="flex items-center gap-2">

            <span
                class="
                    h-3 w-3
                    rounded
                    border border-slate-200
                    bg-white
                "
            ></span>

            <span>
                空き
            </span>

        </div>

    </div>

</div>