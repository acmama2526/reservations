<div class="min-h-screen bg-white text-slate-800">

    <style>
        @page {
            size: A4 landscape;
            margin: 10mm;
        }

        * {
            box-sizing: border-box;
        }

        body {
            margin: 0;
            color: #1e293b;
            font-family:
                "Noto Sans JP",
                "Yu Gothic",
                "Meiryo",
                sans-serif;
        }

        .print-container {
            width: 100%;
            padding: 20px;
        }

        .print-header {
            margin-bottom: 20px;
        }

        .print-title {
            margin: 0 0 15px;
            text-align: center;
            font-size: 24px;
            font-weight: bold;
        }

        .print-info {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 15px;
            font-size: 12px;
        }

        .print-button-area {
            margin-bottom: 20px;
            text-align: right;
        }

        .print-button {
            padding: 10px 20px;
            border: 1px solid #2563eb;
            border-radius: 6px;
            background: #2563eb;
            color: white;
            cursor: pointer;
            font-size: 14px;
        }

        .print-button:hover {
            background: #1d4ed8;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            table-layout: fixed;
            font-size: 11px;
        }

        th,
        td {
            border: 1px solid #64748b;
            padding: 6px 8px;
            vertical-align: middle;
        }

        th {
            background: #e2e8f0;
            font-weight: bold;
            text-align: center;
        }

        td {
            background: white;
        }

        .center {
            text-align: center;
        }

        .no {
            width: 5%;
        }

        .date {
            width: 17%;
        }

        .name {
            width: 14%;
        }

        .people {
            width: 7%;
        }

        .seat {
            width: 11%;
        }

        .status {
            width: 10%;
        }

        .phone {
            width: 14%;
        }

        .description {
            width: 22%;
        }

        .no-data {
            padding: 30px;
            text-align: center;
        }

        .print-footer {
            margin-top: 15px;
            text-align: right;
            font-size: 10px;
        }

        /*
         * 印刷時は印刷ボタンを表示しない
         */
        @media print {
            .no-print {
                display: none !important;
            }

            .print-container {
                padding: 0;
            }
        }
    </style>

    <main class="print-container">

        {{-- 印刷ボタン --}}
        <div class="print-button-area no-print">
            <button type="button" class="print-button" onclick="window.print()">
                🖨 印刷
            </button>
        </div>

        {{-- タイトル --}}
        <header class="print-header">

            <h1 class="print-title">
                予約一覧
            </h1>

            <div class="print-info">

                <span>
                    件数：{{ $reservations->count() }}件
                </span>

                <span>
                    印刷日時：{{ now()->format('Y/m/d H:i') }}
                </span>

            </div>

        </header>

        {{-- 一覧表 --}}
        <table>

            <thead>
                <tr>
                    <th class="no">
                        No
                    </th>

                    <th class="date">
                        日時
                    </th>

                    <th class="name">
                        お名前
                    </th>

                    <th class="people">
                        人数
                    </th>

                    <th class="seat">
                        席
                    </th>

                    <th class="status">
                        状態
                    </th>

                    <th class="phone">
                        電話番号
                    </th>

                    <th class="description">
                        備考
                    </th>
                </tr>
            </thead>

            <tbody>

                @forelse ($reservations as $reservation)

                    <tr>

                        {{-- No --}}
                        <td class="center">
                            {{ $loop->iteration }}
                        </td>

                        {{-- 日時 --}}
                        <td>
                            {{ $reservation->reservation_date->format('Y/m/d') }}
                            ({{ $reservation->reservation_date->locale('ja')->isoFormat('ddd') }})
                            <br>

                            {{ substr($reservation->start_time, 0, 5) }}
                            ～
                            {{ substr($reservation->end_time, 0, 5) }}
                        </td>

                        {{-- お名前 --}}
                        <td>
                            {{ $reservation->customer_name }}
                        </td>

                        {{-- 人数 --}}
                        <td class="center">
                            {{ $reservation->people }}名
                        </td>

                        {{-- 席 --}}
                        <td>

                            @forelse ($reservation->seats as $seat)
                                <div>
                                    {{ $seat->seat_name }}
                                </div>

                            @empty

                                <span>
                                    未割当
                                </span>
                            @endforelse

                        </td>

                        {{-- 状態 --}}
                        <td class="center">

                            @switch($reservation->status)
                                @case('temporary')
                                    仮予約
                                @break

                                @case('reserved')
                                    確定
                                @break

                                @case('visited')
                                    来店済
                                @break

                                @case('paid')
                                    会計済
                                @break

                                @case('cancelled')
                                    キャンセル
                                @break

                                @default
                                    {{ $reservation->status }}
                            @endswitch

                        </td>

                        {{-- 電話番号 --}}
                        <td>

                            @if ($reservation->phone)
                                @if (strlen($reservation->phone) === 11)
                                    {{ preg_replace('/^(\d{3})(\d{4})(\d{4})$/', '$1-$2-$3', $reservation->phone) }}
                                @elseif (strlen($reservation->phone) === 10)
                                    {{ preg_replace('/^(\d{2,4})(\d{2,4})(\d{4})$/', '$1-$2-$3', $reservation->phone) }}
                                @else
                                    {{ $reservation->phone }}
                                @endif
                            @else
                                ―
                            @endif

                        </td>

                        {{-- 備考 --}}
                        <td>
                            {{ $reservation->description ?? '―' }}
                        </td>

                    </tr>

                    @empty

                        <tr>

                            <td colspan="8" class="no-data">
                                該当する予約がありません。
                            </td>

                        </tr>
                    @endforelse

                </tbody>

            </table>

            {{-- フッター --}}
            <div class="print-footer">
                予約管理システム
            </div>

        </main>

        {{-- 印刷画面を開いたら自動的に印刷ダイアログを表示 --}}
        <script>
            window.addEventListener('load', function() {
                window.print();
            });
        </script>

    </div>
