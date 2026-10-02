<div class="min-h-[400px] w-full min-w-0 rounded-lg border border-blue-100 bg-white text-blue-900">

    <div class="flex items-center gap-2 border-b border-blue-100 bg-blue-50 px-5 py-4">
        <svg
            xmlns="http://www.w3.org/2000/svg"
            class="h-5 w-5 shrink-0 text-blue-600"
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

        <h2 class="m-0 text-sm font-bold">
            店舗設定
        </h2>
    </div>

    <div class="p-5">

        @if (session()->has('message'))
            <p role="status" class="mb-4 rounded bg-green-50 px-3 py-2 text-sm text-green-700">
                {{ session('message') }}
            </p>
        @endif

        @if ($errors->any())
            <ul role="alert" class="mb-4 list-none space-y-1 rounded bg-red-50 p-3 text-sm text-red-700">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        @endif

        @if ($canEdit)

            <form wire:submit.prevent="save" class="space-y-5">

                <div class="grid items-center gap-2 sm:grid-cols-[88px_minmax(0,1fr)] sm:gap-4">
                    <label for="shop-name" class="text-sm font-medium">
                        店舗名
                    </label>

                    <input
                        id="shop-name"
                        type="text"
                        wire:model="shop_name"
                        class="h-10 w-full min-w-0 rounded-md border border-blue-200 bg-white px-3 text-sm focus:border-blue-500 focus:outline-none"
                    >
                </div>

                <fieldset class="grid items-center gap-2 sm:grid-cols-[88px_minmax(0,1fr)] sm:gap-4">
                    <legend class="sr-only">営業時間</legend>

                    <span class="text-sm font-medium" aria-hidden="true">
                        営業時間
                    </span>

                    <div class="flex min-w-0 items-center gap-2">
                        <label for="business-start" class="sr-only">
                            開店時間
                        </label>

                        <input
                            id="business-start"
                            type="time"
                            wire:model="business_start"
                            class="h-10 w-full min-w-0 flex-1 rounded-md border border-blue-200 bg-white px-3 text-sm focus:border-blue-500 focus:outline-none"
                        >

                        <span class="shrink-0">〜</span>

                        <label for="business-end" class="sr-only">
                            閉店時間
                        </label>

                        <input
                            id="business-end"
                            type="time"
                            wire:model="business_end"
                            class="h-10 w-full min-w-0 flex-1 rounded-md border border-blue-200 bg-white px-3 text-sm focus:border-blue-500 focus:outline-none"
                        >
                    </div>
                </fieldset>

                <div class="grid items-center gap-2 sm:grid-cols-[88px_minmax(0,1fr)] sm:gap-4">
                    <label for="slot-minutes" class="text-sm font-medium">
                        表示単位
                    </label>

                    <select
                        id="slot-minutes"
                        wire:model="slot_minutes"
                        class="h-10 w-40 rounded-md border border-blue-200 bg-white px-3 text-sm focus:border-blue-500 focus:outline-none"
                    >
                        <option value="15">15分</option>
                        <option value="30">30分</option>
                        <option value="60">60分</option>
                    </select>
                </div>

                <fieldset>
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
                                    class="h-4 w-4 accent-blue-600"
                                >
                                {{ $day }}
                            </label>
                        @endforeach
                    </div>
                </fieldset>

                <div class="border-t border-blue-100 pt-4">
                    <button
                        type="submit"
                        wire:loading.attr="disabled"
                        wire:target="save"
                        class="cursor-pointer rounded-md bg-blue-600 px-6 py-2 text-sm font-bold text-white transition-colors hover:bg-blue-700 disabled:opacity-50"
                    >
                        保存
                    </button>
                </div>

            </form>

        @else

            <dl class="space-y-5 text-sm">

                <div class="grid items-start gap-2 sm:grid-cols-[88px_minmax(0,1fr)] sm:gap-4">
                    <dt class="py-2 font-medium">店舗名</dt>
                    <dd class="m-0 min-w-0 break-words py-2">
                        {{ $shop_name ?: '未設定' }}
                    </dd>
                </div>

                <div class="grid items-start gap-2 sm:grid-cols-[88px_minmax(0,1fr)] sm:gap-4">
                    <dt class="py-2 font-medium">営業時間</dt>
                    <dd class="m-0 py-2">
                        @if ($business_start && $business_end)
                            {{ $business_start }} 〜 {{ $business_end }}
                        @else
                            未設定
                        @endif
                    </dd>
                </div>

                <div class="grid items-start gap-2 sm:grid-cols-[88px_minmax(0,1fr)] sm:gap-4">
                    <dt class="py-2 font-medium">表示単位</dt>
                    <dd class="m-0 py-2">{{ $slot_minutes }}分</dd>
                </div>

                <div class="grid items-start gap-2 sm:grid-cols-[88px_minmax(0,1fr)] sm:gap-4">
                    <dt class="py-2 font-medium">定休日</dt>
                    <dd class="m-0 min-w-0 break-words py-2">
                        {{ count($closed_days) ? implode('・', $closed_days) : 'なし' }}
                    </dd>
                </div>

            </dl>

        @endif

    </div>
</div>