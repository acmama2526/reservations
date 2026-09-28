<div>

    <h2>ユーザー管理</h2>

    <button wire:click="showCreateForm">
        新規登録
    </button>

    @if ($showForm)

        <div>
            <h3>
                {{ $editingId ? 'ユーザー編集' : '新規登録' }}
            </h3>

            <input
                type="text"
                wire:model="name"
                placeholder="名前">

            <input
                type="email"
                wire:model="email"
                placeholder="メールアドレス">

            @if (!$editingId)
                <input
                    type="password"
                    wire:model="password"
                    placeholder="パスワード">
            @endif

            <select wire:model="role">
                <option value="admin">管理者</option>
                <option value="manager">責任者</option>
                <option value="staff">アルバイト</option>
            </select>

            @if ($editingId)

                <button wire:click="updateUser">
                    更新
                </button>

            @else

                <button wire:click="createUser">
                    登録
                </button>

            @endif

        </div>

    @endif

    <hr>

    <table border="1">

        <thead>
            <tr>
                <th>No</th>
                <th>名前</th>
                <th>メールアドレス</th>
                <th>権限</th>
                <th>操作</th>
            </tr>
        </thead>

        <tbody>

            @foreach ($users as $user)

                <tr>

                    <td>{{ $user->id }}</td>

                    <td>{{ $user->name }}</td>

                    <td>{{ $user->email }}</td>

                    <td>
                        @if ($user->role === 'admin')
                            管理者
                        @elseif ($user->role === 'manager')
                            責任者
                        @elseif ($user->role === 'staff')
                            アルバイト
                        @endif
                    </td>

                    <td>

                        <button
                            wire:click="editUser({{ $user->id }})">
                            編集
                        </button>

                        <button
                            wire:click="deleteUser({{ $user->id }})"
                            onclick="return confirm('本当に削除しますか？')">
                            削除
                        </button>

                    </td>

                </tr>

            @endforeach

        </tbody>

    </table>

</div>