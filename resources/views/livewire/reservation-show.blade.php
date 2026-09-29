<div class="max-w-4xl mx-auto p-6">

    {{-- タイトル --}}
    <div class="bg-blue-50 border-b border-gray-200 px-4 py-3 mb-6">
        <h1 class="text-xl font-bold text-blue-700">
            📋 予約詳細
        </h1>
    </div>

    {{-- 予約情報 --}}
    <div class="bg-white border rounded-lg p-6 shadow-sm">

        <div class="grid grid-cols-4 gap-4 mb-4">
            <div class="font-semibold">お客様名</div>
            <div class="col-span-3">
                {{ $reservation->customer_name }}
            </div>
        </div>

        <div class="grid grid-cols-4 gap-4 mb-4">
            <div class="font-semibold">人数</div>
            <div class="col-span-3">
                {{ $reservation->people }}名
            </div>
        </div>

        <div class="grid grid-cols-4 gap-4 mb-4">
            <div class="font-semibold">電話番号</div>
            <div class="col-span-3">
                {{ $reservation->phone ?? '―' }}
            </div>
        </div>

        <div class="grid grid-cols-4 gap-4 mb-4">
            <div class="font-semibold">予約日</div>
            <div class="col-span-3">
                {{ $reservation->reservation_date->format('Y年m月d日') }}
            </div>
        </div>

        <div class="grid grid-cols-4 gap-4 mb-4">
            <div class="font-semibold">時間</div>
            <div class="col-span-3">
                {{ substr($reservation->start_time, 0, 5) }}
                ～
                {{ substr($reservation->end_time, 0, 5) }}
            </div>
        </div>

        <div class="grid grid-cols-4 gap-4 mb-4">
            <div class="font-semibold">席</div>
            <div class="col-span-3">
                @forelse ($reservation->seats as $seat)
                    <span class="mr-2">
                        {{ $seat->seat_name }}
                    </span>
                @empty
                    ―
                @endforelse
            </div>
        </div>

        <div class="grid grid-cols-4 gap-4 mb-4">
            <div class="font-semibold">状態</div>
            <div class="col-span-3">
                @if ($reservation->status === 'temporary')
                    仮予約
                @elseif ($reservation->status === 'reserved')
                    確定
                @elseif ($reservation->status === 'cancelled')
                    キャンセル
                @else
                    {{ $reservation->status }}
                @endif
            </div>
        </div>

        <div class="grid grid-cols-4 gap-4 mb-6">
            <div class="font-semibold">備考</div>
            <div class="col-span-3 whitespace-pre-wrap">
                {{ $reservation->description ?? '―' }}
            </div>
        </div>

        <div class="flex justify-center">
            {{-- 戻るボタン --}}
            <div class="flex justify-center">
                <a href="{{ route('reservations.index') }}"
                    class="rounded border border-gray-300 bg-white px-8 py-3 font-semibold text-gray-700 hover:bg-gray-100">
                    一覧に戻る
                </a>
            </div>

            {{-- 編集ボタン --}}
            <div class="flex justify-center">
                <a href="{{ route('reservations.edit', $reservation) }}"
                    class="rounded border border-gray-300 bg-white px-8 py-3 font-semibold text-gray-700 hover:bg-gray-100">
                    編集する
                </a>
            </div>
        </div>
    </div>
</div>
