<x-layout>
    <main class="min-h-screen w-full bg-slate-50 px-4 py-6 text-blue-950 sm:px-6 sm:py-8">

        <div class="mx-auto w-full max-w-screen-2xl space-y-6">

            {{-- ページ見出し --}}
            <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">

                <div>
                    <h2 class="m-0 text-xl font-bold tracking-tight text-blue-900 sm:text-2xl">
                        店舗・スタッフ管理
                    </h2>

                    <p class="mt-2 mb-0 text-sm leading-relaxed text-slate-600">
                        店舗の営業時間・定休日と、登録されているユーザーを確認できます。
                    </p>
                </div>

                @php
                    $role = auth()->user()?->role;

                    $roleLabel = match ($role) {
                        'admin' => '管理者',
                        'manager' => '責任者',
                        'staff' => 'スタッフ',
                        default => '未ログイン',
                    };
                @endphp

                <span
                    @class([
                        'inline-flex w-fit shrink-0 items-center rounded-md px-3 py-1.5 text-xs font-semibold',
                        'bg-rose-100 text-rose-700' => $role === 'admin',
                        'bg-purple-100 text-purple-700' => $role === 'manager',
                        'bg-blue-100 text-blue-700' => $role === 'staff',
                        'bg-slate-200 text-slate-600' => $role === null,
                    ])
                >
                    {{ $roleLabel }}
                </span>

            </div>

            {{-- 操作できる範囲を案内 --}}
            <div class="rounded-lg border border-blue-100 bg-blue-50 px-4 py-3">
                <p class="m-0 text-sm leading-relaxed text-blue-900">
                    @if ($role === 'admin')
                        店舗設定の変更と、ユーザーの登録・編集・削除ができます。
                    @elseif ($role === 'manager')
                        店舗設定を変更できます。ユーザー一覧は閲覧のみです。
                    @else
                        閲覧のみ利用できます。店舗設定の変更には管理者・責任者の権限が必要です。
                    @endif
                </p>
            </div>

            {{-- 店舗設定・ログイン --}}
            <div class="grid w-full grid-cols-1 items-start gap-6 lg:grid-cols-3">

                <section
                    aria-label="店舗設定"
                    class="min-w-0 {{ auth()->check() ? 'lg:col-span-3' : 'lg:col-span-2' }}"
                >
                    <livewire:shop-setting />
                </section>

                @guest
                    <section
                        aria-label="ログイン"
                        class="min-w-0 lg:col-span-1"
                    >
                        <livewire:login />

                        <p class="mt-3 mb-0 px-1 text-xs leading-relaxed text-slate-500">
                            ログインすると、アカウントの権限に応じた操作ができます。
                        </p>
                    </section>
                @endguest

            </div>

            {{-- ユーザー一覧は常に表示 --}}
            <section aria-label="ユーザー管理" class="w-full min-w-0">
                <livewire:user-management />

                @if ($role !== 'admin')
                    <p class="mt-3 mb-0 px-1 text-xs leading-relaxed text-slate-500">
                        メールアドレスの閲覧とユーザー情報の変更は、管理者のみ利用できます。
                    </p>
                @endif
            </section>

        </div>

    </main>
</x-layout>