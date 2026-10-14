<div class="min-h-screen bg-stone-50">

    {{-- ヘッダー --}}

    <x-header />

    {{-- メイン --}}

    <main class="mx-auto max-w-7xl px-4 py-6 sm:px-6">

        {{-- 席登録・編集フォーム --}}

        <div id="seat-edit-form"
            class="mb-6 overflow-hidden rounded-xl border border-stone-300 bg-white p-5 shadow-sm sm:p-6">

            <div
                class="-mx-5 -mt-5 mb-6 flex items-center justify-between border-b border-emerald-200 bg-emerald-50 px-5 py-5 sm:-mx-6 sm:-mt-6 sm:px-6">

                <h2 class="flex items-center gap-3 text-lg font-semibold text-slate-950 [&>svg]:text-emerald-600">

                    @if ($editingSeatId)
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 shrink-0" viewBox="0 0 24 24" fill="none"
                            stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                            aria-hidden="true">

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

                <div class="grid grid-cols-1 gap-x-8 gap-y-6 md:grid-cols-2 lg:grid-cols-4">

                    {{-- 席名 --}}

                    <div>

                        <label for="seat-management-seat_name" class="mb-2 block text-sm font-medium text-slate-900">

                            席名

                        </label>

                        <input type="text" id="seat-management-seat_name" wire:model="seat_name"
                            class="min-h-11 w-full rounded-lg border border-stone-400 bg-white px-3 py-2.5 text-base text-slate-900 outline-none transition placeholder:text-stone-400 focus:border-emerald-600 focus:ring-2 focus:ring-emerald-100"
                            placeholder="例：T1">

                        @error('seat_name')
                            <p class="mt-1 text-sm text-red-600">

                                {{ $message }}

                            </p>
                        @enderror

                    </div>

                    {{-- 種類 --}}

                    <div>

                        <label for="seat-management-type" class="mb-2 block text-sm font-medium text-slate-900">

                            種類

                        </label>

                        <select id="seat-management-type" wire:model="type"
                            class="min-h-11 w-full rounded-lg border border-stone-400 bg-white px-3 py-2.5 text-base text-slate-900 outline-none transition focus:border-emerald-600 focus:ring-2 focus:ring-emerald-100">

                            <option value="">選択してください</option>

                            <option value="テーブル">テーブル</option>

                            <option value="座敷">座敷</option>

                            <option value="カウンター">カウンター</option>

                        </select>

                        @error('type')
                            <p class="mt-1 text-sm text-red-600">

                                {{ $message }}

                            </p>
                        @enderror

                    </div>

                    {{-- 定員 --}}

                    <div>

                        <label for="seat-management-capacity" class="mb-2 block text-sm font-medium text-slate-900">

                            定員

                        </label>

                        <input type="number" id="seat-management-capacity" wire:model="capacity" min="1"
                            class="min-h-11 w-full rounded-lg border border-stone-400 bg-white px-3 py-2.5 text-base text-slate-900 outline-none transition focus:border-emerald-600 focus:ring-2 focus:ring-emerald-100">

                        @error('capacity')
                            <p class="mt-1 text-sm text-red-600">

                                {{ $message }}

                            </p>
                        @enderror

                    </div>

                    {{-- 表示順 --}}

                    <div>

                        <label for="seat-management-display_order"
                            class="mb-2 block text-sm font-medium text-slate-900">

                            表示順

                        </label>

                        <input type="number" id="seat-management-display_order" wire:model="display_order"
                            min="1"
                            class="min-h-11 w-full rounded-lg border border-stone-400 bg-white px-3 py-2.5 text-base text-slate-900 outline-none transition focus:border-emerald-600 focus:ring-2 focus:ring-emerald-100">

                        @error('display_order')
                            <p class="mt-1 text-sm text-red-600">

                                {{ $message }}

                            </p>
                        @enderror

                    </div>

                </div>

                {{-- 利用可能 --}}

                <div class="mt-4">

                    <label
                        class="inline-flex min-h-11 cursor-pointer items-center gap-3 rounded-lg border border-stone-300 bg-white px-3 text-sm font-medium text-slate-900 transition hover:bg-emerald-50">

                        <input type="checkbox" wire:model="is_active"
                            class="h-4 w-4 rounded border-stone-400 accent-emerald-600 focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-emerald-600 focus-visible:ring-offset-2">

                        利用可能

                    </label>

                </div>

                {{-- ボタン --}}

                <div class="mt-6 flex flex-wrap justify-end gap-3 border-t border-stone-200 pt-5">

                    <button wire:loading.attr="disabled" type="submit"
                        class="inline-flex min-h-11 min-w-28 items-center justify-center gap-2 rounded-lg bg-emerald-700 px-5 py-2.5 text-sm font-semibold text-white transition hover:bg-emerald-800 disabled:cursor-wait disabled:opacity-60 focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-emerald-600 focus-visible:ring-offset-2">

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
                            class="inline-flex min-h-11 items-center justify-center gap-2 rounded-lg border border-stone-400 bg-white px-5 py-2.5 text-sm font-semibold text-emerald-800 transition hover:bg-emerald-50 disabled:cursor-wait disabled:opacity-60 focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-emerald-600 focus-visible:ring-offset-2">

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

        <div class="overflow-hidden rounded-xl border border-stone-300 bg-white shadow-sm">

            <div class="border-b border-emerald-200 bg-emerald-50 px-5 py-5 sm:px-6">

                <h2 class="flex items-center gap-3 text-lg font-semibold text-slate-950 [&>svg]:text-emerald-600">

                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 shrink-0" viewBox="0 0 24 24"
                        fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                        stroke-linejoin="round" aria-hidden="true">

                        <rect x="4" y="3" width="16" height="18" rx="2" />

                        <path d="M8 8h8M8 12h8M8 16h5" />

                    </svg>

                    席マスター一覧

                </h2>

            </div>

            <div class="overflow-x-auto p-5 sm:p-6">

                <table
                    class="w-full min-w-[760px] border-separate border-spacing-0 overflow-hidden rounded-lg border border-stone-300 text-left text-sm text-slate-900">

                    <thead class="bg-stone-100 text-sm text-slate-800">

                        <tr>

                            <th scope="col"
                                class="whitespace-nowrap border-b border-stone-300 px-5 py-3 text-sm font-medium">

                                表示順

                            </th>

                            <th scope="col"
                                class="whitespace-nowrap border-b border-stone-300 px-5 py-3 text-sm font-medium">

                                席名

                            </th>

                            <th scope="col"
                                class="whitespace-nowrap border-b border-stone-300 px-5 py-3 text-sm font-medium">

                                種類

                            </th>

                            <th scope="col"
                                class="whitespace-nowrap border-b border-stone-300 px-5 py-3 text-sm font-medium">

                                定員

                            </th>

                            <th scope="col"
                                class="whitespace-nowrap border-b border-stone-300 px-5 py-3 text-sm font-medium">

                                利用状況

                            </th>

                            <th scope="col"
                                class="whitespace-nowrap border-b border-stone-300 px-5 py-3 text-sm font-medium">

                                操作

                            </th>

                        </tr>

                    </thead>

                    <tbody class="bg-white">

                        @foreach ($seats as $seat)
                            <tr wire:key="seat-{{ $seat->id }}"
                                class="transition-colors hover:bg-emerald-50 focus-within:bg-emerald-50 [&:not(:last-child)>td]:border-b [&:not(:last-child)>td]:border-stone-200">

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
                                            class="inline-flex min-w-[88px] items-center justify-center whitespace-nowrap rounded-md border border-emerald-200 bg-emerald-50 px-3 py-1 text-xs font-medium text-emerald-800">

                                            利用可能

                                        </span>
                                    @else
                                        <span
                                            class="inline-flex min-w-[88px] items-center justify-center whitespace-nowrap rounded-md border border-stone-300 bg-stone-100 px-3 py-1 text-xs font-medium text-stone-600">

                                            停止中

                                        </span>
                                    @endif

                                </td>

                                <td class="px-5 py-4 align-middle">

                                    <button wire:loading.attr="disabled" type="button"
                                        wire:click="edit({{ $seat->id }})"
                                        class="inline-flex min-h-11 items-center justify-center gap-2 rounded-lg border border-stone-400 bg-white px-3 py-2 text-sm font-semibold text-emerald-800 transition hover:bg-emerald-50 disabled:cursor-wait disabled:opacity-60 focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-emerald-600 focus-visible:ring-offset-2">

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

        {{-- 編集ボタンを押したときに編集フォームまでスクロール --}}

        <script>
            document.addEventListener('livewire:init', () => {

                Livewire.on('scroll-to-seat-form', () => {

                    document.getElementById('seat-edit-form')?.scrollIntoView({

                        behavior: 'smooth',

                        block: 'start'

                    });

                });

            });
        </script>

    </main>

    {{-- フッター --}}

    <x-footer />

</div>
