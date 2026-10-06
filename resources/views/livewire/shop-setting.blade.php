<div
    class="mx-4 w-auto min-w-0 overflow-hidden rounded-xl border border-blue-100 bg-white text-blue-900 shadow-sm sm:mx-12">

    @php
        $dayOrder = ['月曜日', '火曜日', '水曜日', '木曜日', '金曜日', '土曜日', '日曜日', '祝日'];

        $sortedClosedDays = array_values(array_intersect($dayOrder, $closed_days));

        $inputClass =
            'h-11 w-full min-w-0 rounded-lg border border-blue-200 bg-white px-3 text-sm transition-colors focus:border-blue-500 focus:outline-none focus:ring-2 focus:ring-blue-100';

        $rowClass = 'grid gap-2 sm:grid-cols-[88px_minmax(0,1fr)] sm:items-center sm:gap-6';
    @endphp

    {{-- 見出し --}}
    <div class="flex items-center justify-between gap-4 border-b border-blue-100 bg-blue-50 px-5 py-4 sm:px-6">

        <div class="flex items-center gap-3">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 shrink-0 text-blue-600" fill="none"
                viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"
                aria-hidden="true">
                <path d="M3 10.5 12 3l9 7.5" />
                <path d="M5 9v12h14V9" />
                <path d="M9 21v-8h6v8" />
            </svg>

            <h2 class="m-0 text-base font-bold">
                店舗設定
            </h2>
        </div>

        <span class="shrink-0 text-xs font-medium text-slate-500">
            {{ $canEdit ? '編集可能' : '閲覧のみ' }}
        </span>

    </div>

    <div class="p-5 sm:p-6">

        @if (session()->has('message'))
            <p role="status"
                class="mb-5 rounded-lg border border-green-100 bg-green-50 px-4 py-3 text-sm text-green-700">
                {{ session('message') }}
            </p>
        @endif

        @if ($errors->any())
            <ul role="alert"
                class="mb-5 list-none space-y-1 rounded-lg border border-red-100 bg-red-50 p-4 text-sm text-red-700">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        @endif

        @if ($canEdit)

            <form wire:submit.prevent="save" class="space-y-5">

                {{-- 店舗名 --}}
                <div class="{{ $rowClass }}">
                    <label for="shop-name" class="text-sm font-medium">
                        店舗名
                    </label>

                    <input id="shop-name" type="text" wire:model="shop_name" class="{{ $inputClass }}"
                        autocomplete="organization">
                </div>

                {{-- 営業時間 --}}
                <fieldset class="{{ $rowClass }}">
                    <legend class="sr-only">営業時間</legend>

                    <span class="text-sm font-medium" aria-hidden="true">
                        営業時間
                    </span>

                    <div class="grid min-w-0 grid-cols-[minmax(0,1fr)_auto_minmax(0,1fr)] items-center gap-3">

                        <div class="min-w-0">
                            <label for="business-start" class="mb-1.5 block text-xs text-slate-500">
                                開店
                            </label>

                            <input id="business-start" type="time" wire:model="business_start"
                                class="{{ $inputClass }}">
                        </div>

                        <span class="pt-5 text-sm text-slate-400" aria-hidden="true">
                            〜
                        </span>

                        <div class="min-w-0">
                            <label for="business-end" class="mb-1.5 block text-xs text-slate-500">
                                閉店
                            </label>

                            <input id="business-end" type="time" wire:model="business_end"
                                class="{{ $inputClass }}">
                        </div>

                    </div>
                </fieldset>

                {{-- 表示単位 --}}
                <div class="{{ $rowClass }}">
                    <label for="slot-minutes" class="text-sm font-medium">
                        表示単位
                    </label>

                    <div class="flex flex-wrap items-center gap-3">
                        <select id="slot-minutes" wire:model="slot_minutes" class="{{ $inputClass }} max-w-32">
                            <option value="15">15分</option>
                            <option value="30">30分</option>
                            <option value="60">60分</option>
                        </select>

                        <span class="text-xs leading-relaxed text-slate-500">
                            予約表に表示する時間の間隔
                        </span>
                    </div>
                </div>

                {{-- 定休日 --}}
                <fieldset class="grid gap-3 sm:grid-cols-[88px_minmax(0,1fr)] sm:gap-6">
                    <legend class="sr-only">定休日</legend>

                    <span class="text-sm font-medium sm:pt-3" aria-hidden="true">
                        定休日
                    </span>

                    <div class="grid grid-cols-2 gap-2 sm:grid-cols-4">
                        @foreach ($dayOrder as $day)
                            <label
                                class="flex min-h-11 cursor-pointer items-center gap-2 rounded-lg border border-blue-100 bg-slate-50 px-3 text-sm transition-colors hover:border-blue-300 hover:bg-blue-50">
                                <input type="checkbox" value="{{ $day }}" wire:model="closed_days"
                                    class="h-4 w-4 shrink-0 accent-blue-600 focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-blue-500">

                                <span class="whitespace-nowrap">
                                    {{ $day }}
                                </span>
                            </label>
                        @endforeach
                    </div>
                </fieldset>

                {{-- 保存 --}}
                <div class="flex justify-end border-t border-blue-100 pt-5">
                    <button type="submit" wire:loading.attr="disabled" wire:target="save"
                        class="inline-flex min-h-11 min-w-28 cursor-pointer items-center justify-center rounded-lg bg-blue-600 px-6 text-sm font-semibold text-white transition-colors hover:bg-blue-700 focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-blue-500 focus-visible:ring-offset-2 disabled:cursor-wait disabled:opacity-50">
                        <span wire:loading.remove wire:target="save">
                            保存
                        </span>

                        <span wire:loading wire:target="save">
                            保存中…
                        </span>
                    </button>
                </div>

            </form>
        @else
            {{-- 閲覧時は内容を2列に整理 --}}
            <dl class="m-0 grid grid-cols-1 gap-5 sm:grid-cols-2 sm:gap-x-8 sm:gap-y-6">

                <div class="min-w-0">
                    <dt class="mb-2 text-xs font-medium text-slate-500">
                        店舗名
                    </dt>

                    <dd class="m-0 break-words text-base font-semibold leading-relaxed">
                        {{ $shop_name ?: '未設定' }}
                    </dd>
                </div>

                <div>
                    <dt class="mb-2 text-xs font-medium text-slate-500">
                        営業時間
                    </dt>

                    <dd class="m-0 text-base font-semibold leading-relaxed tabular-nums">
                        @if ($business_start && $business_end)
                            {{ $business_start }} 〜 {{ $business_end }}
                        @else
                            未設定
                        @endif
                    </dd>
                </div>

                <div>
                    <dt class="mb-2 text-xs font-medium text-slate-500">
                        表示単位
                    </dt>

                    <dd class="m-0 text-sm leading-relaxed">
                        {{ $slot_minutes }}分
                    </dd>
                </div>

                <div class="min-w-0">
                    <dt class="mb-2 text-xs font-medium text-slate-500">
                        定休日
                    </dt>

                    <dd class="m-0 flex flex-wrap gap-2">
                        @forelse ($sortedClosedDays as $day)
                            <span class="rounded-md bg-blue-50 px-2.5 py-1 text-sm font-medium text-blue-800">
                                {{ $day }}
                            </span>
                        @empty
                            <span class="text-sm leading-relaxed">
                                なし
                            </span>
                        @endforelse
                    </dd>
                </div>

            </dl>

        @endif

    </div>
</div>
