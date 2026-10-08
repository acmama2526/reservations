<div class="min-h-screen bg-stone-50 text-base leading-relaxed text-stone-900">
    @php
        // p-1にも同じ状態と配色を使う。
        $reservationStatusClasses = [
            'temporary' => 'border-amber-400 bg-amber-50 text-amber-950 focus:border-amber-600 focus:ring-amber-600/20',
            'reserved' => 'border-blue-400 bg-blue-50 text-blue-950 focus:border-blue-600 focus:ring-blue-600/20',
            'visited' => 'border-emerald-400 bg-emerald-50 text-emerald-950 focus:border-emerald-600 focus:ring-emerald-600/20',
            'paid' => 'border-violet-400 bg-violet-50 text-violet-950 focus:border-violet-600 focus:ring-violet-600/20',
            'cancelled' => 'border-stone-400 bg-stone-100 text-stone-700 focus:border-stone-600 focus:ring-stone-600/20',
        ];
    @endphp
    {{-- ヘッダー --}}
    <x-header />
    {{-- メイン --}}
    <main class="mx-auto w-full max-w-screen-2xl px-4 py-5 sm:px-6 lg:px-8 lg:py-6">
        {{-- フラッシュメッセージ --}}
        @if (session()->has('message'))
            <div role="status" aria-live="polite"
                class="mb-6 rounded-lg border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm text-emerald-700">
                {{ session('message') }}
            </div>
        @endif
        {{-- 検索条件 --}}
        <section aria-labelledby="reservation-search-title" class="mb-5 overflow-hidden rounded-xl border border-stone-300 bg-white p-5 shadow-sm sm:p-6">
            <h2 id="reservation-search-title"
                class="flex items-center gap-2 -mx-5 -mt-5 mb-6 border-b border-emerald-200 bg-emerald-50 px-5 py-4 text-lg font-semibold text-stone-900 sm:-mx-6 sm:-mt-6 sm:px-6">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 shrink-0" viewBox="0 0 24 24" fill="none"
                    stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                    aria-hidden="true">
                    <circle cx="10.5" cy="10.5" r="6.5" />
                    <path d="m16 16 4.5 4.5" />
                </svg>
                検索条件
            </h2>
            <div class="grid grid-cols-1 gap-x-6 gap-y-4 md:grid-cols-2 lg:grid-cols-3">
                {{-- 日付 From --}}
                <div>
                    <label for="reservation-filter-dateFrom" class="mb-2 block text-sm font-semibold text-stone-700">
                        日付（開始）
                    </label>
                    <input type="date" id="reservation-filter-dateFrom" wire:model="dateFrom"
                        class="min-h-12 w-full rounded-lg border border-stone-400 bg-white px-3 py-2.5 text-base text-stone-900 outline-none transition placeholder:text-stone-600 focus:border-emerald-700 focus:ring-2 focus:ring-emerald-700/20">
                </div>
                {{-- 日付 To --}}
                <div>
                    <label for="reservation-filter-dateTo" class="mb-2 block text-sm font-semibold text-stone-700">
                        日付（終了）
                    </label>
                    <input type="date" id="reservation-filter-dateTo" wire:model="dateTo"
                        class="min-h-12 w-full rounded-lg border border-stone-400 bg-white px-3 py-2.5 text-base text-stone-900 outline-none transition placeholder:text-stone-600 focus:border-emerald-700 focus:ring-2 focus:ring-emerald-700/20">
                </div>
                {{-- お名前 --}}
                <div>
                    <label for="reservation-filter-customerName"
                        class="mb-2 block text-sm font-semibold text-stone-700">
                        お名前
                    </label>
                    <input type="text" id="reservation-filter-customerName" wire:model="customerName"
                        placeholder="例）山田 太郎"
                        class="min-h-12 w-full rounded-lg border border-stone-400 bg-white px-3 py-2.5 text-base text-stone-900 outline-none transition placeholder:text-stone-600 focus:border-emerald-700 focus:ring-2 focus:ring-emerald-700/20">
                </div>
                {{-- 人数 --}}
                <div>
                    <label for="reservation-filter-people" class="mb-2 block text-sm font-semibold text-stone-700">
                        人数
                    </label>
                    <select id="reservation-filter-people" wire:model="people"
                        class="min-h-12 w-full rounded-lg border border-stone-400 bg-white px-3 py-2.5 text-base text-stone-900 outline-none transition placeholder:text-stone-600 focus:border-emerald-700 focus:ring-2 focus:ring-emerald-700/20">
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
                    <label for="reservation-filter-status" class="mb-2 block text-sm font-semibold text-stone-700">
                        状態
                    </label>
                    <select id="reservation-filter-status" wire:model="status"
                        class="min-h-12 w-full rounded-lg border border-stone-400 bg-white px-3 py-2.5 text-base text-stone-900 outline-none transition placeholder:text-stone-600 focus:border-emerald-700 focus:ring-2 focus:ring-emerald-700/20">
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
                    <label for="reservation-filter-seat" class="mb-2 block text-sm font-semibold text-stone-700">
                        席
                    </label>
                    <select id="reservation-filter-seat" wire:model="seat"
                        class="min-h-12 w-full rounded-lg border border-stone-400 bg-white px-3 py-2.5 text-base text-stone-900 outline-none transition placeholder:text-stone-600 focus:border-emerald-700 focus:ring-2 focus:ring-emerald-700/20">
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
            <div class="mt-5 flex flex-col gap-3 border-t border-stone-200 pt-5 sm:flex-row sm:items-center sm:justify-between">
                <button type="button" wire:click="clearSearch" wire:loading.attr="disabled"
                    wire:target="search,clearSearch"
                    class="inline-flex min-h-12 cursor-pointer items-center justify-center gap-2 rounded-lg border border-stone-300 bg-white px-4 py-2.5 text-sm font-medium text-stone-700 transition hover:bg-stone-100 disabled:cursor-wait disabled:opacity-60 focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-emerald-700 focus-visible:ring-offset-2">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 shrink-0" viewBox="0 0 24 24" fill="none"
                        stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                        aria-hidden="true">
                        <path d="M3 10a9 9 0 1 1 2.6 8.4M3 4v6h6" />
                    </svg>
                    条件をクリア
                </button>
                <button type="button" wire:click="search" wire:loading.attr="disabled" wire:target="search,clearSearch"
                    class="inline-flex min-h-12 cursor-pointer items-center justify-center gap-2 rounded-lg min-w-[144px] bg-emerald-700 px-8 py-2.5 text-base font-semibold text-white transition hover:bg-emerald-800 disabled:cursor-wait disabled:opacity-60 focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-emerald-700 focus-visible:ring-offset-2">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 shrink-0" viewBox="0 0 24 24" fill="none"
                        stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                        aria-hidden="true">
                        <circle cx="10.5" cy="10.5" r="6.5" />
                        <path d="m16 16 4.5 4.5" />
                    </svg>
                    検索する
                </button>
            </div>
        </section>
        {{-- 一覧 --}}
        <section aria-labelledby="reservation-list-title" class="overflow-hidden rounded-xl border border-stone-300 bg-white p-5 shadow-sm sm:p-6">
            <div
                class="-mx-5 -mt-5 mb-6 flex flex-col gap-4 border-b border-emerald-200 bg-emerald-50 px-5 py-4 sm:-mx-6 sm:-mt-6 sm:px-6 lg:flex-row lg:items-center lg:justify-between">
                <h2 id="reservation-list-title" class="flex flex-wrap items-center gap-2 text-lg font-semibold text-stone-900">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 shrink-0" viewBox="0 0 24 24" fill="none"
                        stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                        aria-hidden="true">
                        <rect x="4" y="3" width="16" height="18" rx="2" />
                        <path d="M8 8h8M8 12h8M8 16h5" />
                    </svg>
                    予約一覧
                    <span class="ml-2 inline-block whitespace-nowrap rounded-md border border-stone-300 bg-white px-2.5 py-1 text-xs font-medium text-stone-700">
                        （全 {{ $reservations->total() }} 件）
                    </span>
                </h2>
                {{-- 右側のボタン --}}
                <div class="flex flex-wrap items-center gap-3 sm:gap-4">
                    {{-- 新規登録 --}}
                    <a href="{{ route('reservations.create') }}"
                        class="inline-flex min-h-12 cursor-pointer items-center justify-center gap-2 whitespace-nowrap rounded-lg bg-emerald-700 px-5 py-2.5 text-base font-semibold text-white transition hover:bg-emerald-800 focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-emerald-700 focus-visible:ring-offset-2">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 shrink-0" viewBox="0 0 24 24"
                            fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                            stroke-linejoin="round" aria-hidden="true">
                            <path d="M12 5v14M5 12h14" />
                        </svg>
                        新規予約
                    </a>
                    {{-- 印刷 --}}
                    <button type="button" wire:click="printReservations" wire:loading.attr="disabled"
                        wire:target="printReservations"
                        class="inline-flex min-h-12 cursor-pointer items-center justify-center gap-2 whitespace-nowrap rounded-lg border border-stone-400 bg-white px-5 py-2.5 text-base font-semibold text-stone-700 transition hover:bg-emerald-50 disabled:cursor-wait disabled:opacity-60 focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-emerald-700 focus-visible:ring-offset-2">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 shrink-0" viewBox="0 0 24 24"
                            fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                            stroke-linejoin="round" aria-hidden="true">
                            <path d="M6 9V2h12v7" />
                            <path d="M6 18H4a2 2 0 0 1-2-2v-5a2 2 0 0 1 2-2h16a2 2 0 0 1 2 2v5a2 2 0 0 1-2 2h-2" />
                            <path d="M6 14h12v8H6z" />
                        </svg>
                        一覧を印刷
                    </button>
                    {{-- 表示件数 --}}
                    <div class="flex items-center gap-2 whitespace-nowrap text-sm text-stone-600">
                        <span>
                            表示件数
                        </span>
                        <select aria-label="予約の表示件数" wire:model.live="perPage"
                            class="min-h-12 rounded-lg border border-stone-400 bg-white px-3 py-2 text-base text-stone-800 outline-none focus:border-emerald-700 focus:ring-2 focus:ring-emerald-700/20">
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
            <p class="mb-3 text-xs text-stone-600 xl:hidden">一覧は横にスクロールできます。</p>
            {{-- テーブル --}}
            <div role="region" aria-label="予約一覧" tabindex="0" class="overflow-x-auto rounded-lg border border-stone-300 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-emerald-700">
                <table
                    class="[&_th:nth-child(1)]:text-center [&_th:nth-child(4)]:text-center [&_th:nth-child(6)]:text-center w-full min-w-[1180px] border-collapse text-left text-sm text-stone-800">
                    <thead class="bg-stone-100">
                        <tr class="bg-stone-100">
                            <th scope="col"
                                class="whitespace-nowrap border-b border-stone-300 bg-stone-100 px-4 py-3 text-sm font-semibold text-stone-600">
                                No
                            </th>
                            <th scope="col"
                                class="whitespace-nowrap border-b border-stone-300 bg-stone-100 px-4 py-3 text-sm font-semibold text-stone-600">
                                日時
                            </th>
                            <th scope="col"
                                class="whitespace-nowrap border-b border-stone-300 bg-stone-100 px-4 py-3 text-sm font-semibold text-stone-600">
                                お名前
                            </th>
                            <th scope="col"
                                class="whitespace-nowrap border-b border-stone-300 bg-stone-100 px-4 py-3 text-sm font-semibold text-stone-600">
                                人数
                            </th>
                            <th scope="col"
                                class="whitespace-nowrap border-b border-stone-300 bg-stone-100 px-4 py-3 text-sm font-semibold text-stone-600">
                                席
                            </th>
                            <th scope="col"
                                class="whitespace-nowrap border-b border-stone-300 bg-stone-100 px-4 py-3 text-sm font-semibold text-stone-600">
                                状態
                            </th>
                            <th scope="col"
                                class="whitespace-nowrap border-b border-stone-300 bg-stone-100 px-4 py-3 text-sm font-semibold text-stone-600">
                                電話番号
                            </th>
                            <th scope="col"
                                class="whitespace-nowrap border-b border-stone-300 bg-stone-100 px-4 py-3 text-sm font-semibold text-stone-600">
                                備考
                            </th>
                            <th scope="col"
                                class="md:sticky md:right-0 z-20 border-l border-stone-300 text-center whitespace-nowrap border-b border-stone-300 bg-stone-100 px-4 py-3 text-sm font-semibold text-stone-600">
                                操作
                            </th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($reservations as $index => $reservation)
                            <tr wire:key="reservation-{{ $reservation->id }}"
                                class="group transition-colors hover:bg-emerald-50 focus-within:bg-emerald-50">
                                {{-- No --}}
                                <td class="border-b border-stone-300 px-4 py-4 text-center align-middle tabular-nums">
                                    {{ $reservations->firstItem() + $index }}
                                </td>
                                {{-- 日時 --}}
                                <td
                                    class="whitespace-nowrap border-b border-stone-300 px-4 py-4 align-middle leading-6 tabular-nums">
                                    {{ $reservation->reservation_date->format('Y/m/d') }}
                                    ({{ $reservation->reservation_date->locale('ja')->isoFormat('ddd') }})
                                    <br>
                                    <span class="text-base font-semibold text-stone-900">
                                        {{ substr($reservation->start_time, 0, 5) }}
                                        ～
                                        {{ substr($reservation->end_time, 0, 5) }}
                                    </span>
                                </td>
                                {{-- 名前 --}}
                                <td
                                    class="min-w-[120px] break-words border-b border-stone-300 px-4 py-4 align-middle text-base font-semibold text-stone-900">
                                    {{ $reservation->customer_name }}
                                </td>
                                {{-- 人数 --}}
                                <td class="border-b border-stone-300 px-4 py-4 text-center align-middle tabular-nums">
                                    {{ $reservation->people }}名
                                </td>
                                {{-- 席 --}}
                                <td class="min-w-[110px] whitespace-nowrap border-b border-stone-300 px-4 py-4 align-middle">
                                    @forelse ($reservation->seats as $seat)
                                        <div>
                                            {{ $seat->seat_name }}
                                        </div>
                                    @empty
                                        <span class="inline-block whitespace-nowrap rounded-md border border-stone-300 bg-stone-100 px-2 py-1 text-xs font-medium text-stone-700">
                                            未割当
                                        </span>
                                    @endforelse
                                </td>
                                {{-- 状態 --}}
                                <td class="border-b border-stone-300 px-4 py-4 text-center align-middle">
                                    <select wire:change="updateStatus({{ $reservation->id }}, $event.target.value)"
                                        aria-label="{{ $reservation->customer_name }}様の予約状態"
                                        class="min-h-12 min-w-[120px] cursor-pointer rounded-lg border px-3 py-2 text-sm font-semibold outline-none transition focus:ring-2 {{ $reservationStatusClasses[$reservation->status] ?? 'border-stone-400 bg-stone-50 text-stone-900 focus:border-stone-600 focus:ring-stone-600/20' }}">
                                        <option value="temporary" class="bg-amber-50 text-amber-950"
                                            {{ $reservation->status === 'temporary' ? 'selected' : '' }}>
                                            仮予約
                                        </option>
                                        <option value="reserved" class="bg-blue-50 text-blue-950"
                                            {{ $reservation->status === 'reserved' ? 'selected' : '' }}>
                                            確定
                                        </option>
                                        <option value="visited" class="bg-emerald-50 text-emerald-950"
                                            {{ $reservation->status === 'visited' ? 'selected' : '' }}>
                                            来店済
                                        </option>
                                        <option value="paid" class="bg-violet-50 text-violet-950"
                                            {{ $reservation->status === 'paid' ? 'selected' : '' }}>
                                            会計済
                                        </option>
                                        <option value="cancelled" class="bg-stone-100 text-stone-700"
                                            {{ $reservation->status === 'cancelled' ? 'selected' : '' }}>
                                            キャンセル
                                        </option>
                                    </select>
                                </td>
                                {{-- 電話番号 --}}
                                {{-- 10～11桁の電話番号に-をつける --}}
                                <td
                                    class="whitespace-nowrap border-b border-stone-300 px-4 py-4 align-middle leading-6 tabular-nums">
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
                                <td class="min-w-[120px] max-w-[260px] break-words border-b border-stone-300 px-4 py-4 align-middle text-stone-700">
                                    {{ $reservation->description ?? '―' }}
                                </td>
                                {{-- 操作 --}}
                                <td class="md:sticky md:right-0 z-10 border-b border-l border-stone-300 bg-white px-4 py-4 align-middle group-hover:bg-emerald-50 group-focus-within:bg-emerald-50">
                                    <div class="flex items-center justify-end gap-2 whitespace-nowrap">
                                        {{-- 詳細 --}}
                                        <a href="{{ route('reservations.show', $reservation) }}" aria-label="{{ $reservation->customer_name }}様の予約詳細"
                                            class="inline-flex min-h-12 min-w-[64px] cursor-pointer items-center justify-center rounded-lg border border-stone-400 bg-white px-3 py-2 text-sm font-semibold text-emerald-800 transition hover:bg-emerald-100 focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-emerald-700 focus-visible:ring-offset-2">
                                            詳細
                                        </a>
                                        {{-- 編集 --}}
                                        <a href="{{ route('reservations.edit', $reservation) }}" aria-label="{{ $reservation->customer_name }}様の予約を編集"
                                            class="inline-flex min-h-12 min-w-[64px] cursor-pointer items-center justify-center rounded-lg border border-stone-300 bg-white px-3 py-2 text-sm font-semibold text-stone-700 transition hover:bg-stone-100 focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-emerald-700 focus-visible:ring-offset-2">
                                            編集
                                        </a>
                                        {{-- 削除 --}}
                                        <button type="button" wire:click="deleteReservation({{ $reservation->id }})" aria-label="{{ $reservation->customer_name }}様の予約を削除"
                                            wire:loading.attr="disabled" wire:target="deleteReservation"
                                            wire:confirm="{{ $reservation->customer_name }} 様／{{ $reservation->reservation_date->format('Y/m/d') }} {{ substr($reservation->start_time, 0, 5) }} の予約を削除しますか？"
                                            class="inline-flex min-h-12 min-w-[64px] cursor-pointer items-center justify-center rounded-lg border border-rose-200 bg-white px-3 py-2 text-sm font-semibold text-rose-700 transition hover:bg-rose-50 disabled:cursor-wait disabled:opacity-60 focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-rose-700 focus-visible:ring-offset-2">
                                            削除
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="9" class="px-4 py-12 text-center text-sm text-stone-600">
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
