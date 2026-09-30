<div class="overflow-hidden rounded-lg border border-blue-100 bg-white text-blue-900">

    {{-- 見出し --}}
    <div class="flex items-center justify-between gap-4 border-b border-blue-100 bg-blue-50 px-5 py-3">
        <h2 class="m-0 flex items-center gap-2 text-base font-bold">
            <svg
                class="h-5 w-5 text-blue-600"
                xmlns="http://www.w3.org/2000/svg"
                viewBox="0 0 24 24"
                fill="none"
                stroke="currentColor"
                stroke-width="2"
                aria-hidden="true"
            >
                <circle cx="9" cy="7" r="4" />
                <path
                    stroke-linecap="round"
                    d="M2 21v-2a7 7 0 0 1 14 0v2M17 3a4 4 0 0 1 0 8M22 21v-2a7 7 0 0 0-4-6"
                />
            </svg>

            ユーザー管理
        </h2>

        <button
            type="button"
            wire:click="showCreateForm"
            class="cursor-pointer rounded-md bg-blue-600 px-4 py-2 text-sm font-medium text-white transition-colors hover:bg-blue-700"
        >
            ＋ 新規登録
        </button>
    </div>

    {{-- 登録・編集フォーム --}}
    @if ($showForm)
        <div class="border-b border-blue-100 bg-blue-50/50 p-5">
            <h3 class="mb-4 text-sm font-bold text-blue-900">
                {{ $editingId ? 'ユーザー編集' : '新規登録' }}
            </h3>

            @php
                $inputClass = 'block w-full rounded-md border border-blue-200 bg-white px-3 py-2 text-sm text-blue-900 placeholder:text-slate-400 focus:border-blue-500 focus:outline-none focus:ring-2 focus:ring-blue-100';
            @endphp

            <div class="grid grid-cols-1 gap-4 md:grid-cols-2">

                <div>
                    <label
                        for="user-name"
                        class="mb-1 block text-sm font-medium"
                    >
                        名前
                    </label>

                    <input
                        id="user-name"
                        type="text"
                        wire:model="name"
                        class="{{ $inputClass }}"
                        placeholder="名前"
                    >

                    @error('name')
                        <p class="mt-1 text-xs text-red-600">
                            {{ $message }}
                        </p>
                    @enderror
                </div>

                <div>
                    <label
                        for="user-email"
                        class="mb-1 block text-sm font-medium"
                    >
                        メールアドレス
                    </label>

                    <input
                        id="user-email"
                        type="email"
                        wire:model="email"
                        class="{{ $inputClass }}"
                        placeholder="メールアドレス"
                    >

                    @error('email')
                        <p class="mt-1 text-xs text-red-600">
                            {{ $message }}
                        </p>
                    @enderror
                </div>

                @if (!$editingId)
                    <div>
                        <label
                            for="user-password"
                            class="mb-1 block text-sm font-medium"
                        >
                            パスワード
                        </label>

                        <input
                            id="user-password"
                            type="password"
                            wire:model="password"
                            autocomplete="new-password"
                            class="{{ $inputClass }}"
                            placeholder="パスワード"
                        >

                        @error('password')
                            <p class="mt-1 text-xs text-red-600">
                                {{ $message }}
                            </p>
                        @enderror
                    </div>
                @endif

                <div>
                    <label
                        for="user-role"
                        class="mb-1 block text-sm font-medium"
                    >
                        権限
                    </label>

                    <select
                        id="user-role"
                        wire:model="role"
                        class="{{ $inputClass }}"
                    >
                        <option value="admin">管理者</option>
                        <option value="manager">責任者</option>
                        <option value="staff">スタッフ</option>
                    </select>

                    @error('role')
                        <p class="mt-1 text-xs text-red-600">
                            {{ $message }}
                        </p>
                    @enderror
                </div>

            </div>

            <div class="mt-4 flex justify-end">
                @if ($editingId)
                    <button
                        type="button"
                        wire:click="updateUser"
                        wire:loading.attr="disabled"
                        wire:target="updateUser"
                        class="cursor-pointer rounded-md bg-blue-600 px-6 py-2 text-sm font-medium text-white transition-colors hover:bg-blue-700 disabled:cursor-wait disabled:opacity-50"
                    >
                        更新
                    </button>
                @else
                    <button
                        type="button"
                        wire:click="createUser"
                        wire:loading.attr="disabled"
                        wire:target="createUser"
                        class="cursor-pointer rounded-md bg-blue-600 px-6 py-2 text-sm font-medium text-white transition-colors hover:bg-blue-700 disabled:cursor-wait disabled:opacity-50"
                    >
                        登録
                    </button>
                @endif
            </div>
        </div>
    @endif

    {{-- ユーザー一覧 --}}
    <div class="overflow-x-auto">
        <table class="w-full min-w-[640px] border-collapse text-left text-sm">
            <thead class="bg-blue-50">
                <tr>
                    <th scope="col" class="border border-blue-100 px-4 py-3 text-center font-semibold">
                        No
                    </th>

                    <th scope="col" class="border border-blue-100 px-4 py-3 font-semibold">
                        名前
                    </th>

                    <th scope="col" class="border border-blue-100 px-4 py-3 font-semibold">
                        メールアドレス
                    </th>

                    <th scope="col" class="border border-blue-100 px-4 py-3 text-center font-semibold">
                        権限
                    </th>

                    <th scope="col" class="border border-blue-100 px-4 py-3 text-center font-semibold">
                        操作
                    </th>
                </tr>
            </thead>

            <tbody>
                @forelse ($users as $user)
                    <tr
                        wire:key="user-{{ $user->id }}"
                        class="transition-colors hover:bg-blue-50/50"
                    >
                        <td class="border border-blue-100 px-4 py-3 text-center">
                            {{ $user->id }}
                        </td>

                        <td class="whitespace-nowrap border border-blue-100 px-4 py-3">
                            {{ $user->name }}
                        </td>

                        <td class="border border-blue-100 px-4 py-3">
                            {{ $user->email }}
                        </td>

                        <td class="border border-blue-100 px-4 py-3 text-center">
                            @if ($user->role === 'admin')
                                <span class="inline-block whitespace-nowrap rounded bg-red-100 px-3 py-1 text-xs font-semibold text-red-700">
                                    管理者
                                </span>
                            @elseif ($user->role === 'manager')
                                <span class="inline-block whitespace-nowrap rounded bg-purple-100 px-3 py-1 text-xs font-semibold text-purple-700">
                                    責任者
                                </span>
                            @elseif ($user->role === 'staff')
                                <span class="inline-block whitespace-nowrap rounded bg-blue-100 px-3 py-1 text-xs font-semibold text-blue-700">
                                    アルバイト
                                </span>
                            @endif
                        </td>

                        <td class="border border-blue-100 px-4 py-3">
                            <div class="flex items-center justify-center gap-2">
                                <button
                                    type="button"
                                    wire:click="editUser({{ $user->id }})"
                                    class="cursor-pointer whitespace-nowrap rounded border border-blue-200 bg-white px-3 py-1 text-xs font-medium text-blue-600 transition-colors hover:bg-blue-50"
                                >
                                    編集
                                </button>

                                <button
                                    type="button"
                                    wire:click="deleteUser({{ $user->id }})"
                                    onclick="
                                        if (!confirm('本当に削除しますか？')) {
                                            event.preventDefault();
                                            event.stopImmediatePropagation();
                                        }
                                    "
                                    class="cursor-pointer whitespace-nowrap rounded border border-red-200 bg-white px-3 py-1 text-xs font-medium text-red-600 transition-colors hover:bg-red-50"
                                >
                                    削除
                                </button>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" class="px-4 py-8 text-center text-sm text-slate-500">
                            登録されているユーザーはいません。
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

</div>