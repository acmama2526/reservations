<div>

    <h2>ログイン</h2>

    @if(session()->has('error'))
        <p>
            {{ session('error') }}
        </p>
    @endif

    <form wire:submit.prevent="login">

        <div>
            <label>メールアドレス</label>

            <input
                type="email"
                wire:model="email">
        </div>

        <br>

        <div>
            <label>パスワード</label>

            <input
                type="password"
                wire:model="password">
        </div>

        <br>

        <button type="submit">
            ログイン
        </button>

    </form>

</div>