<div class="...">

    <h2 class="text-xl font-bold text-blue-900">
        予約状況
    </h2>

    <div class="flex items-center justify-center gap-4">

        {{-- 前の日付 --}}
        <button wire:click="previousDay">
            ＜
        </button>

        <span>
            {{ $date }}
        </span>

        {{-- 次の日付 --}}
        <button wire:click="nextDay">
            ＞
        </button>

    </div>

    <div class="grid grid-cols-[repeat(12,80px)] gap-2 text-center">

        <div class="border p-2">
            席＼時間
        </div>

        {{-- ② 一番上に時間を並べる --}}
        @foreach ($times as $time)
            <div class="border p-2 ">{{ $time }}</div>
        @endforeach

        {{-- ③ 席を1つずつ取り出す --}}
        @foreach ($seats as $seat)
            <div class="border p-2">
                {{ $seat }}
            </div>

            {{-- ⑤ その席の時間分だけ空マスを作る --}}
            @foreach ($times as $time)
                @php
                    $foundReservation = null;
                    // この時間は「すでに予約バーの途中」か？
                    $isOccupied = false;
                @endphp
                @foreach ($reservations as $reservation)
                    @if ($seat === $reservation['seat'] && $time === $reservation['start_time'])
                        @php
                            $foundReservation = $reservation;
                        @endphp
                    @elseif($seat === $reservation['seat'] && $time > $reservation['start_time'] && $time <= $reservation['end_time'])
                        @php
                            $isOccupied = true;
                        @endphp
                    @endif
                @endforeach
                @if ($foundReservation)
                    <div class="h-10 border border-green-300"
                        style="grid-column: span {{ $foundReservation['span'] }} / span {{ $foundReservation['span'] }};">
                        {{ $foundReservation['name'] }}
                        {{ $foundReservation['people'] }}名
                    </div>
                    @elseif ($isOccupied)
                    {{-- すでに予約バーの途中なので、空マスは作らない --}}
                @else
                    <div class="h-10 border"></div>
                @endif
            @endforeach
        @endforeach


    </div>

</div>
