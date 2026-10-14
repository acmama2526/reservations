<x-layout>
    <main class="min-h-screen bg-stone-50 px-4 py-6 text-base leading-relaxed text-stone-900 sm:px-6 lg:px-8">
        <div class="mx-auto w-full max-w-screen-2xl">
            <div class="min-w-0 space-y-6">

                @can('view-shop-settings')
                    <section aria-label="店舗設定" class="min-w-0">
                        <livewire:shop-setting />
                    </section>
                @endcan

                @can('view-user-management')
                    <section aria-label="ユーザー管理" class="min-w-0">
                        <livewire:user-management />
                    </section>
                @endcan

                @if (
                    ! \Illuminate\Support\Facades\Gate::allows('view-shop-settings')
                    && ! \Illuminate\Support\Facades\Gate::allows('view-user-management')
                )
                    <section
                        aria-labelledby="management-permission-title"
                        class="rounded-xl border border-stone-300 bg-white p-6 shadow-sm"
                    >
                        <h2
                            id="management-permission-title"
                            class="m-0 text-lg font-semibold text-stone-900"
                        >
                            店舗・スタッフ管理
                        </h2>

                        <p class="mb-0 mt-3 text-sm leading-relaxed text-stone-600">
                            このページを閲覧する権限がありません。
                            予約情報は予約管理画面から確認してください。
                        </p>

                        <a
                            href="{{ route('top') }}"
                            class="mt-5 inline-flex min-h-11 items-center justify-center rounded-lg bg-emerald-700 px-5 py-3 text-sm font-semibold text-white no-underline transition-colors hover:bg-emerald-800 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-emerald-700"
                        >
                            予約管理へ戻る
                        </a>
                    </section>
                @endif

            </div>
        </div>
    </main>
</x-layout>