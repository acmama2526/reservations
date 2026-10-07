<div class="min-h-screen bg-slate-50 text-base leading-relaxed text-slate-800">

    <x-header />

    {{-- 画面中央にコンパクトに配置 --}}
    <main class="mx-auto w-full max-w-4xl px-4 py-5 sm:px-6 lg:px-8">

        {{-- タイトル --}}
        <div class="mb-4 overflow-hidden rounded-xl border border-blue-100 bg-white shadow-sm">
            <div class="bg-blue-50 px-4 py-3">
                <h1 class="text-xl font-bold text-blue-950">
                    📋 予約詳細
                </h1>
            </div>
        </div>

        {{-- 予約情報 --}}
        <div class="overflow-hidden rounded-xl border border-blue-100 bg-white p-4 shadow-sm sm:p-5">

            {{-- 見出し --}}
            <div class="mb-3">
                <h2 class="border-b border-blue-100 bg-blue-50 px-3 py-2 font-bold text-blue-950">
                    予約情報
                </h2>
            </div>

            {{-- お客様名 --}}
            <div class="grid grid-cols-4 items-center border-b border-slate-100 py-2.5">
                <div class="font-semibold text-slate-600">
                    お客様名
                </div>

                <div class="col-span-3 font-medium text-slate-900">
                    {{ $reservation->customer_name }}
                </div>
            </div>

            {{-- 人数 --}}
            <div class="grid grid-cols-4 items-center border-b border-slate-100 py-2.5">
                <div class="font-semibold text-slate-600">
                    人数
                </div>

                <div class="col-span-3 font-medium text-slate-900">
                    {{ $reservation->people }}名
                </div>
            </div>

            {{-- 電話番号 --}}
            <div class="grid grid-cols-4 items-center border-b border-slate-100 py-2.5">
                <div class="font-semibold text-slate-600">
                    電話番号
                </div>

                <div class="col-span-3 font-medium text-slate-900">
                    {{ $reservation->phone ?? '―' }}
                </div>
            </div>

            {{-- 予約日 --}}
            <div class="grid grid-cols-4 items-center border-b border-slate-100 py-2.5">
                <div class="font-semibold text-slate-600">
                    予約日
                </div>

                <div class="col-span-3 font-medium text-slate-900">
                    {{ $reservation->reservation_date->format('Y年m月d日') }}
                </div>
            </div>

            {{-- 時間 --}}
            <div class="grid grid-cols-4 items-center border-b border-slate-100 py-2.5">
                <div class="font-semibold text-slate-600">
                    時間
                </div>

                <div class="col-span-3 font-medium text-slate-900">
                    {{ substr($reservation->start_time, 0, 5) }}
                    <span class="mx-1 text-slate-400">～</span>
                    {{ substr($reservation->end_time, 0, 5) }}
                </div>
            </div>

            {{-- 席 --}}
            <div class="grid grid-cols-4 items-start border-b border-slate-100 py-2.5">
                <div class="font-semibold text-slate-600">
                    席
                </div>

                <div class="col-span-3">
                    <div class="flex flex-wrap gap-2">

                        @forelse ($reservation->seats as $seat)
                            <span
                                class="inline-flex items-center rounded-md border border-blue-200 bg-blue-50 px-3 py-1 text-sm font-medium text-blue-900">
                                {{ $seat->seat_name }}
                                <span class="ml-1 text-blue-700">
                                    （{{ $seat->capacity }}名）
                                </span>
                            </span>
                        @empty
                            <span class="text-slate-500">
                                ―
                            </span>
                        @endforelse

                    </div>
                </div>
            </div>

            {{-- 状態 --}}
            <div class="grid grid-cols-4 items-center border-b border-slate-100 py-2.5">
                <div class="font-semibold text-slate-600">
                    状態
                </div>

                <div class="col-span-3">

                    @if ($reservation->status === 'temporary')
                        <span
                            class="inline-flex rounded-full bg-yellow-100 px-3 py-1 text-sm font-semibold text-yellow-800">
                            仮予約
                        </span>
                    @elseif ($reservation->status === 'reserved')
                        <span
                            class="inline-flex rounded-full bg-blue-100 px-3 py-1 text-sm font-semibold text-blue-800">
                            確定
                        </span>
                    @elseif ($reservation->status === 'visited')
                        <span
                            class="inline-flex rounded-full bg-green-100 px-3 py-1 text-sm font-semibold text-green-800">
                            来店済
                        </span>
                    @elseif ($reservation->status === 'paid')
                        <span
                            class="inline-flex rounded-full bg-purple-100 px-3 py-1 text-sm font-semibold text-purple-800">
                            会計済
                        </span>
                    @elseif ($reservation->status === 'cancelled')
                        <span class="inline-flex rounded-full bg-red-100 px-3 py-1 text-sm font-semibold text-red-800">
                            キャンセル
                        </span>
                    @else
                        <span class="font-medium text-slate-900">
                            {{ $reservation->status }}
                        </span>
                    @endif

                </div>
            </div>

            {{-- 備考 --}}
            <div class="grid grid-cols-4 items-start border-b border-slate-100 py-2.5">
                <div class="font-semibold text-slate-600">
                    備考
                </div>

                <div class="col-span-3 whitespace-pre-wrap text-slate-800">
                    {{ $reservation->description ?? '―' }}
                </div>
            </div>

            {{-- ボタン --}}
            <div class="mt-4 flex flex-wrap justify-center gap-2 border-t border-slate-200 pt-4">

                {{-- 一覧に戻る --}}
                <a href="{{ route('reservations.index') }}"
                    class="inline-flex min-h-11 items-center justify-center rounded-md border border-slate-300 bg-white px-6 py-2 text-sm font-semibold text-slate-700 transition hover:bg-slate-100 focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-blue-500 focus-visible:ring-offset-2">
                    一覧に戻る
                </a>

                {{-- 編集 --}}
                <a href="{{ route('reservations.edit', $reservation) }}"
                    class="inline-flex min-h-11 items-center justify-center rounded-md bg-blue-600 px-7 py-2 text-sm font-semibold text-white transition hover:bg-blue-700 focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-blue-500 focus-visible:ring-offset-2">
                    編集する
                </a>

            </div>

        </div>

    </main>

    <x-footer />

</div>
