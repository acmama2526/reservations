<header class="w-full border-b border-blue-100 border-t-2 border-t-blue-900 bg-white">

    @php
        $navClass = 'block cursor-pointer whitespace-nowrap border-0 bg-transparent py-2 text-sm font-medium text-blue-900 no-underline transition-colors hover:text-blue-500 focus-visible:text-blue-500';
    @endphp

    <div class="wrapper flex w-full flex-col gap-3 px-6 py-4 md:flex-row md:items-center md:justify-between md:gap-8">

        <h1 class="m-0 shrink-0 whitespace-nowrap text-2xl font-bold leading-tight text-blue-900">
            予約管理システム
        </h1>

        <nav aria-label="メインメニュー" class="min-w-0 overflow-x-auto">
            <ul class="m-0 flex list-none items-center gap-6 p-0">

                <li class="shrink-0">
                    <a href="{{ route('top') }}" class="{{ $navClass }}">
                        予約管理
                    </a>
                </li>

                <li class="shrink-0">
                    <a href="{{ route('reservations.index') }}" class="{{ $navClass }}">
                        予約一覧・検索
                    </a>
                </li>

                <li class="shrink-0">
                    <a href="{{ route('seats.index') }}" class="{{ $navClass }}">
                        席マスタ
                    </a>
                </li>

                <li class="shrink-0">
                    <a href="{{ route('admin-dashboard') }}" class="{{ $navClass }}">
                        店舗・スタッフ管理
                    </a>
                </li>

                @guest
                    <li class="shrink-0">
                        <button
                            type="button"
                            class="{{ $navClass }}"
                            onclick="this.closest('header').querySelector('dialog').showModal()"
                        >
                            ログイン
                        </button>
                    </li>
                @endguest

                @auth
                    <li class="shrink-0">
                        <form method="POST" action="{{ route('logout') }}" class="m-0">
                            @csrf

                            <button type="submit" class="{{ $navClass }}">
                                ログアウト
                            </button>
                        </form>
                    </li>
                @endauth

            </ul>
        </nav>

    </div>

    @guest
        <dialog
            aria-label="ログイン"
            class="m-auto max-h-[90vh] w-96 max-w-[calc(100vw-2rem)] overflow-y-auto rounded-lg border border-blue-100 bg-white p-0 text-blue-900 shadow-xl backdrop:bg-black/40"
        >
            <div class="relative">
                <button
                    type="button"
                    aria-label="ログイン画面を閉じる"
                    class="absolute right-3 top-2 z-10 cursor-pointer border-0 bg-transparent px-2 text-2xl text-blue-900 hover:text-blue-500"
                    onclick="this.closest('dialog').close()"
                >
                    ×
                </button>

                <livewire:login />
            </div>
        </dialog>
    @endguest

</header>