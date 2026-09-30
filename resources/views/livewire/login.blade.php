<div class="mx-auto w-full max-w-md overflow-hidden rounded-lg border border-blue-100 bg-white text-blue-900">

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
                <rect x="5" y="10" width="14" height="11" rx="2" />
                <path
                    stroke-linecap="round"
                    d="M8 10V7a4 4 0 0 1 8 0v3M12 14v3"
                />
            </svg>

            ログイン
        </h2>
    </div>

    <div class="p-5">
        <div class="mb-5 text-center">
            <h3 class="m-0 text-lg font-bold">
                予約管理システム
            </h3>

            <p class="mt-1 text-xs text-blue-900/70">
                アカウントでログインしてください
            </p>
        </div>

        {{-- ログインエラー --}}
        @if (session()->has('error'))
            <div
                role="alert"
                class="mb-4 rounded-md border border-red-200 bg-red-50 px-3 py-2 text-sm text-red-700"
            >
                {{ session('error') }}
            </div>
        @endif

        @php
            $inputClass = 'block w-full rounded-md border border-blue-200 bg-white px-3 py-2 text-sm text-blue-900 placeholder:text-slate-400 focus:border-blue-500 focus:outline-none focus:ring-2 focus:ring-blue-100';
        @endphp

        <form wire:submit.prevent="login" class="space-y-4">

            {{-- メールアドレス --}}
            <div>
                <label
                    for="login-email"
                    class="mb-1 block text-sm font-medium"
                >
                    メールアドレス
                </label>

                <input
                    id="login-email"
                    type="email"
                    wire:model="email"
                    autocomplete="username"
                    class="{{ $inputClass }}"
                    placeholder="メールアドレス"
                >

                @error('email')
                    <p class="mt-1 text-xs text-red-600">
                        {{ $message }}
                    </p>
                @enderror
            </div>

            {{-- パスワード --}}
            <div>
                <label
                    for="login-password"
                    class="mb-1 block text-sm font-medium"
                >
                    パスワード
                </label>

                <input
                    id="login-password"
                    type="password"
                    wire:model="password"
                    autocomplete="current-password"
                    class="{{ $inputClass }}"
                    placeholder="パスワード"
                >

                @error('password')
                    <p class="mt-1 text-xs text-red-600">
                        {{ $message }}
                    </p>
                @enderror
            </div>

            {{-- ログインボタン --}}
            <button
                type="submit"
                wire:loading.attr="disabled"
                wire:target="login"
                class="w-full cursor-pointer rounded-md bg-blue-600 px-4 py-2 text-sm font-medium text-white transition-colors hover:bg-blue-700 disabled:cursor-wait disabled:opacity-50"
            >
                <span wire:loading.remove wire:target="login">
                    ログイン
                </span>

                <span wire:loading wire:target="login">
                    ログイン中…
                </span>
            </button>

        </form>
    </div>

</div>