<div class="mx-auto w-full max-w-md overflow-hidden rounded-xl border border-stone-300 bg-white text-stone-900 shadow-sm">

    @php
        $inputClass =
            'block min-h-12 w-full min-w-0 rounded-lg border border-stone-400 bg-white px-3 py-2 text-base text-stone-900 transition-colors placeholder:text-stone-500 focus:border-emerald-700 focus:outline-none focus:ring-2 focus:ring-emerald-700/20';
    @endphp

    <div class="border-b border-emerald-200 bg-emerald-50 px-5 py-4 sm:px-6">
        <h2 class="m-0 flex items-center gap-3 text-lg font-semibold">
            <svg
                class="h-5 w-5 shrink-0 text-emerald-700"
                xmlns="http://www.w3.org/2000/svg"
                viewBox="0 0 24 24"
                fill="none"
                stroke="currentColor"
                stroke-width="1.8"
                stroke-linecap="round"
                stroke-linejoin="round"
                aria-hidden="true"
            >
                <rect x="5" y="10" width="14" height="11" rx="2" />
                <path d="M8 10V7a4 4 0 0 1 8 0v3M12 14v3" />
            </svg>
            ログイン
        </h2>
    </div>

    <div class="p-5 sm:p-6">
        <p class="mb-6 mt-0 text-sm leading-relaxed text-stone-600">
            登録済みのアカウントでログインしてください。
        </p>

        @if (session()->has('error'))
            <div
                role="alert"
                class="mb-5 rounded-lg border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-700"
            >
                {{ session('error') }}
            </div>
        @endif

        <form wire:submit.prevent="login" class="space-y-5">
            <div>
                <label for="login-email" class="mb-2 block text-sm font-medium text-stone-700">
                    メールアドレス
                </label>

                <input
                    id="login-email"
                    type="email"
                    wire:model="email"
                    autocomplete="username"
                    required
                    class="{{ $inputClass }}"
                    aria-invalid="{{ $errors->has('email') ? 'true' : 'false' }}"
                    @error('email')
                        aria-describedby="login-email-error"
                    @enderror
                >

                @error('email')
                    <p id="login-email-error" class="mb-0 mt-2 text-sm text-red-700">
                        {{ $message }}
                    </p>
                @enderror
            </div>

            <div>
                <label for="login-password" class="mb-2 block text-sm font-medium text-stone-700">
                    パスワード
                </label>

                <input
                    id="login-password"
                    type="password"
                    wire:model="password"
                    autocomplete="current-password"
                    required
                    class="{{ $inputClass }}"
                    aria-invalid="{{ $errors->has('password') ? 'true' : 'false' }}"
                    @error('password')
                        aria-describedby="login-password-error"
                    @enderror
                >

                @error('password')
                    <p id="login-password-error" class="mb-0 mt-2 text-sm text-red-700">
                        {{ $message }}
                    </p>
                @enderror
            </div>

            <div class="pt-1">
                <button
                    type="submit"
                    wire:loading.attr="disabled"
                    wire:target="login"
                    class="inline-flex min-h-12 w-full cursor-pointer items-center justify-center rounded-lg bg-emerald-700 px-4 py-3 text-base font-semibold text-white transition-colors hover:bg-emerald-800 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-emerald-700 disabled:cursor-wait disabled:opacity-50"
                >
                    <span wire:loading.remove wire:target="login">ログイン</span>
                    <span wire:loading wire:target="login">ログイン中…</span>
                </button>
            </div>
        </form>
    </div>
</div>