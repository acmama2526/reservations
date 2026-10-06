<x-layout>
    @php
        $role = auth()->user()?->role;

        $roleLabel = match ($role) {
            'admin' => '管理者',
            'manager' => '責任者',
            'staff' => 'スタッフ',
            default => '未ログイン',
        };

        $roleClass = match ($role) {
            'admin' => 'bg-rose-100 text-rose-800 ring-1 ring-inset ring-rose-200',
            'manager' => 'bg-purple-100 text-purple-800 ring-1 ring-inset ring-purple-200',
            'staff' => 'bg-blue-100 text-blue-800 ring-1 ring-inset ring-blue-200',
            default => 'bg-slate-100 text-slate-700 ring-1 ring-inset ring-slate-200',
        };
    @endphp

    <main class="min-h-screen bg-slate-50 px-4 py-6 text-base leading-relaxed text-slate-800 sm:px-6 lg:px-8 lg:py-8">

        <div class="mx-auto w-full max-w-screen-2xl">

            {{-- ページの目的とログイン状態 --}}
            <div class="mb-8 flex flex-wrap items-start justify-between gap-4">

                <div class="min-w-0">
                    <h2 class="m-0 text-2xl font-bold leading-snug tracking-tight text-blue-950">
                        店舗・スタッフ管理
                    </h2>

                    <p class="mb-0 mt-2 text-base leading-relaxed text-slate-600">
                        店舗情報と登録ユーザーを確認・管理できます。
                    </p>
                </div>

                <span class="inline-flex min-h-10 shrink-0 items-center rounded-md px-4 py-2 text-sm font-semibold {{ $roleClass }}">
                    {{ $roleLabel }}
                </span>

            </div>

            {{-- 左：店舗情報とユーザー一覧／右：ログイン --}}
            <div
                @class([
                    'grid min-w-0 grid-cols-1 items-start gap-6 lg:gap-8',
                    'xl:grid-cols-[minmax(0,1fr)_360px]' => ! auth()->check(),
                ])
            >

                <div class="min-w-0 space-y-6 lg:space-y-8">

                    {{-- 店舗設定：文字サイズと操作領域を確保 --}}
                    <section
                        aria-label="店舗設定"
                        class="min-w-0
                            [&>div]:!mx-0
                            [&>div]:!w-full
                            [&>div]:!max-w-none
                            [&_h3]:!text-lg
                            [&_label]:!text-sm
                            [&_label]:!font-semibold
                            [&_input:not([type=checkbox]):not([type=radio]):not([type=hidden])]:min-h-11
                            [&_input:not([type=checkbox]):not([type=radio]):not([type=hidden])]:!text-base
                            [&_select]:min-h-11
                            [&_select]:!text-base
                            [&_textarea]:!text-base
                            [&_button]:min-h-11
                            [&_button]:!text-sm
                            [&_button]:!font-semibold
                            [&_button]:focus-visible:outline
                            [&_button]:focus-visible:outline-2
                            [&_button]:focus-visible:outline-offset-2
                            [&_button]:focus-visible:outline-blue-600
                            [&_input]:focus-visible:outline
                            [&_input]:focus-visible:outline-2
                            [&_input]:focus-visible:outline-offset-2
                            [&_input]:focus-visible:outline-blue-600
                            [&_select]:focus-visible:outline
                            [&_select]:focus-visible:outline-2
                            [&_select]:focus-visible:outline-offset-2
                            [&_select]:focus-visible:outline-blue-600"
                    >
                        <livewire:shop-setting />
                    </section>

                    {{-- ユーザー管理：行を追いやすく、操作ボタンの間隔を確保 --}}
                    <section
                        aria-label="ユーザー管理"
                        class="min-w-0
                            [&>div]:!mx-0
                            [&>div]:!w-full
                            [&>div]:!max-w-none
                            [&_h3]:!text-lg
                            [&_table]:!text-sm
                            [&_th]:!py-3
                            [&_th]:!font-semibold
                            [&_td]:!py-4
                            [&_tbody_tr]:transition-colors
                            [&_tbody_tr:hover]:!bg-blue-50
                            [&_label]:!text-sm
                            [&_label]:!font-semibold
                            [&_input:not([type=checkbox]):not([type=radio]):not([type=hidden])]:min-h-11
                            [&_input:not([type=checkbox]):not([type=radio]):not([type=hidden])]:!text-base
                            [&_select]:min-h-11
                            [&_select]:!text-base
                            [&_button]:min-h-11
                            [&_button]:!text-sm
                            [&_button]:!font-semibold
                            [&_button+button]:ml-2
                            [&_button]:focus-visible:outline
                            [&_button]:focus-visible:outline-2
                            [&_button]:focus-visible:outline-offset-2
                            [&_button]:focus-visible:outline-blue-600
                            [&_input]:focus-visible:outline
                            [&_input]:focus-visible:outline-2
                            [&_input]:focus-visible:outline-offset-2
                            [&_input]:focus-visible:outline-blue-600
                            [&_select]:focus-visible:outline
                            [&_select]:focus-visible:outline-2
                            [&_select]:focus-visible:outline-offset-2
                            [&_select]:focus-visible:outline-blue-600"
                    >
                        <livewire:user-management />
                    </section>

                </div>

                @guest
                    <aside
                        aria-label="ログイン"
                        class="min-w-0 space-y-5"
                    >

                        <div class="
                            [&>div]:!mx-0
                            [&>div]:!w-full
                            [&>div]:!max-w-none
                            [&_h3]:!text-lg
                            [&_label]:!text-sm
                            [&_label]:!font-semibold
                            [&_input:not([type=checkbox]):not([type=radio]):not([type=hidden])]:min-h-12
                            [&_input:not([type=checkbox]):not([type=radio]):not([type=hidden])]:!text-base
                            [&_button]:min-h-12
                            [&_button]:!text-base
                            [&_button]:!font-semibold
                            [&_button]:focus-visible:outline
                            [&_button]:focus-visible:outline-2
                            [&_button]:focus-visible:outline-offset-2
                            [&_button]:focus-visible:outline-blue-600
                            [&_input]:focus-visible:outline
                            [&_input]:focus-visible:outline-2
                            [&_input]:focus-visible:outline-offset-2
                            [&_input]:focus-visible:outline-blue-600"
                        >
                            <livewire:login />
                        </div>

                        {{-- 権限の説明 --}}
                        <div class="rounded-lg border border-slate-200 bg-white p-5">

                            <h3 class="m-0 text-base font-semibold text-blue-950">
                                ログイン後にできること
                            </h3>

                            <dl class="mb-0 mt-4 space-y-4 text-sm leading-relaxed">

                                <div class="grid grid-cols-[64px_minmax(0,1fr)] items-start gap-3">
                                    <dt class="font-semibold text-rose-800">
                                        管理者
                                    </dt>

                                    <dd class="m-0 text-slate-600">
                                        店舗設定・席マスタ・ユーザー情報の管理
                                    </dd>
                                </div>

                                <div class="grid grid-cols-[64px_minmax(0,1fr)] items-start gap-3">
                                    <dt class="font-semibold text-purple-800">
                                        責任者
                                    </dt>

                                    <dd class="m-0 text-slate-600">
                                        店舗設定・席マスタの管理
                                    </dd>
                                </div>

                                <div class="grid grid-cols-[64px_minmax(0,1fr)] items-start gap-3">
                                    <dt class="font-semibold text-blue-800">
                                        スタッフ
                                    </dt>

                                    <dd class="m-0 text-slate-600">
                                        このページでは情報の閲覧のみ
                                    </dd>
                                </div>

                            </dl>

                        </div>

                    </aside>
                @endguest

            </div>

        </div>

    </main>
</x-layout>