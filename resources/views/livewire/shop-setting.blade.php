<div class="overflow-hidden rounded-lg border border-blue-100 bg-white text-blue-900">

    {{-- 見出し --}}
    <div class="border-b border-blue-100 bg-blue-50 px-5 py-3">
        <h2 class="m-0 flex items-center gap-2 text-base font-bold">
            <svg
                class="h-5 w-5 text-blue-600"
                xmlns="http://www.w3.org/2000/svg"
                viewBox="0 0 24 24"
                fill="none"
                stroke="currentColor"
                stroke-width="2"
                aria-hidden="true"
            >
                <path
                    stroke-linecap="round"
                    stroke-linejoin="round"
                    d="M3 10l9-7 9 7M5 9v12h14V9M9 21v-8h6v8"
                />
            </svg>

            店舗設定
        </h2>
    </div>

    @php
        $inputClass = 'block w-full rounded-md border border-blue-200 bg-white px-3 py-2 text-sm text-blue-900 placeholder:text-slate-400 focus:border-blue-500 focus:outline-none focus:ring-2 focus:ring-blue-100';
    @endphp

    <div class="space-y-5 p-5">

        {{-- 店舗名 --}}
        <div class="grid gap-2 sm:grid-cols-[6rem_minmax(0,1fr)]">
            <label
                for="shop-name"
                class="text-sm font-medium sm:pt-2"
            >
                店舗名
            </label>

            <div>
                <input
                    id="shop-name"
                    type="text"
                    wire:model="shop_name"
                    class="{{ $inputClass }}"
                    placeholder="店舗名を入力"
                >

                @error('shop_name')
                    <p class="mt-1 text-xs text-red-600">
                        {{ $message }}
                    </p>
                @enderror
            </div>
        </div>

        {{-- 営業時間 --}}
        <div class="grid gap-2 sm:grid-cols-[6rem_minmax(0,1fr)]">
            <p class="m-0 text-sm font-medium sm:pt-2">
                営業時間
            </p>

            <div>
                <div class="flex items-center gap-2">
                    <div class="min-w-0 flex-1">
                        <label for="business-start" class="sr-only">
                            営業開始時間
                        </label>

                        <input
                            id="business-start"
                            type="time"
                            wire:model="business_start"
                            class="{{ $inputClass }}"
                        >
                    </div>

                    <span class="shrink-0 text-sm">〜</span>

                    <div class="min-w-0 flex-1">
                        <label for="business-end" class="sr-only">
                            営業終了時間
                        </label>

                        <input
                            id="business-end"
                            type="time"
                            wire:model="business_end"
                            class="{{ $inputClass }}"
                        >
                    </div>
                </div>

                @error('business_start')
                    <p class="mt-1 text-xs text-red-600">
                        {{ $message }}
                    </p>
                @enderror

                @error('business_end')
                    <p class="mt-1 text-xs text-red-600">
                        {{ $message }}
                    </p>
                @enderror
            </div>
        </div>

        {{-- 表示単位 --}}
        <div class="grid gap-2 sm:grid-cols-[6rem_minmax(0,1fr)]">
            <label
                for="slot-minutes"
                class="text-sm font-medium sm:pt-2"
            >
                表示単位
            </label>

            <div>
                <select
                    id="slot-minutes"
                    wire:model="slot_minutes"
                    class="{{ $inputClass }} max-w-40"
                >
                    <option value="15">15分</option>
                    <option value="30">30分</option>
                    <option value="60">60分</option>
                </select>

                @error('slot_minutes')
                    <p class="mt-1 text-xs text-red-600">
                        {{ $message }}
                    </p>
                @enderror
            </div>
        </div>

        {{-- 定休日 --}}
        <fieldset class="m-0 min-w-0 border-0 p-0">
            <legend class="mb-3 text-sm font-medium">
                定休日
            </legend>

            <div class="grid grid-cols-2 gap-x-4 gap-y-3 sm:grid-cols-4">
                @foreach (['月曜日', '火曜日', '水曜日', '木曜日', '金曜日', '土曜日', '日曜日', '祝日'] as $day)
                    <label class="flex cursor-pointer items-center gap-2 text-sm">
                        <input
                            type="checkbox"
                            value="{{ $day }}"
                            wire:model="closed_days"
                            class="h-4 w-4 shrink-0 cursor-pointer accent-blue-600"
                        >

                        <span>{{ $day }}</span>
                    </label>
                @endforeach
            </div>

            @error('closed_days')
                <p class="mt-2 text-xs text-red-600">
                    {{ $message }}
                </p>
            @enderror
        </fieldset>

        {{-- 保存 --}}
        <div class="border-t border-blue-100 pt-4">
            <button
                type="button"
                wire:click="save"
                wire:loading.attr="disabled"
                wire:target="save"
                class="cursor-pointer rounded-md bg-blue-600 px-6 py-2 text-sm font-medium text-white transition-colors hover:bg-blue-700 disabled:cursor-wait disabled:opacity-50"
            >
                保存
            </button>
        </div>

    </div>
</div>