<!DOCTYPE html>
<html lang="ja">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>予約管理システム</title>

    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body class="bg-slate-50 text-slate-800">

    {{-- ヘッダー --}}
    <header class="flex h-16 items-center bg-blue-900 px-6 text-white">

        <div class="mr-10 text-xl font-bold">
            📅 予約管理システム
        </div>

        <nav class="flex flex-1 gap-8">

            <a href="#" class="rounded bg-blue-700 px-4 py-2">
                予約状況
            </a>

            <a href="#" class="px-4 py-2">
                予約一覧
            </a>

            <a href="#" class="px-4 py-2">
                席マスタ
            </a>

            <a href="#" class="px-4 py-2">
                ユーザー管理
            </a>

            <a href="#" class="px-4 py-2">
                設定
            </a>

        </nav>

        <div>
            👤 スタッフA
        </div>

    </header>


    {{-- ページ本体 --}}
    <div class="flex gap-6 p-6">

        {{-- メインコンテンツ --}}
        <main class="flex-1">

            <h1 class="text-3xl font-bold text-blue-950">
                予約状況画面（TOP）
            </h1>

            <p class="mt-1 text-slate-600">
                席別タイムテーブルを中心に、予約の登録・編集・削除・移動を担当
            </p>


            {{-- 予約状況 --}}
            <section class="mt-6 rounded-lg border border-slate-200 bg-white p-5 shadow-sm">

                <div>
                    <livewire:reservation-status />
                </div>

                <div class="mt-6">
                    <livewire:reservation-form />
                </div>

            </section>

        </main>



    </div>

</body>

</html>
