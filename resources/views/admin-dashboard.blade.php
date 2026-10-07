<x-layout>
    <main class="min-h-screen bg-stone-50 px-4 py-6 text-base leading-relaxed text-stone-900 sm:px-6 lg:px-8">
        <div class="mx-auto w-full max-w-screen-2xl">
            <div
                @class([
                    'grid min-w-0 grid-cols-1 items-start gap-6',
                    'xl:grid-cols-[minmax(0,1fr)_360px]' => ! auth()->check(),
                ])
            >
                <div class="min-w-0 space-y-6">
                    <section aria-label="店舗設定" class="min-w-0">
                        <livewire:shop-setting />
                    </section>

                    <section aria-label="ユーザー管理" class="min-w-0">
                        <livewire:user-management />
                    </section>
                </div>

                @guest
                    <aside
                        aria-label="ログインと権限の案内"
                        class="min-w-0 space-y-5"
                    >
                        <div class="min-w-0">
                            <livewire:login />
                        </div>

                        <div class="overflow-hidden rounded-xl border border-stone-300 bg-white shadow-sm">
                            <div class="border-b border-emerald-200 bg-emerald-50 px-5 py-4">
                                <h2 class="m-0 text-base font-semibold text-stone-900">
                                    権限について
                                </h2>
                            </div>

                            <div class="p-5">
                                <p class="m-0 text-sm leading-relaxed text-stone-600">
                                    ログインすると、権限に応じて設定を変更できます。
                                </p>

                                <dl class="mb-0 mt-4 space-y-4 text-sm leading-relaxed">
                                    <div class="grid grid-cols-[64px_minmax(0,1fr)] items-start gap-3">
                                        <dt class="font-semibold text-stone-800">
                                            管理者
                                        </dt>
                                        <dd class="m-0 text-stone-700">
                                            店舗設定・席マスタ・ユーザー情報の管理
                                        </dd>
                                    </div>

                                    <div class="grid grid-cols-[64px_minmax(0,1fr)] items-start gap-3">
                                        <dt class="font-semibold text-stone-800">
                                            責任者
                                        </dt>
                                        <dd class="m-0 text-stone-700">
                                            店舗設定・席マスタの管理
                                        </dd>
                                    </div>

                                    <div class="grid grid-cols-[64px_minmax(0,1fr)] items-start gap-3">
                                        <dt class="font-semibold text-stone-800">
                                            スタッフ
                                        </dt>
                                        <dd class="m-0 text-stone-700">
                                            このページでは情報の閲覧のみ
                                        </dd>
                                    </div>
                                </dl>
                            </div>
                        </div>
                    </aside>
                @endguest
            </div>
        </div>
    </main>
</x-layout>