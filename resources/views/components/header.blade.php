<header class="w-full border-t-2 border-t-blue-900 border-b border-blue-100 bg-white text-blue-950">

    @php
        $menus = [
            [
                'label' => '予約管理',
                'route' => 'top',
                'active' => request()->routeIs('top', 'reservations.create'),
            ],
            [
                'label' => '予約一覧・検索',
                'route' => 'reservations.index',
                'active' => request()->routeIs(
                    'reservations.index',
                    'reservations.show',
                    'reservations.edit'
                ),
            ],
            [
                'label' => '席マスタ',
                'route' => 'seats.index',
                'active' => request()->routeIs('seats.index'),
            ],
            [
                'label' => '店舗・スタッフ管理',
                'route' => 'admin-dashboard',
                'active' => request()->routeIs(
                    'admin-dashboard',
                    'shop_setting',
                    'users.index'
                ),
            ],
        ];

        $accountButtonClass = 'inline-flex shrink-0 cursor-pointer items-center justify-center whitespace-nowrap rounded-md border-0 bg-transparent px-3 py-2 text-sm font-medium text-blue-900 transition-colors hover:bg-blue-50 hover:text-blue-700 focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-blue-500 focus-visible:ring-offset-2';
    @endphp

    <div class="mx-auto grid w-full max-w-screen-2xl grid-cols-[minmax(0,1fr)_auto] items-center gap-x-4 gap-y-3 px-4 py-3 sm:px-6 xl:grid-cols-[auto_minmax(0,1fr)_16rem] xl:gap-x-8">

        {{-- システム名 --}}
        <h1 class="order-1 m-0 min-w-0 text-base font-bold leading-snug tracking-tight text-blue-900 sm:text-xl">
            <a
                href="{{ route('top') }}"
                class="rounded-sm no-underline focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-blue-500 focus-visible:ring-offset-4"
            >
                予約管理システム
            </a>
        </h1>

        {{-- メインナビゲーション --}}
        <nav
            aria-label="メインメニュー"
            class="order-3 col-span-2 min-w-0 overflow-x-auto xl:order-2 xl:col-span-1"
        >
            <ul class="m-0 flex w-max min-w-full list-none items-center justify-end gap-1 p-1 sm:gap-2">

                @foreach ($menus as $menu)
                    <li class="shrink-0">
                        <a
                            href="{{ route($menu['route']) }}"
                            @if ($menu['active'])
                                aria-current="page"
                            @endif
                            @class([
                                'relative block whitespace-nowrap rounded-md px-3 py-3 text-sm no-underline transition-colors focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-blue-500 focus-visible:ring-inset',
                                'bg-blue-50 font-bold text-blue-800' => $menu['active'],
                                'font-medium text-blue-900 hover:bg-blue-50 hover:text-blue-600' => ! $menu['active'],
                            ])
                        >
                            {{ $menu['label'] }}

                            @if ($menu['active'])
                                <span
                                    aria-hidden="true"
                                    class="absolute right-3 bottom-1 left-3 h-0.5 rounded-full bg-blue-600"
                                ></span>
                            @endif
                        </a>
                    </li>
                @endforeach

            </ul>
        </nav>

        {{-- ログイン状態に関係なく同じ幅を確保 --}}
        <div class="order-2 flex w-32 min-w-0 items-center justify-end gap-2 sm:w-64 xl:order-3 xl:border-l xl:border-blue-100 xl:pl-6">

            @guest
                <button
                    type="button"
                    class="{{ $accountButtonClass }}"
                    aria-haspopup="dialog"
                    aria-controls="header-login-modal"
                    onclick="document.getElementById('header-login-modal').showModal()"
                >
                    ログイン
                </button>
            @endguest

            @auth
                <div class="min-w-0 flex-1 text-right">
                    <p class="m-0 hidden text-xs leading-5 text-slate-500 sm:block">
                        ログイン中
                    </p>

                    <p
                        class="m-0 truncate text-sm font-semibold leading-5 text-blue-900"
                        title="{{ auth()->user()->name }} さん"
                    >
                        {{ auth()->user()->name }} さん
                    </p>
                </div>

                <form
                    method="POST"
                    action="{{ route('logout') }}"
                    class="m-0 shrink-0"
                >
                    @csrf

                    <button
                        type="submit"
                        class="{{ $accountButtonClass }}"
                    >
                        ログアウト
                    </button>
                </form>
            @endauth

        </div>

    </div>

    {{-- 権限に関するメッセージ --}}
    @if (session()->has('permission-message'))
        <div class="mx-auto max-w-screen-2xl px-4 pb-3 sm:px-6">
            <p
                role="alert"
                class="m-0 rounded-md border border-red-100 bg-red-50 px-4 py-3 text-sm leading-relaxed text-red-700"
            >
                {{ session('permission-message') }}
            </p>
        </div>
    @endif

    {{-- ログインダイアログ --}}
    @guest
        <dialog
            id="header-login-modal"
            aria-label="ログイン"
            class="m-auto max-h-[90vh] w-96 max-w-[calc(100vw-2rem)] overflow-y-auto rounded-xl border border-blue-100 bg-white p-0 text-blue-900 shadow-xl backdrop:bg-slate-950/40"
        >
            <div class="relative pt-12">

                <button
                    type="button"
                    aria-label="ログイン画面を閉じる"
                    class="absolute top-3 right-3 inline-flex h-8 w-8 cursor-pointer items-center justify-center rounded-md border-0 bg-transparent text-slate-500 transition-colors hover:bg-blue-50 hover:text-blue-900 focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-blue-500"
                    onclick="this.closest('dialog').close()"
                >
                    <svg
                        xmlns="http://www.w3.org/2000/svg"
                        class="h-5 w-5"
                        viewBox="0 0 24 24"
                        fill="none"
                        stroke="currentColor"
                        stroke-width="2"
                        stroke-linecap="round"
                        aria-hidden="true"
                    >
                        <path d="m6 6 12 12M18 6 6 18" />
                    </svg>
                </button>

                <livewire:login />

            </div>
        </dialog>
    @endguest

</header>