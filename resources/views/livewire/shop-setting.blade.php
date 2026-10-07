<div class="w-full min-w-0 overflow-hidden rounded-xl border border-stone-300 bg-white text-stone-900 shadow-sm">

    @php
        $dayOrder = [
            '月曜日', '火曜日', '水曜日', '木曜日',
            '金曜日', '土曜日', '日曜日', '祝日',
        ];

        $sortedClosedDays = array_values(array_intersect($dayOrder, $closed_days));

        $inputClass =
            'h-11 w-full min-w-0 rounded-lg border border-stone-400 bg-white px-3 text-base text-stone-900 transition-colors placeholder:text-stone-500 focus:border-emerald-700 focus:outline-none focus:ring-2 focus:ring-emerald-700/20';
    @endphp

    {{-- 見出し --}}
    <div class="flex items-center justify-between gap-4 border-b border-emerald-200 bg-emerald-50 px-5 py-4 sm:px-6">
        <div class="flex min-w-0 items-center gap-3">
            <svg
                xmlns="http://www.w3.org/2000/svg"
                class="h-5 w-5 shrink-0 text-emerald-700"
                fill="none"
                viewBox="0 0 24 24"
                stroke="currentColor"
                stroke-width="1.8"
                stroke-linecap="round"
                stroke-linejoin="round"
                aria-hidden="true"
            >
                <path d="M3 10.5 12 3l9 7.5" />
                <path d="M5 9v12h14V9" />
                <path d="M9 21v-8h6v8" />
            </svg>

            <h2 class="m-0 text-lg font-semibold">
                店舗設定
            </h2>
        </div>

        <span class="shrink-0 text-xs font-medium text-stone-600">
            {{ $canEdit ? '編集可能' : '閲覧のみ' }}
        </span>
    </div>

    <div class="p-5 sm:p-6">

        @if (session()->has('message'))
            <p
                role="status"
                class="mb-5 rounded-lg border border-emerald-200 bg-emerald-100 px-4 py-3 text-sm text-emerald-900"
            >
                {{ session('message') }}
            </p>
        @endif

        @if ($errors->any())
            <ul
                role="alert"
                class="mb-5 list-none space-y-1 rounded-lg border border-red-200 bg-red-50 p-4 text-sm text-red-700"
            >
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        @endif

        @if ($canEdit)
            <form wire:submit.prevent="save" class="space-y-6">

                {{-- 広い画面では２列、狭い画面では１列 --}}
                <div class="grid grid-cols-1 items-start gap-6 lg:grid-cols-2 lg:gap-x-8">

                    {{-- 店舗名 --}}
                    <div class="min-w-0">
                        <label
                            for="shop-name"
                            class="mb-2 block text-sm font-semibold text-stone-700"
                        >
                            店舗名
                        </label>

                        <input
                            id="shop-name"
                            type="text"
                            wire:model="shop_name"
                            autocomplete="organization"
                            class="{{ $inputClass }}"
                        >
                    </div>

                    {{-- 営業時間 --}}
                    <fieldset class="min-w-0">
                        <legend class="mb-2 text-sm font-semibold text-stone-700">
                            営業時間
                        </legend>

                        <div class="grid min-w-0 grid-cols-[minmax(0,1fr)_auto_minmax(0,1fr)] items-center gap-3">
                            <div class="min-w-0">
                                <label for="business-start" class="sr-only">
                                    開店時間
                                </label>

                                <input
                                    id="business-start"
                                    type="time"
                                    wire:model="business_start"
                                    class="{{ $inputClass }}"
                                >
                            </div>

                            <span class="text-sm text-stone-600" aria-hidden="true">
                                〜
                            </span>

                            <div class="min-w-0">
                                <label for="business-end" class="sr-only">
                                    閉店時間
                                </label>

                                <input
                                    id="business-end"
                                    type="time"
                                    wire:model="business_end"
                                    class="{{ $inputClass }}"
                                >
                            </div>
                        </div>
                    </fieldset>

                    {{-- 表示単位 --}}
                    <div class="min-w-0">
                        <label
                            for="slot-minutes"
                            class="mb-2 block text-sm font-semibold text-stone-700"
                        >
                            表示単位
                        </label>

                        <div class="flex flex-wrap items-center gap-3">
                            <select
                                id="slot-minutes"
                                wire:model="slot_minutes"
                                aria-describedby="slot-minutes-help"
                                class="{{ $inputClass }} max-w-32"
                            >
                                <option value="15">15分</option>
                                <option value="30">30分</option>
                                <option value="60">60分</option>
                            </select>

                            <span id="slot-minutes-help" class="text-sm leading-relaxed text-stone-600">
                                予約表に表示する時間の間隔
                            </span>
                        </div>
                    </div>

                    {{-- 定休日 --}}
                    <fieldset class="min-w-0">
                        <legend class="mb-2 text-sm font-semibold text-stone-700">
                            定休日
                        </legend>

                        <div class="grid grid-cols-2 gap-2 xl:grid-cols-4">
                            @foreach ($dayOrder as $day)
                                <label class="relative block cursor-pointer">
                                    <input
                                        type="checkbox"
                                        value="{{ $day }}"
                                        wire:model="closed_days"
                                        class="peer sr-only"
                                    >

                                    <span class="flex min-h-11 items-center gap-2 rounded-lg border border-stone-300 bg-white px-3 text-sm font-medium text-stone-700 transition-colors hover:border-emerald-400 hover:bg-emerald-50 peer-checked:border-emerald-700 peer-checked:bg-emerald-100 peer-checked:text-emerald-900 peer-focus-visible:outline peer-focus-visible:outline-2 peer-focus-visible:outline-offset-2 peer-focus-visible:outline-emerald-700">
                                        <svg
                                            xmlns="http://www.w3.org/2000/svg"
                                            class="h-4 w-4 shrink-0"
                                            fill="none"
                                            viewBox="0 0 24 24"
                                            stroke="currentColor"
                                            stroke-width="2"
                                            stroke-linecap="round"
                                            stroke-linejoin="round"
                                            aria-hidden="true"
                                        >
                                            <rect x="3" y="3" width="18" height="18" rx="4" />
                                        </svg>

                                        <span class="whitespace-nowrap">{{ $day }}</span>
                                    </span>

                                    <svg
                                        xmlns="http://www.w3.org/2000/svg"
                                        class="pointer-events-none absolute left-3 top-1/2 h-4 w-4 -translate-y-1/2 text-emerald-900 opacity-0 peer-checked:opacity-100"
                                        fill="none"
                                        viewBox="0 0 24 24"
                                        stroke="currentColor"
                                        stroke-width="2"
                                        stroke-linecap="round"
                                        stroke-linejoin="round"
                                        aria-hidden="true"
                                    >
                                        <path d="m7 12 3 3 7-7" />
                                    </svg>
                                </label>
                            @endforeach
                        </div>
                    </fieldset>

                </div>

                {{-- 保存 --}}
                <div class="flex justify-end border-t border-stone-200 pt-5">
                    <button
                        type="submit"
                        wire:loading.attr="disabled"
                        wire:target="save"
                        class="inline-flex min-h-11 min-w-28 cursor-pointer items-center justify-center rounded-lg bg-emerald-700 px-6 text-sm font-semibold text-white transition-colors hover:bg-emerald-800 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-emerald-700 disabled:cursor-wait disabled:opacity-50"
                    >
                        <span wire:loading.remove wire:target="save">保存</span>
                        <span wire:loading wire:target="save">保存中…</span>
                    </button>
                </div>

            </form>
        @else
            {{-- 閲覧 --}}
            <dl class="m-0 grid grid-cols-1 gap-6 sm:grid-cols-2 sm:gap-x-8">
                <div class="min-w-0">
                    <dt class="mb-1.5 text-sm font-medium text-stone-700">
                        店舗名
                    </dt>
                    <dd class="m-0 break-words text-base font-semibold">
                        {{ $shop_name ?: '未設定' }}
                    </dd>
                </div>

                <div>
                    <dt class="mb-1.5 text-sm font-medium text-stone-700">
                        営業時間
                    </dt>
                    <dd class="m-0 text-base font-semibold tabular-nums">
                        @if ($business_start && $business_end)
                            {{ $business_start }} 〜 {{ $business_end }}
                        @else
                            未設定
                        @endif
                    </dd>
                </div>

                <div>
                    <dt class="mb-1.5 text-sm font-medium text-stone-700">
                        表示単位
                    </dt>
                    <dd class="m-0 text-base font-medium">
                        {{ $slot_minutes }}分
                    </dd>
                </div>

                <div class="min-w-0">
                    <dt class="mb-1.5 text-sm font-medium text-stone-700">
                        定休日
                    </dt>
                    <dd class="m-0 flex flex-wrap gap-2">
                        @forelse ($sortedClosedDays as $day)
                            <span class="rounded-md border border-stone-300 bg-stone-100 px-2.5 py-1 text-sm font-medium text-stone-800">
                                {{ $day }}
                            </span>
                        @empty
                            <span class="text-base">なし</span>
                        @endforelse
                    </dd>
                </div>
            </dl>
        @endif

    </div>
</div>