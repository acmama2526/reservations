<div class="min-h-screen bg-gray-50">

    <main class="mx-auto max-w-7xl px-6 py-8">

        {{-- ページタイトル --}}
        <div class="mb-6">
            <h1 class="text-3xl font-bold text-blue-950">
                予約状況画面（TOP）
            </h1>
        </div>


        {{-- 予約状況 --}}
        <section class="rounded-xl border bg-white p-6 shadow-sm">
            

            <livewire:reservation-status />

        </section>


        {{-- 下段 --}}
        <div class="mt-6 grid grid-cols-1 items-start gap-6 lg:grid-cols-12">

            {{-- 左：当日の予約一覧（7カラム） --}}
            <div class="lg:col-span-7">

                <section class="rounded-xl border bg-white p-6 shadow-sm">

                    <h2 class="mb-5 text-xl font-bold text-blue-900">
                        本日の予約
                    </h2>

                    <div class="overflow-x-auto">

                        <table class="w-full border-collapse text-sm">

                            <thead>
                                <tr class="bg-blue-50">

                                    <th class="border px-3 py-3">
                                        時間
                                    </th>

                                    <th class="border px-3 py-3">
                                        お名前
                                    </th>

                                    <th class="border px-3 py-3">
                                        人数
                                    </th>

                                    <th class="border px-3 py-3">
                                        席
                                    </th>

                                    <th class="border px-3 py-3">
                                        状態
                                    </th>

                                </tr>
                            </thead>

                            <tbody>

                                @forelse ($todayReservations as $reservation)

                                    <tr class="hover:bg-gray-50">

                                        {{-- 時間 --}}
                                        <td class="border px-3 py-3 text-center">
                                            {{ substr($reservation->start_time, 0, 5) }}
                                            ～
                                            {{ substr($reservation->end_time, 0, 5) }}
                                        </td>

                                        {{-- 名前 --}}
                                        <td class="border px-3 py-3">
                                            {{ $reservation->customer_name }}
                                        </td>

                                        {{-- 人数 --}}
                                        <td class="border px-3 py-3 text-center">
                                            {{ $reservation->people }}名
                                        </td>

                                        {{-- 席 --}}
                                        <td class="border px-3 py-3">

                                            @forelse ($reservation->seats as $seat)
                                                <div>
                                                    {{ $seat->seat_name }}
                                                </div>

                                            @empty

                                                <span class="text-gray-400">
                                                    未割当
                                                </span>
                                            @endforelse

                                        </td>

                                        {{-- 状態 --}}
                                        <td class="border px-3 py-3 text-center">

                                            @if ($reservation->status === 'reserved')
                                                <span class="rounded bg-blue-100 px-2 py-1 text-blue-700">
                                                    確定
                                                </span>
                                            @elseif ($reservation->status === 'temporary')
                                                <span class="rounded bg-gray-100 px-2 py-1 text-gray-700">
                                                    仮予約
                                                </span>
                                            @elseif ($reservation->status === 'cancelled')
                                                <span class="rounded bg-red-100 px-2 py-1 text-red-700">
                                                    キャンセル
                                                </span>
                                            @endif

                                        </td>

                                    </tr>

                                @empty

                                    <tr>
                                        <td colspan="5" class="border px-3 py-8 text-center text-gray-500">
                                            本日の予約はありません。
                                        </td>
                                    </tr>

                                @endforelse

                            </tbody>

                        </table>

                    </div>

                </section>

            </div>


            {{-- 右：新規予約（5カラム） --}}
            <div class="lg:col-span-5">

                <livewire:reservation-create />

            </div>

        </div>

    </main>

</div>
