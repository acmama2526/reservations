<div class="min-h-screen bg-gray-50">

    {{-- ヘッダー --}}


    {{-- メイン --}}
    <main class="mx-auto max-w-7xl px-6 py-8">

        {{-- タイトル --}}
        <div class="mb-8">

            <h1 class="text-4xl font-bold text-blue-950">
                予約一覧・検索
            </h1>

            <p class="mt-2 text-xl text-gray-600">
                予約の検索・確認・詳細表示・状態管理
            </p>

        </div>


        {{-- フラッシュメッセージ --}}
        @if (session()->has('message'))
            <div class="mb-6 rounded-lg bg-green-100 px-5 py-4 text-green-800">
                {{ session('message') }}
            </div>
        @endif


        {{-- 検索条件 --}}
        <section class="mb-6 rounded-xl border bg-white p-6 shadow-sm">

            <h2 class="mb-5 text-2xl font-bold text-blue-900">
                🔍 検索条件
            </h2>


            <div class="grid grid-cols-1 gap-5 md:grid-cols-2 lg:grid-cols-3">


                {{-- 日付 From --}}
                <div>

                    <label class="mb-2 block font-medium">
                        日付（開始）
                    </label>

                    <input type="date" wire:model="dateFrom" class="w-full rounded-lg border px-4 py-3">

                </div>


                {{-- 日付 To --}}
                <div>

                    <label class="mb-2 block font-medium">
                        日付（終了）
                    </label>

                    <input type="date" wire:model="dateTo" class="w-full rounded-lg border px-4 py-3">

                </div>


                {{-- お名前 --}}
                <div>

                    <label class="mb-2 block font-medium">
                        お名前
                    </label>

                    <input type="text" wire:model="customerName" placeholder="例）山田 太郎"
                        class="w-full rounded-lg border px-4 py-3">

                </div>


                {{-- 人数 --}}
                <div>

                    <label class="mb-2 block font-medium">
                        人数
                    </label>

                    <select wire:model="people" class="w-full rounded-lg border px-4 py-3">

                        <option value="">
                            指定なし
                        </option>

                        @for ($i = 1; $i <= 10; $i++)
                            <option value="{{ $i }}">
                                {{ $i }}名
                            </option>
                        @endfor

                    </select>

                </div>


                {{-- 状態 --}}
                <div>

                    <label class="mb-2 block font-medium">
                        状態
                    </label>

                    <select wire:model="status" class="w-full rounded-lg border px-4 py-3">

                        <option value="">
                            指定なし
                        </option>

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


                {{-- 席 --}}
                <div>

                    <label class="mb-2 block font-medium">
                        席
                    </label>

                    <select wire:model="seat" class="w-full rounded-lg border px-4 py-3">

                        <option value="">
                            指定なし
                        </option>

                        @foreach ($seats as $seat)
                            <option value="{{ $seat->id }}">
                                {{ $seat->seat_name }}
                            </option>
                        @endforeach

                    </select>

                </div>

            </div>


            {{-- 検索ボタン --}}
            <div class="mt-6 flex justify-end gap-3">

                <button type="button" wire:click="clearSearch"
                    class="rounded-lg border bg-white px-8 py-3 font-medium hover:bg-gray-50">
                    ↻ クリア
                </button>

                <button type="button" wire:click="search"
                    class="rounded-lg bg-blue-600 px-8 py-3 font-bold text-white hover:bg-blue-700">
                    🔍 検索
                </button>

            </div>

        </section>


        {{-- 一覧 --}}
        <section class="rounded-xl border bg-white p-6 shadow-sm">

            <div class="mb-5 flex items-center justify-between">

                <h2 class="text-2xl font-bold text-blue-900">
                    📋 予約一覧
                    （全 {{ $reservations->total() }} 件）
                </h2>


                {{-- 右側のボタン --}}
                <div class="flex items-center gap-4">

                    {{-- 新規登録 --}}
                    <a href="{{ route('reservations.create') }}"
                        class="rounded-lg bg-blue-600 px-5 py-3 font-bold text-white hover:bg-blue-700">
                        ＋ 新規登録
                    </a>

                    {{-- 表示件数 --}}
                    <div class="flex items-center gap-2">

                        <span>
                            表示件数
                        </span>

                        <select wire:model.live="perPage" class="rounded-lg border px-3 py-2">

                            <option value="10">
                                10件
                            </option>

                            <option value="20">
                                20件
                            </option>

                            <option value="50">
                                50件
                            </option>

                        </select>

                    </div>

                </div>

            </div>


            {{-- テーブル --}}
            <div class="overflow-x-auto">

                <table class="w-full border-collapse text-sm">

                    <thead>

                        <tr class="bg-blue-50">

                            <th class="border px-3 py-3">
                                No
                            </th>

                            <th class="border px-3 py-3">
                                日時
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

                            <th class="border px-3 py-3">
                                電話番号
                            </th>

                            <th class="border px-3 py-3">
                                備考
                            </th>

                            <th class="border px-3 py-3">
                                操作
                            </th>

                        </tr>

                    </thead>


                    <tbody>

                        @forelse ($reservations as $index => $reservation)

                            <tr class="hover:bg-gray-50">

                                {{-- No --}}
                                <td class="border px-3 py-3 text-center">
                                    {{ $reservations->firstItem() + $index }}
                                </td>


                                {{-- 日時 --}}
                                <td class="border px-3 py-3 whitespace-nowrap">

                                    {{ $reservation->reservation_date->format('Y/m/d') }}

                                    ({{ $reservation->reservation_date->locale('ja')->isoFormat('ddd') }})
                                    <br>

                                    {{ substr($reservation->start_time, 0, 5) }}

                                    ～

                                    {{ substr($reservation->end_time, 0, 5) }}

                                </td>


                                {{-- 名前 --}}
                                <td class="border px-3 py-3 font-medium">
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
                                        <span class="rounded-md bg-blue-100 px-3 py-1 text-blue-700">
                                            確定
                                        </span>
                                    @elseif ($reservation->status === 'temporary')
                                        <span class="rounded-md bg-gray-100 px-3 py-1 text-gray-700">
                                            仮予約
                                        </span>
                                    @elseif ($reservation->status === 'cancelled')
                                        <span class="rounded-md bg-red-100 px-3 py-1 text-red-700">
                                            キャンセル
                                        </span>
                                    @endif

                                </td>


                                {{-- 電話番号 --}}
                                <td class="border px-3 py-3 whitespace-nowrap">
                                    {{ $reservation->phone ?? '―' }}
                                </td>


                                {{-- 備考 --}}
                                <td class="border px-3 py-3">
                                    {{ $reservation->description ?? '―' }}
                                </td>


                                {{-- 操作 --}}
                                <td class="border px-3 py-3">

                                    <div class="flex gap-2">

                                        {{-- 詳細 --}}
                                        <a href="{{ route('reservations.show', $reservation) }}"
                                            class="rounded border px-3 py-1 text-sm hover:bg-gray-100">
                                            詳細
                                        </a>


                                        {{-- 編集 --}}
                                        <a href="{{ route('reservations.edit', $reservation) }}"
                                            class="rounded bg-blue-600 px-3 py-1 text-sm text-white hover:bg-blue-700">
                                            編集
                                        </a>


                                        {{-- 削除 --}}
                                        <button type="button" wire:click="deleteReservation({{ $reservation->id }})"
                                            wire:confirm="この予約を削除しますか？"
                                            class="rounded bg-red-500 px-3 py-1 text-sm text-white hover:bg-red-600">
                                            削除
                                        </button>

                                    </div>

                                </td>

                            </tr>


                        @empty

                            <tr>

                                <td colspan="9" class="border px-3 py-10 text-center text-gray-500">
                                    該当する予約がありません。
                                </td>

                            </tr>
                        @endforelse

                    </tbody>

                </table>

            </div>


            {{-- ページネーション --}}
            <div class="mt-6">

                {{ $reservations->links() }}

            </div>

        </section>

    </main>

</div>
