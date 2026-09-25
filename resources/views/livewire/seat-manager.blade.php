<div>
  <h2>席マスタ</h2>

  {{-- 追加ボタンを押すと、Livewireが SeatManager.php の save()関数を呼ぶ --}}
  <form wire:submit="save">
    <div>
      <label>席名</label>
      <input type="text" wire:model="seat_name">
    </div>

    <div>
      <label>種類</label>
      <select wire:model="type">
        <option value="">選択してください</option>
        <option value="テーブル">テーブル</option>
        <option value="座敷">座敷</option>
        <option value="カウンター">カウンター</option>
      </select>
    </div>

    <div>
      <label>定員</label>
      <input type="number" wire:model="capacity">
    </div>

    <div>
      <label>表示順</label>
      <input type="number" wire:model="display_order">
    </div>

    <div>
      <label>
        <input type="checkbox" wire:model="is_active">
        利用可能
      </label>
    </div>

    <button type="submit">
      @if ($editingSeatId)
        更新
      @else
        追加
      @endif
    </button>
    @if ($editingSeatId)
      <button type="button" wire:click="cancelEdit">
        キャンセル
      </button>
    @endif
  </form>

  <hr>

  <table border="1">
    <thead>
      <tr>
        <th>表示順</th>
        <th>席名</th>
        <th>種類</th>
        <th>定員</th>
        <th>利用状況</th>
        <th>操作</th>
      </tr>
    </thead>

    <tbody>
      @foreach ($seats as $seat)
        <tr>
          <td>{{ $seat->display_order }}</td>
          <td>{{ $seat->seat_name }}</td>
          <td>{{ $seat->type }}</td>
          <td>{{ $seat->capacity }}</td>
          <td>
            @if ($seat->is_active)
              使用中
            @else
              停止中
            @endif
          </td>
          <td>
            <button type="button" wire:click="edit({{ $seat->id }})">
              編集
            </button>
            <button type="button" wire:click="toggleActive({{ $seat->id }})">
              @if ($seat->is_active)
                停止
              @else
                使用再開
              @endif
            </button>
          </td>
        </tr>
      @endforeach
    </tbody>
  </table>
</div>
