<div class="min-h-screen bg-slate-50">

    {{-- ============================================================
         TOPページ本体
    ============================================================= --}}
    <div class="mx-auto max-w-7xl px-6 py-8">


        {{-- ========================================================
             ページタイトル
        ========================================================= --}}
        <div class="mb-6">

            <h1 class="text-2xl font-bold text-blue-900">
                予約状況画面（TOP）
            </h1>

        </div>


        {{-- ========================================================
             予約状況
        ========================================================= --}}
        <section>

            <livewire:reservation-status />

        </section>


        {{-- ========================================================
             下段

             PC
             左：7カラム
             右：5カラム

             スマートフォン
             縦並び
        ========================================================= --}}
        <div
            class="
                mt-6
                grid grid-cols-1
                items-start
                gap-6
                lg:grid-cols-12
            "
        >


            {{-- ====================================================
                 左：本日の予約一覧
            ===================================================== --}}
            <section class="lg:col-span-7">

                <div
                    class="
                        rounded-xl
                        border border-slate-200
                        bg-white
                        p-6
                    "
                >

                    {{-- タイトル --}}
                    <div class="mb-5 flex items-center justify-between">

                        <h2 class="text-xl font-bold text-blue-900">
                            本日の予約
                        </h2>

                    </div>


                    {{-- =============================================
                         予約一覧
                    ============================================== --}}
                    <div class="overflow-x-auto">

                        <table class="w-full border-collapse text-sm">

                            {{-- ヘッダー --}}
                            <thead>

                                <tr class="bg-blue-50">

                                    <th class="border border-slate-200 px-3 py-3">
                                        時間
                                    </th>

                                    <th class="border border-slate-200 px-3 py-3">
                                        お名前
                                    </th>

                                    <th class="border border-slate-200 px-3 py-3">
                                        人数
                                    </th>

                                    <th class="border border-slate-200 px-3 py-3">
                                        席
                                    </th>

                                    <th class="border border-slate-200 px-3 py-3">
                                        状態
                                    </th>

                                </tr>

                            </thead>


                            {{-- データ --}}
                            <tbody>

                                @forelse ($todayReservations as $reservation)

                                    <tr class="hover:bg-slate-50">


                                        {{-- 時間 --}}
                                        <td
                                            class="
                                                whitespace-nowrap
                                                border border-slate-200
                                                px-3 py-3
                                                text-center
                                            "
                                        >

                                            {{ substr($reservation->start_time, 0, 5) }}

                                            ～

                                            {{ substr($reservation->end_time, 0, 5) }}

                                        </td>


                                        {{-- お客様名 --}}
                                        <td
                                            class="
                                                border border-slate-200
                                                px-3 py-3
                                            "
                                        >
                                            {{ $reservation->customer_name }}
                                        </td>


                                        {{-- 人数 --}}
                                        <td
                                            class="
                                                border border-slate-200
                                                px-3 py-3
                                                text-center
                                            "
                                        >
                                            {{ $reservation->people }}名
                                        </td>


                                        {{-- 席 --}}
                                        <td
                                            class="
                                                border border-slate-200
                                                px-3 py-3
                                            "
                                        >

                                            @forelse ($reservation->seats as $seat)

                                                <div>
                                                    {{ $seat->seat_name }}
                                                </div>

                                            @empty

                                                <span class="text-slate-400">
                                                    未割当
                                                </span>

                                            @endforelse

                                        </td>


                                        {{-- 状態 --}}
                                        <td
                                            class="
                                                border border-slate-200
                                                px-3 py-3
                                                text-center
                                            "
                                        >

                                            {{-- 確定 --}}
                                            @if ($reservation->status === 'reserved')

                                                <span
                                                    class="
                                                        inline-block
                                                        rounded-md
                                                        bg-blue-100
                                                        px-3 py-1
                                                        text-xs font-semibold
                                                        text-blue-700
                                                    "
                                                >
                                                    確定
                                                </span>


                                            {{-- 仮予約 --}}
                                            @elseif ($reservation->status === 'temporary')

                                                <span
                                                    class="
                                                        inline-block
                                                        rounded-md
                                                        bg-slate-100
                                                        px-3 py-1
                                                        text-xs font-semibold
                                                        text-slate-700
                                                    "
                                                >
                                                    仮予約
                                                </span>


                                            {{-- キャンセル --}}
                                            @elseif ($reservation->status === 'cancelled')

                                                <span
                                                    class="
                                                        inline-block
                                                        rounded-md
                                                        bg-red-100
                                                        px-3 py-1
                                                        text-xs font-semibold
                                                        text-red-700
                                                    "
                                                >
                                                    キャンセル
                                                </span>

                                            @endif

                                        </td>

                                    </tr>


                                @empty

                                    {{-- 予約がない場合 --}}
                                    <tr>

                                        <td
                                            colspan="5"
                                            class="
                                                border border-slate-200
                                                px-3 py-10
                                                text-center
                                                text-slate-500
                                            "
                                        >
                                            本日の予約はありません。
                                        </td>

                                    </tr>

                                @endforelse

                            </tbody>

                        </table>

                    </div>

                </div>

            </section>


            {{-- ====================================================
                 右：新規予約
            ===================================================== --}}
            <section class="lg:col-span-5">

                {{--
                    チームメンバーが作成した
                    ReservationCreateをそのまま再利用します。
                --}}

                <livewire:reservation-create />

            </section>


        </div>

    </div>

</div>