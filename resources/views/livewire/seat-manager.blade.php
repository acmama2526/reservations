<div class="min-h-screen bg-slate-50 text-base leading-relaxed text-slate-800">
    {{-- ヘッダー --}}
    <x-header />
    {{-- メイン --}}
    <main class="mx-auto w-full max-w-screen-2xl px-4 py-6 sm:px-6 lg:px-8 lg:py-8">
        {{-- ページタイトル --}}
        <div class="mb-8">
            <h1 class="text-2xl font-bold leading-snug tracking-tight text-blue-950">
                席マスタ
            </h1>
            <p class="mt-2 text-base leading-relaxed text-slate-600">
                席の登録・編集・利用状況を管理します
            </p>
        </div>
        {{-- 席登録・編集フォーム --}}
        <div class="mb-6 overflow-hidden rounded-xl border border-blue-100 bg-white p-5 shadow-sm sm:p-6">
            <div
                class="-mx-5 -mt-5 mb-6 flex items-center justify-between border-b border-blue-100 bg-blue-50 px-5 py-4 sm:-mx-6 sm:-mt-6 sm:px-6">
                <h2 class="flex items-center gap-2 text-lg font-semibold text-blue-950">
                    @if ($editingSeatId)
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 shrink-0" viewBox="0 0 24 24"
                            fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                            stroke-linejoin="round" aria-hidden="true">
                            <path d="m16 3 5 5-12 12-6 1 1-6Z" />
                            <path d="m14 5 5 5" />
                        </svg>
                        席情報の編集
                    @else
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 shrink-0" viewBox="0 0 24 24"
                            fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                            stroke-linejoin="round" aria-hidden="true">
                            <rect x="6" y="3" width="12" height="10" rx="2" />
                            <path d="M5 13h14v4H5zM7 17v4M17 17v4" />
                        </svg>
                        席の新規登録
                    @endif
                </h2>
            </div>
            <form wire:submit="save">
                <div class="grid grid-cols-1 gap-x-6 gap-y-5 md:grid-cols-2 lg:grid-cols-4">
                    {{-- 席名 --}}
                    <div>
                        <label for="seat-management-seat_name" class="mb-2 block text-sm font-semibold text-slate-700">
                            席名
                        </label>
                        <input type="text" id="seat-management-seat_name" wire:model="seat_name"
                            class="min-h-12 w-full rounded-md border border-slate-300 bg-white px-3 py-2.5 text-base text-slate-900 outline-none transition placeholder:text-slate-500 focus:border-blue-600 focus:ring-2 focus:ring-blue-100"
                            placeholder="例：T1">
                    </div>
                    {{-- 種類 --}}
                    <div>
                        <label for="seat-management-type" class="mb-2 block text-sm font-semibold text-slate-700">
                            種類
                        </label>
                        <select id="seat-management-type" wire:model="type"
                            class="min-h-12 w-full rounded-md border border-slate-300 bg-white px-3 py-2.5 text-base text-slate-900 outline-none transition placeholder:text-slate-500 focus:border-blue-600 focus:ring-2 focus:ring-blue-100">
                            <option value="">選択してください</option>
                            <option value="テーブル">テーブル</option>
                            <option value="座敷">座敷</option>
                            <option value="カウンター">カウンター</option>
                        </select>
                    </div>
                    {{-- 定員 --}}
                    <div>
                        <label for="seat-management-capacity" class="mb-2 block text-sm font-semibold text-slate-700">
                            定員
                        </label>
                        <input type="number" id="seat-management-capacity" wire:model="capacity"
                            class="min-h-12 w-full rounded-md border border-slate-300 bg-white px-3 py-2.5 text-base text-slate-900 outline-none transition placeholder:text-slate-500 focus:border-blue-600 focus:ring-2 focus:ring-blue-100"
                            min="1">
                    </div>
                    {{-- 表示順 --}}
                    <div>
                        <label for="seat-management-display_order"
                            class="mb-2 block text-sm font-semibold text-slate-700">
                            表示順
                        </label>
                        <input type="number" id="seat-management-display_order" wire:model="display_order"
                            class="min-h-12 w-full rounded-md border border-slate-300 bg-white px-3 py-2.5 text-base text-slate-900 outline-none transition placeholder:text-slate-500 focus:border-blue-600 focus:ring-2 focus:ring-blue-100"
                            min="1">
                    </div>
                </div>
                {{-- 利用可能 --}}
                <div class="mt-4">
                    <label
                        class="inline-flex min-h-11 cursor-pointer items-center gap-3 rounded-md px-2 text-base font-medium text-slate-700 hover:bg-slate-50">
                        <input type="checkbox" wire:model="is_active"
                            class="h-5 w-5 rounded border-slate-300 accent-blue-600 focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-blue-600 focus-visible:ring-offset-2">
                        利用可能
                    </label>
                </div>
                {{-- ボタン --}}
                <div class="mt-6 flex flex-wrap gap-3">
                    <button wire:loading.attr="disabled" type="submit"
                        class="inline-flex min-h-12 items-center justify-center gap-2 rounded-md bg-blue-600 px-5 py-2.5 text-base font-semibold text-white transition hover:bg-blue-700 disabled:cursor-wait disabled:opacity-60 focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-blue-600 focus-visible:ring-offset-2">
                        @if ($editingSeatId)
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 shrink-0" viewBox="0 0 24 24"
                                fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                                stroke-linejoin="round" aria-hidden="true">
                                <path d="m5 12 4 4L19 6" />
                            </svg>
                            更新
                        @else
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 shrink-0" viewBox="0 0 24 24"
                                fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                                stroke-linejoin="round" aria-hidden="true">
                                <path d="M12 5v14M5 12h14" />
                            </svg>
                            新規登録
                        @endif
                    </button>
                    @if ($editingSeatId)
                        <button wire:loading.attr="disabled" type="button" wire:click="cancelEdit"
                            class="inline-flex min-h-12 items-center justify-center gap-2 rounded-md border border-slate-300 bg-white px-5 py-2.5 text-base font-semibold text-slate-700 transition hover:bg-slate-100 disabled:cursor-wait disabled:opacity-60 focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-blue-600 focus-visible:ring-offset-2">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 shrink-0" viewBox="0 0 24 24"
                                fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                                stroke-linejoin="round" aria-hidden="true">
                                <path d="m6 6 12 12M6 18 18 6" />
                            </svg>
                            キャンセル
                        </button>
                    @endif
                </div>
            </form>
        </div>
        {{-- 席一覧 --}}
        <div class="overflow-hidden rounded-xl border border-blue-100 bg-white shadow-sm">
            <div class="border-b border-blue-100 bg-blue-50 px-5 py-4 sm:px-6">
                <h2 class="flex items-center gap-2 text-lg font-semibold text-blue-950">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 shrink-0" viewBox="0 0 24 24"
                        fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                        stroke-linejoin="round" aria-hidden="true">
                        <rect x="4" y="3" width="16" height="18" rx="2" />
                        <path d="M8 8h8M8 12h8M8 16h5" />
                    </svg>
                    席マスター一覧
                </h2>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full min-w-[760px] border-collapse text-left text-sm text-slate-800">
                    <thead class="bg-slate-50 text-sm text-slate-600">
                        <tr>
                            <th scope="col" class="whitespace-nowrap px-5 py-3 text-sm font-semibold">表示順</th>
                            <th scope="col" class="whitespace-nowrap px-5 py-3 text-sm font-semibold">席名</th>
                            <th scope="col" class="whitespace-nowrap px-5 py-3 text-sm font-semibold">種類</th>
                            <th scope="col" class="whitespace-nowrap px-5 py-3 text-sm font-semibold">定員</th>
                            <th scope="col" class="whitespace-nowrap px-5 py-3 text-sm font-semibold">利用状況</th>
                            <th scope="col" class="whitespace-nowrap px-5 py-3 text-sm font-semibold">操作</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-200">
                        @foreach ($seats as $seat)
                            <tr wire:key="seat-{{ $seat->id }}"
                                class="transition-colors hover:bg-blue-50/60 focus-within:bg-blue-50/60">
                                <td class="px-5 py-4 align-middle">
                                    {{ $seat->display_order }}
                                </td>
                                <td class="px-5 py-4 align-middle text-base font-semibold text-slate-950">
                                    {{ $seat->seat_name }}
                                </td>
                                <td class="px-5 py-4 align-middle">
                                    {{ $seat->type }}
                                </td>
                                <td class="px-5 py-4 align-middle">
                                    {{ $seat->capacity }} 名
                                </td>
                                <td class="px-5 py-4 align-middle">
                                    @if ($seat->is_active)
                                        <span
                                            class="inline-flex min-w-[88px] items-center justify-center whitespace-nowrap rounded-md bg-blue-100 px-3 py-1.5 text-sm font-semibold text-blue-800">
                                            利用可能
                                        </span>
                                    @else
                                        <span
                                            class="inline-flex min-w-[88px] items-center justify-center whitespace-nowrap rounded-md bg-slate-100 px-3 py-1.5 text-sm font-semibold text-slate-600">
                                            停止中
                                        </span>
                                    @endif
                                </td>
                                <td class="px-5 py-4 align-middle">
                                    <button wire:loading.attr="disabled" type="button"
                                        wire:click="edit({{ $seat->id }})"
                                        class="inline-flex min-h-11 items-center justify-center gap-2 rounded-md border border-slate-300 bg-white px-4 py-2 text-sm font-semibold text-slate-700 transition hover:bg-slate-100 disabled:cursor-wait disabled:opacity-60 focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-blue-600 focus-visible:ring-offset-2">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 shrink-0"
                                            viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                            stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                                            aria-hidden="true">
                                            <path d="m16 3 5 5-12 12-6 1 1-6Z" />
                                            <path d="m14 5 5 5" />
                                        </svg>
                                        編集
                                    </button>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
        {{-- 空席検索 --}}
        <div class="mt-6 overflow-hidden rounded-xl border border-blue-100 bg-white p-5 shadow-sm sm:p-6">
            <h2
                class="-mx-5 -mt-5 mb-6 flex items-center gap-2 border-b border-blue-100 bg-blue-50 px-5 py-4 text-lg font-semibold text-blue-950 sm:-mx-6 sm:-mt-6 sm:px-6">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 shrink-0" viewBox="0 0 24 24" fill="none"
                    stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                    aria-hidden="true">
                    <circle cx="10.5" cy="10.5" r="6.5" />
                    <path d="m16 16 4.5 4.5" />
                </svg>
                空席検索
            </h2>
            <div class="grid grid-cols-1 gap-x-6 gap-y-5 md:grid-cols-2 lg:grid-cols-4">
                <div>
                    <label for="seat-management-people" class="mb-2 block text-sm font-semibold text-slate-700">
                        人数
                    </label>
                    <input type="number" min="1" id="seat-management-people" wire:model="people"
                        class="min-h-12 w-full rounded-md border border-slate-300 bg-white px-3 py-2.5 text-base text-slate-900 outline-none transition placeholder:text-slate-500 focus:border-blue-600 focus:ring-2 focus:ring-blue-100">
                </div>
                <div>
                    <label for="seat-management-reservation_date"
                        class="mb-2 block text-sm font-semibold text-slate-700">
                        予約日
                    </label>
                    <input type="date" id="seat-management-reservation_date" wire:model="reservation_date"
                        class="min-h-12 w-full rounded-md border border-slate-300 bg-white px-3 py-2.5 text-base text-slate-900 outline-none transition placeholder:text-slate-500 focus:border-blue-600 focus:ring-2 focus:ring-blue-100">
                </div>
                <div>
                    <label for="seat-management-start_time" class="mb-2 block text-sm font-semibold text-slate-700">
                        開始時刻
                    </label>
                    <input type="time" id="seat-management-start_time" wire:model="start_time"
                        class="min-h-12 w-full rounded-md border border-slate-300 bg-white px-3 py-2.5 text-base text-slate-900 outline-none transition placeholder:text-slate-500 focus:border-blue-600 focus:ring-2 focus:ring-blue-100">
                </div>
                <div>
                    <label for="seat-management-end_time" class="mb-2 block text-sm font-semibold text-slate-700">
                        終了時刻
                    </label>
                    <input type="time" id="seat-management-end_time" wire:model="end_time"
                        class="min-h-12 w-full rounded-md border border-slate-300 bg-white px-3 py-2.5 text-base text-slate-900 outline-none transition placeholder:text-slate-500 focus:border-blue-600 focus:ring-2 focus:ring-blue-100">
                </div>
            </div>
            {{-- 営業時間外の場合のエラー表示 --}}
            @if (session()->has('error'))
                <div role="alert"
                    class="mt-4 rounded-lg border border-rose-200 bg-rose-50 px-4 py-3 text-sm font-medium text-rose-800">
                    {{ session('error') }}
                </div>
            @endif
            <div class="mt-4">
                <button wire:loading.attr="disabled" type="button" wire:click="searchAvailableSeats"
                    class="inline-flex min-h-12 items-center justify-center gap-2 rounded-md bg-blue-600 px-5 py-2.5 text-base font-semibold text-white transition hover:bg-blue-700 disabled:cursor-wait disabled:opacity-60 focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-blue-600 focus-visible:ring-offset-2">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 shrink-0" viewBox="0 0 24 24"
                        fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                        stroke-linejoin="round" aria-hidden="true">
                        <circle cx="10.5" cy="10.5" r="6.5" />
                        <path d="m16 16 4.5 4.5" />
                    </svg>
                    空席を検索
                </button>
            </div>
            <div class="mt-6">
                <h3 class="mb-3 text-base font-semibold text-blue-950">
                    空いている席
                </h3>
                @if (count($availableSeatIds) > 0)
                    <div class="flex flex-wrap gap-2">
                        @foreach ($seats->whereIn('id', $availableSeatIds) as $seat)
                            <span
                                class="inline-flex items-center rounded-md border border-blue-200 bg-blue-50 px-4 py-2 text-base font-semibold text-blue-800">
                                {{ $seat->seat_name }}
                                （{{ $seat->capacity }}名）
                            </span>
                        @endforeach
                    </div>
                @else
                    <p class="text-sm leading-relaxed text-slate-600">
                        条件を入力して「空席を検索」を押してください。
                    </p>
                @endif
            </div>
            <button wire:loading.attr="disabled" type="button" wire:click="autoAssignSeats"
                class="mt-6 inline-flex min-h-12 items-center justify-center gap-2 rounded-md border border-slate-300 bg-white px-5 py-2.5 text-base font-semibold text-slate-700 transition hover:bg-slate-100 disabled:cursor-wait disabled:opacity-60 focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-blue-600 focus-visible:ring-offset-2">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 shrink-0" viewBox="0 0 24 24" fill="none"
                    stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                    aria-hidden="true">
                    <rect x="3" y="3" width="7" height="7" rx="1" />
                    <rect x="14" y="3" width="7" height="7" rx="1" />
                    <rect x="3" y="14" width="7" height="7" rx="1" />
                    <rect x="14" y="14" width="7" height="7" rx="1" />
                </svg>
                自動配置
            </button>
            <div class="mt-6">
                <h3 class="mb-3 text-base font-semibold text-blue-950">
                    自動配置結果
                </h3>
                @if (count($selectedSeatIds) > 0)
                    <div class="flex flex-wrap gap-2">
                        @foreach ($seats->whereIn('id', $selectedSeatIds) as $seat)
                            <span
                                class="inline-flex items-center rounded-md border border-blue-200 bg-blue-50 px-4 py-2 text-base font-semibold text-blue-800">
                                {{ $seat->seat_name }}
                                （{{ $seat->capacity }}名）
                            </span>
                        @endforeach
                    </div>
                @else
                    <p class="text-sm leading-relaxed text-slate-600">
                        自動配置結果はありません。
                    </p>
                @endif
            </div>
            {{-- 予約ID入力欄（テスト用） --}}
            <div class="mt-4">
                <label for="seat-management-reservationId" class="mb-2 block text-sm font-semibold text-slate-700">
                    予約ID（テスト用）
                </label>
                <input type="number" min="1" id="seat-management-reservationId" wire:model="reservationId"
                    class="min-h-12 w-full sm:w-48 rounded-md border border-slate-300 bg-white px-3 py-2.5 text-base text-slate-900 outline-none transition placeholder:text-slate-500 focus:border-blue-600 focus:ring-2 focus:ring-blue-100">
            </div>
            <div class="mt-4">
                <button wire:loading.attr="disabled" type="button" wire:click="saveAssignment"
                    class="inline-flex min-h-12 items-center justify-center gap-2 rounded-md bg-blue-600 px-5 py-2.5 text-base font-semibold text-white transition hover:bg-blue-700 disabled:cursor-wait disabled:opacity-60 focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-blue-600 focus-visible:ring-offset-2">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 shrink-0" viewBox="0 0 24 24"
                        fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                        stroke-linejoin="round" aria-hidden="true">
                        <path d="M19 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h12l4 4v12a2 2 0 0 1-2 2Z" />
                        <path d="M7 3v6h9V3M7 21v-8h10v8" />
                    </svg>
                    配置を保存
                </button>
            </div>
            @if (session()->has('message'))
                <div role="status" aria-live="polite"
                    class="mt-4 rounded-lg border border-green-200 bg-green-50 px-4 py-3 text-sm font-medium text-green-800">
                    {{ session('message') }}
                </div>
            @endif
        </div>
    </main>
    {{-- フッター --}}
    <x-footer />
</div>
