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
                担当A：予約状況画面（TOP）
            </h1>

            <p class="mt-1 text-slate-600">
                席別タイムテーブルを中心に、予約の登録・編集・削除・移動を担当
            </p>


            {{-- 予約状況 --}}
            <section class="mt-6 rounded-lg border border-slate-200 bg-white p-5 shadow-sm">

                <h2 class="text-xl font-bold text-blue-900">
                    予約状況
                </h2>

                {{-- 日付操作 --}}
                <div class="mt-4 flex items-center justify-center gap-4">

                    <button
                        class="rounded border border-slate-300 px-4 py-2 hover:bg-slate-100">
                        ＜
                    </button>

                    <strong>
                        2026年11月16日（土）
                    </strong>

                    <button
                        class="rounded border border-slate-300 px-4 py-2 hover:bg-slate-100">
                        ＞
                    </button>

                </div>

            </section>

        </main>


        {{-- サイドバー --}}
        <aside class="w-72">

            <section class="rounded-lg border border-slate-200 bg-white p-4 shadow-sm">

                <h3 class="font-bold text-blue-900">
                    ◎ 担当範囲
                </h3>

                <ul class="mt-3 list-disc space-y-1 pl-5 text-sm">
                    <li>予約状況TOP画面</li>
                    <li>タイムテーブル表示</li>
                    <li>予約登録・編集・削除</li>
                    <li>予約変更</li>
                </ul>

            </section>


            <section class="mt-4 rounded-lg border border-slate-200 bg-white p-4 shadow-sm">

                <h3 class="font-bold text-blue-900">
                    ☑ MUST（必須）
                </h3>

                <ul class="mt-3 list-disc space-y-1 pl-5 text-sm">
                    <li>席別タイムテーブル表示</li>
                    <li>新規予約</li>
                    <li>予約編集</li>
                    <li>予約削除</li>
                </ul>

            </section>

        </aside>

    </div>

</body>
</html>