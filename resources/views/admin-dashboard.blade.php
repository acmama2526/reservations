<x-layout>
    <main class="w-full space-y-6">

        <div class="grid w-full grid-cols-1 items-start gap-5 lg:grid-cols-3">

            {{-- ログイン後は店舗設定を横幅いっぱいに表示 --}}
            <div class="min-w-0 {{ auth()->check() ? 'lg:col-span-3' : 'lg:col-span-2' }}">
                <livewire:shop-setting />
            </div>

            {{-- 未ログイン時は右側にログイン画面を表示 --}}
            @guest
                <div class="min-w-0 lg:col-span-1">
                    <livewire:login />
                </div>
            @endguest

        </div>

        {{-- ユーザー一覧はログイン前後とも表示 --}}
        <div class="w-full min-w-0">
            <livewire:user-management />
        </div>

    </main>
</x-layout>