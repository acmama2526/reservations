<div class="min-h-screen bg-slate-50 p-6">

  {{-- ページタイトル --}}
  <div class="mb-6">
    <h1 class="text-3xl font-bold text-slate-800">
      席マスタ
    </h1>
    <p class="mt-1 text-slate-500">
      席の登録・編集・利用状況を管理します
    </p>
  </div>

  {{-- 席登録・編集フォーム --}}
  <div class="mb-6 rounded-xl border border-slate-200 bg-white p-6 shadow-sm">

    <div class="mb-4 flex items-center justify-between">
      <h2 class="text-xl font-semibold text-slate-700">
        @if ($editingSeatId)
          席情報の編集
        @else
          席の新規登録
        @endif
      </h2>
    </div>

    <form wire:submit="save">

      <div class="grid grid-cols-1 gap-4 md:grid-cols-2 lg:grid-cols-4">

        {{-- 席名 --}}
        <div>
          <label class="mb-1 block text-sm font-medium text-slate-600">
            席名
          </label>

          <input
            type="text"
            wire:model="seat_name"
            class="w-full rounded-lg border border-slate-300 px-3 py-2
                               focus:border-blue-500 focus:outline-none focus:ring-2 focus:ring-blue-200"
            placeholder="例：T1">
        </div>

        {{-- 種類 --}}
        <div>
          <label class="mb-1 block text-sm font-medium text-slate-600">
            種類
          </label>

          <select
            wire:model="type"
            class="w-full rounded-lg border border-slate-300 px-3 py-2
                               focus:border-blue-500 focus:outline-none focus:ring-2 focus:ring-blue-200">
            <option value="">選択してください</option>
            <option value="テーブル">テーブル</option>
            <option value="座敷">座敷</option>
            <option value="カウンター">カウンター</option>
          </select>
        </div>

        {{-- 定員 --}}
        <div>
          <label class="mb-1 block text-sm font-medium text-slate-600">
            定員
          </label>

          <input
            type="number"
            wire:model="capacity"
            class="w-full rounded-lg border border-slate-300 px-3 py-2
                               focus:border-blue-500 focus:outline-none focus:ring-2 focus:ring-blue-200"
            min="1">
        </div>

        {{-- 表示順 --}}
        <div>
          <label class="mb-1 block text-sm font-medium text-slate-600">
            表示順
          </label>

          <input
            type="number"
            wire:model="display_order"
            class="w-full rounded-lg border border-slate-300 px-3 py-2
                               focus:border-blue-500 focus:outline-none focus:ring-2 focus:ring-blue-200"
            min="1">
        </div>

      </div>

      {{-- 利用可能 --}}
      <div class="mt-4">
        <label class="inline-flex items-center gap-2 text-sm font-medium text-slate-700">
          <input
            type="checkbox"
            wire:model="is_active"
            class="h-4 w-4 rounded border-slate-300">
          利用可能
        </label>
      </div>

      {{-- ボタン --}}
      <div class="mt-5 flex gap-3">

        <button
          type="submit"
          class="rounded-lg bg-blue-600 px-5 py-2 font-medium text-white
                           hover:bg-blue-700">
          @if ($editingSeatId)
            更新
          @else
            新規登録
          @endif
        </button>

        @if ($editingSeatId)
          <button
            type="button"
            wire:click="cancelEdit"
            class="rounded-lg border border-slate-300 bg-white px-5 py-2
                               font-medium text-slate-600 hover:bg-slate-100">
            キャンセル
          </button>
        @endif

      </div>

    </form>
  </div>


  {{-- 席一覧 --}}
  <div class="overflow-hidden rounded-xl border border-slate-200 bg-white shadow-sm">

    <div class="border-b border-slate-200 px-6 py-4">
      <h2 class="text-xl font-semibold text-slate-700">
        席マスター一覧
      </h2>
    </div>

    <div class="overflow-x-auto">

      <table class="w-full border-collapse text-left">

        <thead class="bg-slate-100 text-sm text-slate-600">
          <tr>
            <th class="px-4 py-3">表示順</th>
            <th class="px-4 py-3">席名</th>
            <th class="px-4 py-3">種類</th>
            <th class="px-4 py-3">定員</th>
            <th class="px-4 py-3">利用状況</th>
            <th class="px-4 py-3">操作</th>
          </tr>
        </thead>

        <tbody class="divide-y divide-slate-200">

          @foreach ($seats as $seat)
            <tr class="hover:bg-slate-50">

              <td class="px-4 py-3">
                {{ $seat->display_order }}
              </td>

              <td class="px-4 py-3 font-medium text-slate-800">
                {{ $seat->seat_name }}
              </td>

              <td class="px-4 py-3">
                {{ $seat->type }}
              </td>

              <td class="px-4 py-3">
                {{ $seat->capacity }} 名
              </td>

              <td class="px-4 py-3">

                @if ($seat->is_active)
                  <span
                    class="rounded-full bg-green-100 px-3 py-1
                                                 text-sm font-medium text-green-700">
                    利用可能
                  </span>
                @else
                  <span
                    class="rounded-full bg-slate-200 px-3 py-1
                                                 text-sm font-medium text-slate-600">
                    停止中
                  </span>
                @endif

              </td>

              <td class="px-4 py-3">

                <button
                  type="button"
                  wire:click="edit({{ $seat->id }})"
                  class="rounded-lg bg-blue-600 px-4 py-1.5
                                           text-sm font-medium text-white
                                           hover:bg-blue-700">
                  編集
                </button>

              </td>

            </tr>
          @endforeach

        </tbody>

      </table>

    </div>
  </div>

</div>
