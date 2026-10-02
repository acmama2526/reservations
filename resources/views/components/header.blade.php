<header class="w-full border-b border-blue-100 border-t-2 border-t-blue-900 bg-white">
    <div class="wrapper flex w-full flex-col gap-3 px-6 py-4 md:flex-row md:items-center md:justify-between md:gap-8">

        <h1 class="m-0 shrink-0 whitespace-nowrap text-2xl font-bold leading-tight text-blue-900">
            予約管理システム
        </h1>

        <nav aria-label="メインメニュー" class="min-w-0 overflow-x-auto">
            @php
                $navClass =
                    'block whitespace-nowrap py-2 text-sm font-medium text-blue-900 no-underline transition-colors hover:text-blue-500 focus-visible:text-blue-500';
            @endphp

            <ul class="m-0 flex list-none items-center gap-6 p-0">
                <li class="shrink-0">
                    <a href="{{ route('top') }}">
                        予約管理
                    </a>
                </li>

                <li class="shrink-0">
                    <a href="{{ route('reservation-list') }}">
                        予約一覧・検索
                    </a>
                </li>

                <li class="shrink-0">
                    <a href="{{ route('seats.index') }}">
                        席マスタ
                    </a>
                </li>

                <li class="shrink-0">
                    <a href="{{ route('admin-dashboard') }}">
                        店舗・スタッフ管理
                    </a>
                </li>
            </ul>
        </nav>

    </div>
</header>
