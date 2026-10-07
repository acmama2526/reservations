<div class="min-h-screen bg-slate-50 text-base leading-relaxed text-slate-800">
    {{-- ヘッダー --}}
    <x-header />
    {{-- メイン --}}
    <main class="mx-auto w-full max-w-screen-2xl px-4 py-6 sm:px-6 lg:px-8 lg:py-8">

        {{-- フラッシュメッセージ --}}
        @if (session()->has('message'))
            <div role="status" aria-live="polite"
                class="mb-6 rounded-lg border border-green-200 bg-green-50 px-4 py-3 text-sm text-green-700">
                {{ session('message') }}
            </div>
        @endif
        {{-- 検索条件 --}}
        <section class="mb-6 overflow-hidden rounded-xl border border-blue-100 bg-white p-5 shadow-sm sm:p-6">
            <h2
                class="flex items-center gap-2 -mx-5 -mt-5 mb-6 border-b border-blue-100 bg-blue-50 px-5 py-4 text-lg font-semibold text-blue-950 sm:-mx-6 sm:-mt-6 sm:px-6">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 shrink-0" viewBox="0 0 24 24" fill="none"
                    stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                    aria-hidden="true">
                    <circle cx="10.5" cy="10.5" r="6.5" />
                    <path d="m16 16 4.5 4.5" />
                </svg>
                検索条件
            </h2>
            <div class="grid grid-cols-1 gap-x-6 gap-y-5 md:grid-cols-2 lg:grid-cols-3">
                {{-- 日付 From --}}
                <div>
                    <label for="reservation-filter-dateFrom" class="mb-2 block text-sm font-semibold text-slate-700">
                        日付（開始）
                    </label>
                    <input type="date" id="reservation-filter-dateFrom" wire:model="dateFrom"
                        class="min-h-12 w-full rounded-md border border-slate-300 bg-white px-3 py-2.5 text-base text-slate-900 outline-none transition placeholder:text-slate-500 focus:border-blue-600 focus:ring-2 focus:ring-blue-100">
                </div>
                {{-- 日付 To --}}
                <div>
                    <label for="reservation-filter-dateTo" class="mb-2 block text-sm font-semibold text-slate-700">
                        日付（終了）
                    </label>
                    <input type="date" id="reservation-filter-dateTo" wire:model="dateTo"
                        class="min-h-12 w-full rounded-md border border-slate-300 bg-white px-3 py-2.5 text-base text-slate-900 outline-none transition placeholder:text-slate-500 focus:border-blue-600 focus:ring-2 focus:ring-blue-100">
                </div>
                {{-- お名前 --}}
                <div>
                    <label for="reservation-filter-customerName"
                        class="mb-2 block text-sm font-semibold text-slate-700">
                        お名前
                    </label>
                    <input type="text" id="reservation-filter-customerName" wire:model="customerName"
                        placeholder="例）山田 太郎"
                        class="min-h-12 w-full rounded-md border border-slate-300 bg-white px-3 py-2.5 text-base text-slate-900 outline-none transition placeholder:text-slate-500 focus:border-blue-600 focus:ring-2 focus:ring-blue-100">
                </div>
                {{-- 人数 --}}
                <div>
                    <label for="reservation-filter-people" class="mb-2 block text-sm font-semibold text-slate-700">
                        人数
                    </label>
                    <select id="reservation-filter-people" wire:model="people"
                        class="min-h-12 w-full rounded-md border border-slate-300 bg-white px-3 py-2.5 text-base text-slate-900 outline-none transition placeholder:text-slate-500 focus:border-blue-600 focus:ring-2 focus:ring-blue-100">
                        <option value="">
                            指定なし
                        </option>
                        @for ($i = 1; $i <= $totalCapacity; $i++)
                            <option value="{{ $i }}">
                                {{ $i }}名
                            </option>
                        @endfor
                    </select>
                </div>
                {{-- 状態 --}}
                <div>
                    <label for="reservation-filter-status" class="mb-2 block text-sm font-semibold text-slate-700">
                        状態
                    </label>
                    <select id="reservation-filter-status" wire:model="status"
                        class="min-h-12 w-full rounded-md border border-slate-300 bg-white px-3 py-2.5 text-base text-slate-900 outline-none transition placeholder:text-slate-500 focus:border-blue-600 focus:ring-2 focus:ring-blue-100">
                        <option value="">
                            指定なし
                        </option>
                        <option value="temporary">
                            仮予約
                        </option>
                        <option value="reserved">
                            確定
                        </option>
                        <option value="visited">
                            来店済
                        </option>
                        <option value="paid">
                            会計済
                        </option>
                        <option value="cancelled">
                            キャンセル
                        </option>
                    </select>
                </div>
                {{-- 席 --}}
                <div>
                    <label for="reservation-filter-seat" class="mb-2 block text-sm font-semibold text-slate-700">
                        席
                    </label>
                    <select id="reservation-filter-seat" wire:model="seat"
                        class="min-h-12 w-full rounded-md border border-slate-300 bg-white px-3 py-2.5 text-base text-slate-900 outline-none transition placeholder:text-slate-500 focus:border-blue-600 focus:ring-2 focus:ring-blue-100">
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
            <div class="mt-6 flex flex-wrap justify-end gap-3">
                <button type="button" wire:click="clearSearch" wire:loading.attr="disabled"
                    wire:target="search,clearSearch"
                    class="inline-flex min-h-12 items-center justify-center gap-2 rounded-md border border-slate-300 bg-white px-6 py-2.5 text-base font-semibold text-slate-700 transition hover:bg-slate-100 disabled:cursor-wait disabled:opacity-60 focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-blue-600 focus-visible:ring-offset-2">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 shrink-0" viewBox="0 0 24 24" fill="none"
                        stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                        aria-hidden="true">
                        <path d="M3 10a9 9 0 1 1 2.6 8.4M3 4v6h6" />
                    </svg>
                    クリア
                </button>
                <button type="button" wire:click="search" wire:loading.attr="disabled" wire:target="search,clearSearch"
                    class="inline-flex min-h-12 items-center justify-center gap-2 rounded-md bg-blue-600 px-8 py-2.5 text-base font-semibold text-white transition hover:bg-blue-700 disabled:cursor-wait disabled:opacity-60 focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-blue-600 focus-visible:ring-offset-2">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 shrink-0" viewBox="0 0 24 24" fill="none"
                        stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                        aria-hidden="true">
                        <circle cx="10.5" cy="10.5" r="6.5" />
                        <path d="m16 16 4.5 4.5" />
                    </svg>
                    検索
                </button>
            </div>
        </section>
        {{-- 一覧 --}}
        <section class="overflow-hidden rounded-xl border border-blue-100 bg-white p-5 shadow-sm sm:p-6">
            <div
                class="-mx-5 -mt-5 mb-6 flex flex-col gap-4 border-b border-blue-100 bg-blue-50 px-5 py-4 sm:-mx-6 sm:-mt-6 sm:px-6 lg:flex-row lg:items-center lg:justify-between">
                <h2 class="flex flex-wrap items-center gap-2 text-lg font-semibold text-blue-950">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 shrink-0" viewBox="0 0 24 24" fill="none"
                        stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                        aria-hidden="true">
                        <rect x="4" y="3" width="16" height="18" rx="2" />
                        <path d="M8 8h8M8 12h8M8 16h5" />
                    </svg>
                    予約一覧
                    <span class="ml-2 inline-block text-sm font-medium text-slate-600">
                        （全 {{ $reservations->total() }} 件）
                    </span>
                </h2>
                {{-- 右側のボタン --}}
                <div class="flex flex-wrap items-center gap-3 sm:gap-4">
                    {{-- 新規登録 --}}
                    <a href="{{ route('reservations.create') }}"
                        class="inline-flex min-h-12 items-center justify-center gap-2 whitespace-nowrap rounded-md bg-blue-600 px-5 py-2.5 text-base font-semibold text-white transition hover:bg-blue-700 focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-blue-600 focus-visible:ring-offset-2">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 shrink-0" viewBox="0 0 24 24"
                            fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                            stroke-linejoin="round" aria-hidden="true">
                            <path d="M12 5v14M5 12h14" />
                        </svg>
                        新規登録
                    </a>
                    {{-- 表示件数 --}}
                    <div class="flex items-center gap-2 whitespace-nowrap text-sm text-slate-600">
                        <span>
                            表示件数
                        </span>
                        <select aria-label="予約の表示件数" wire:model.live="perPage"
                            class="min-h-11 rounded-md border border-slate-300 bg-white px-3 py-2 text-base text-slate-800 outline-none focus:border-blue-600 focus:ring-2 focus:ring-blue-100">
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
            <div class="overflow-x-auto rounded-lg border border-blue-100">
                <table
                    class="[&_th:nth-child(1)]:text-center [&_th:nth-child(4)]:text-center [&_th:nth-child(6)]:text-center w-full min-w-[1120px] border-collapse text-left text-sm text-slate-800">
                    <thead>
                        <tr class="bg-[#f7f9fb]">
                            <th scope="col"
                                class="whitespace-nowrap border-b border-slate-200 bg-slate-50 px-4 py-3 text-sm font-semibold text-slate-600">
                                No
                            </th>
                            <th scope="col"
                                class="whitespace-nowrap border-b border-slate-200 bg-slate-50 px-4 py-3 text-sm font-semibold text-slate-600">
                                日時
                            </th>
                            <th scope="col"
                                class="whitespace-nowrap border-b border-slate-200 bg-slate-50 px-4 py-3 text-sm font-semibold text-slate-600">
                                お名前
                            </th>
                            <th scope="col"
                                class="whitespace-nowrap border-b border-slate-200 bg-slate-50 px-4 py-3 text-sm font-semibold text-slate-600">
                                人数
                            </th>
                            <th scope="col"
                                class="whitespace-nowrap border-b border-slate-200 bg-slate-50 px-4 py-3 text-sm font-semibold text-slate-600">
                                席
                            </th>
                            <th scope="col"
                                class="whitespace-nowrap border-b border-slate-200 bg-slate-50 px-4 py-3 text-sm font-semibold text-slate-600">
                                状態
                            </th>
                            <th scope="col"
                                class="whitespace-nowrap border-b border-slate-200 bg-slate-50 px-4 py-3 text-sm font-semibold text-slate-600">
                                電話番号
                            </th>
                            <th scope="col"
                                class="whitespace-nowrap border-b border-slate-200 bg-slate-50 px-4 py-3 text-sm font-semibold text-slate-600">
                                備考
                            </th>
                            <th scope="col"
                                class="whitespace-nowrap border-b border-slate-200 bg-slate-50 px-4 py-3 text-sm font-semibold text-slate-600">
                                操作
                            </th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($reservations as $index => $reservation)
                            <tr wire:key="reservation-{{ $reservation->id }}"
                                class="transition-colors hover:bg-blue-50/60 focus-within:bg-blue-50/60">
                                {{-- No --}}
                                <td class="border-b border-slate-200 px-4 py-4 text-center align-middle tabular-nums">
                                    {{ $reservations->firstItem() + $index }}
                                </td>
                                {{-- 日時 --}}
                                <td
                                    class="whitespace-nowrap border-b border-slate-200 px-4 py-4 align-middle leading-6 tabular-nums">
                                    {{ $reservation->reservation_date->format('Y/m/d') }}
                                    ({{ $reservation->reservation_date->locale('ja')->isoFormat('ddd') }})
                                    <br>
                                    <span class="text-base font-semibold text-slate-950">
                                        {{ substr($reservation->start_time, 0, 5) }}
                                        ～
                                        {{ substr($reservation->end_time, 0, 5) }}
                                    </span>
                                </td>
                                {{-- 名前 --}}
                                <td
                                    class="border-b border-slate-200 px-4 py-4 align-middle text-base font-semibold text-slate-950">
                                    {{ $reservation->customer_name }}
                                </td>
                                {{-- 人数 --}}
                                <td class="border-b border-slate-200 px-4 py-4 text-center align-middle tabular-nums">
                                    {{ $reservation->people }}名
                                </td>
                                {{-- 席 --}}
                                <td class="border-b border-slate-200 px-4 py-4 align-middle">
                                    @forelse ($reservation->seats as $seat)
                                        <div>
                                            {{ $seat->seat_name }}
                                        </div>
                                    @empty
                                        <span class="text-sm font-medium text-amber-800">
                                            未割当
                                        </span>
                                    @endforelse
                                </td>
                                {{-- 状態 --}}
                                <td class="border-b border-slate-200 px-4 py-4 text-center align-middle">
                                    <select wire:change="updateStatus({{ $reservation->id }}, $event.target.value)"
                                        class="min-h-10 min-w-[120px] rounded-md border border-slate-300 bg-white px-3 py-2 text-sm font-semibold text-slate-800">
                                        <option value="temporary"
                                            {{ $reservation->status === 'temporary' ? 'selected' : '' }}>
                                            仮予約
                                        </option>

                                        <option value="reserved"
                                            {{ $reservation->status === 'reserved' ? 'selected' : '' }}>
                                            確定
                                        </option>

                                        <option value="visited"
                                            {{ $reservation->status === 'visited' ? 'selected' : '' }}>
                                            来店済
                                        </option>

                                        <option value="paid"
                                            {{ $reservation->status === 'paid' ? 'selected' : '' }}>
                                            会計済
                                        </option>

                                        <option value="cancelled"
                                            {{ $reservation->status === 'cancelled' ? 'selected' : '' }}>
                                            キャンセル
                                        </option>
                                    </select>
                                </td>
                                {{-- 電話番号 --}}
                                <td
                                    class="whitespace-nowrap border-b border-slate-200 px-4 py-4 align-middle leading-6 tabular-nums">
                                    {{ $reservation->phone ?? '―' }}
                                </td>
                                {{-- 備考 --}}
                                <td class="border-b border-slate-200 px-4 py-4 align-middle">
                                    {{ $reservation->description ?? '―' }}
                                </td>
                                {{-- 操作 --}}
                                <td class="border-b border-slate-200 px-4 py-4 align-middle">
                                    <div class="flex items-center gap-2 whitespace-nowrap">
                                        {{-- 詳細 --}}
                                        <a href="{{ route('reservations.show', $reservation) }}"
                                            class="inline-flex min-h-11 min-w-[60px] items-center justify-center rounded-md border border-blue-200 bg-blue-50 px-3 py-2 text-sm font-semibold text-blue-800 transition hover:bg-blue-100 focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-blue-600 focus-visible:ring-offset-2">
                                            詳細
                                        </a>
                                        {{-- 編集 --}}
                                        <a href="{{ route('reservations.edit', $reservation) }}"
                                            class="inline-flex min-h-11 min-w-[60px] items-center justify-center rounded-md border border-slate-300 bg-white px-3 py-2 text-sm font-semibold text-slate-700 transition hover:bg-slate-100 focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-blue-600 focus-visible:ring-offset-2">
                                            編集
                                        </a>
                                        {{-- 削除 --}}
                                        <button type="button" wire:click="deleteReservation({{ $reservation->id }})"
                                            wire:loading.attr="disabled" wire:target="deleteReservation"
                                            wire:confirm="{{ $reservation->customer_name }} 様／{{ $reservation->reservation_date->format('Y/m/d') }} {{ substr($reservation->start_time, 0, 5) }} の予約を削除しますか？"
                                            class="ml-3 inline-flex min-h-11 min-w-[60px] items-center justify-center rounded-md border border-rose-200 bg-white px-3 py-2 text-sm font-semibold text-rose-700 transition hover:bg-rose-50 disabled:cursor-wait disabled:opacity-60 focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-blue-600 focus-visible:ring-offset-2">
                                            削除
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="9" class="px-4 py-12 text-center text-sm text-slate-500">
                                    該当する予約がありません。
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            {{-- ページネーション --}}
            <div class="mt-6 [&_a]:min-h-11 [&_button]:min-h-11">
                {{ $reservations->links() }}
            </div>
        </section>
    </main>
    {{-- フッター --}}
    <x-footer />
</div>
