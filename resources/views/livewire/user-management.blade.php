<div>
    <h2>ユーザー管理情報</h2>

    <table border="1">
        <tr>
            <th>No</th>
            <th>名前</th>
            <th>メールアドレス</th>
        </tr>

        @foreach ($users as $user)
            <tr>
                <td>{{ $user->id }}</td>
                <td>{{ $user->name }}</td>
                <td>{{ $user->email }}</td>
            </tr>
        @endforeach

    </table>
</div>
