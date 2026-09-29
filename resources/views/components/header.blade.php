<header>
    <div>
        <h1>予約管理システム</h1>

        <nav aria-label="メインメニュー">
            <ul>
                <li>
                    <a
                        href="#"
                        wire:click.prevent="changePage('reservation')"
                    >
                        予約管理
                    </a>
                </li>

                <li>
                    <a
                        href="#"
                        wire:click.prevent="changePage('reservation-list')"
                    >
                        予約一覧・検索
                    </a>
                </li>

                <li>
                    <a
                        href="#"
                        wire:click.prevent="changePage('seat')"
                    >
                        席マスタ
                    </a>
                </li>

                <li>
                    <a
                        href="#"
                        wire:click.prevent="changePage('setting')"
                    >
                        店舗・スタッフ管理
                    </a>
                </li>

                {{-- @guest
                    <li>
                        <a
                            href="#"
                            wire:click.prevent="changePage('login')"
                        >
                            ログイン
                        </a>
                    </li>
                @endguest

                @auth
                    <li>
                        <span>ログアウト（未設定）</span>
                    </li>
                @endauth --}}
            </ul>
        </nav>
    </div>
</header>