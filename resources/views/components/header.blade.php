<header class="w-full border-b border-stone-300 border-t-2 border-t-emerald-700 bg-white text-stone-900">

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

        $roleLabel = match (auth()->user()?->role) {
            'admin' => '管理者',
            'manager' => '責任者',
            'staff' => 'スタッフ',
            default => null,
        };

        $accountButtonClass =
            'inline-flex min-h-11 shrink-0 cursor-pointer items-center justify-center whitespace-nowrap rounded-lg border border-stone-300 bg-white px-3 py-2 text-sm font-medium text-stone-700 transition-colors hover:border-emerald-400 hover:bg-emerald-50 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-emerald-700';
    @endphp

    <div class="mx-auto grid w-full max-w-screen-2xl grid-cols-[minmax(0,1fr)_auto] items-center gap-x-4 gap-y-3 px-4 py-3 sm:px-6 xl:grid-cols-[auto_minmax(0,1fr)_16rem] xl:gap-x-6">

        {{-- システム名 --}}
        <h1 class="order-1 m-0 min-w-0 text-base font-semibold leading-snug tracking-tight sm:text-xl">
            <a
                href="{{ route('top') }}"
                class="rounded-sm no-underline transition-colors hover:text-emerald-800 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-4 focus-visible:outline-emerald-700"
            >
                予約管理システム
            </a>
        </h1>

        {{-- ナビゲーション --}}
        <nav
            aria-label="メインメニュー"
            class="order-3 col-span-2 min-w-0 overflow-x-auto xl:order-2 xl:col-span-1"
        >
            <ul class="m-0 flex w-max min-w-full list-none items-center justify-start gap-1 p-1 sm:gap-2 xl:justify-end">
                @foreach ($menus as $menu)
                    <li class="shrink-0">
                        <a
                            href="{{ route($menu['route']) }}"
                            @if ($menu['active'])
                                aria-current="page"
                            @endif
                            @class([
                                'relative flex min-h-12 items-center whitespace-nowrap rounded-lg px-3 py-3 text-sm no-underline transition-colors focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-emerald-700',
                                'bg-emerald-100 font-semibold text-emerald-900' => $menu['active'],
                                'font-medium text-stone-700 hover:bg-emerald-50 hover:text-emerald-900' => ! $menu['active'],
                            ])
                        >
                            {{ $menu['label'] }}

                            @if ($menu['active'])
                                <span
                                    aria-hidden="true"
                                    class="absolute bottom-1 left-3 right-3 h-0.5 rounded-full bg-emerald-700"
                                ></span>
                            @endif
                        </a>
                    </li>
                @endforeach
            </ul>
        </nav>

        {{-- アカウント --}}
        <div class="order-2 flex w-32 min-w-0 items-center justify-end gap-3 sm:w-64 xl:order-3 xl:border-l xl:border-stone-300 xl:pl-5">

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
                    <p class="m-0 hidden text-xs leading-5 text-stone-600 sm:block">
                        {{ $roleLabel ? $roleLabel . 'としてログイン中' : 'ログイン中' }}
                    </p>

                    <p
                        class="m-0 truncate text-sm font-semibold leading-5"
                        title="{{ auth()->user()->name }} さん"
                    >
                        {{ auth()->user()->name }} さん
                    </p>
                </div>

                <form method="POST" action="{{ route('logout') }}" class="m-0 shrink-0">
                    @csrf

                    <button type="submit" class="{{ $accountButtonClass }}">
                        ログアウト
                    </button>
                </form>
            @endauth

        </div>
    </div>

    @if (session()->has('permission-message'))
        <div class="mx-auto max-w-screen-2xl px-4 pb-3 sm:px-6">
            <p
                role="alert"
                class="m-0 rounded-lg border border-red-200 bg-red-50 px-4 py-3 text-sm leading-relaxed text-red-700"
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
            class="m-auto max-h-[90vh] w-96 max-w-[calc(100vw-2rem)] overflow-y-auto rounded-xl border border-stone-300 bg-white p-0 text-stone-900 shadow-xl backdrop:bg-stone-950/40"
        >
            <div class="relative px-4 pb-4 pt-14">
                <button
                    type="button"
                    aria-label="ログイン画面を閉じる"
                    class="absolute right-2 top-2 inline-flex h-11 w-11 cursor-pointer items-center justify-center rounded-lg border-0 bg-transparent text-stone-600 transition-colors hover:bg-emerald-50 hover:text-emerald-900 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-emerald-700"
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