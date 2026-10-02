<div class="w-full min-w-0 rounded-lg border border-blue-100 bg-white text-blue-900">

    @php
        $inputClass = 'w-full rounded-md border border-blue-200 bg-white px-3 py-2 text-sm focus:border-blue-500 focus:outline-none';
    @endphp

    <div class="flex items-center justify-between gap-4 border-b border-blue-100 bg-blue-50 px-5 py-4">

        <div class="flex items-center gap-2">
            <svg
                xmlns="http://www.w3.org/2000/svg"
                class="h-5 w-5 shrink-0 text-blue-600"
                fill="none"
                viewBox="0 0 24 24"
                stroke="currentColor"
                stroke-width="1.8"
                stroke-linecap="round"
                stroke-linejoin="round"
                aria-hidden="true"
            >
                <circle cx="9" cy="7" r="4" />
                <path d="M2 21v-3a7 7 0 0 1 14 0v3" />
                <path d="M16 3a4 4 0 0 1 0 8" />
                <path d="M22 21v-3a7 7 0 0 0-4-6" />
            </svg>

            <h2 class="m-0 text-sm font-bold">
                ユーザー管理
            </h2>
        </div>

        @if ($canManageUsers)
            <button
                type="button"
                wire:click="showCreateForm"
                class="shrink-0 cursor-pointer rounded-md bg-blue-600 px-4 py-2 text-sm font-bold text-white hover:bg-blue-700"
            >
                ＋ 新規登録
            </button>
        @endif

    </div>

    @if (session()->has('user-message'))
        <p role="status" class="mx-5 mt-4 rounded bg-green-50 px-3 py-2 text-sm text-green-700">
            {{ session('user-message') }}
        </p>
    @endif

    @if ($errors->any())
        <ul role="alert" class="mx-5 mt-4 list-none space-y-1 rounded bg-red-50 p-3 text-sm text-red-700">
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    @endif

    @if ($canManageUsers && $showForm)
        <div class="m-5 rounded-lg border border-blue-100 bg-blue-50 p-5">

            <h3 class="mb-4 text-base font-bold">
                {{ $editingId ? 'ユーザー編集' : '新規登録' }}
            </h3>

            <form
                wire:submit.prevent="{{ $editingId ? 'updateUser' : 'createUser' }}"
                class="space-y-4"
            >

                <div>
                    <label for="user-name" class="mb-1 block text-sm font-medium">
                        名前
                    </label>

                    <input
                        id="user-name"
                        type="text"
                        wire:model="name"
                        autocomplete="name"
                        class="{{ $inputClass }}"
                    >
                </div>

                <div>
                    <label for="user-email" class="mb-1 block text-sm font-medium">
                        メールアドレス
                    </label>

                    <input
                        id="user-email"
                        type="email"
                        wire:model="email"
                        autocomplete="email"
                        class="{{ $inputClass }}"
                    >
                </div>

                @if (!$editingId)
                    <div>
                        <label for="user-password" class="mb-1 block text-sm font-medium">
                            パスワード
                        </label>

                        <input
                            id="user-password"
                            type="password"
                            wire:model="password"
                            autocomplete="new-password"
                            class="{{ $inputClass }}"
                        >
                    </div>
                @endif

                <div>
                    <label for="user-role" class="mb-1 block text-sm font-medium">
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
                </div>

                <div class="flex gap-3">
                    <button
                        type="submit"
                        wire:loading.attr="disabled"
                        class="cursor-pointer rounded-md bg-blue-600 px-5 py-2 text-sm font-bold text-white hover:bg-blue-700 disabled:opacity-50"
                    >
                        {{ $editingId ? '更新' : '登録' }}
                    </button>

                    <button
                        type="button"
                        wire:click="cancelForm"
                        class="cursor-pointer rounded-md border border-blue-200 bg-white px-5 py-2 text-sm hover:bg-blue-100"
                    >
                        キャンセル
                    </button>
                </div>

            </form>

        </div>
    @endif

    <div class="w-full overflow-x-auto">
        <table class="w-full border-collapse text-sm">

            <thead class="bg-blue-50">
                <tr>
                    <th class="border-b border-r border-blue-100 px-4 py-3 text-center font-medium">
                        No
                    </th>

                    <th class="border-b border-r border-blue-100 px-4 py-3 text-left font-medium">
                        名前
                    </th>

                    <th class="border-b border-r border-blue-100 px-4 py-3 text-left font-medium">
                        メールアドレス
                    </th>

                    <th class="border-b border-blue-100 px-4 py-3 text-center font-medium">
                        権限
                    </th>

                    @if ($canManageUsers)
                        <th class="border-b border-l border-blue-100 px-4 py-3 text-center font-medium">
                            操作
                        </th>
                    @endif
                </tr>
            </thead>

            <tbody>
                @forelse ($users as $user)
                    <tr wire:key="user-row-{{ $user->id }}" class="hover:bg-blue-50">

                        <td class="border-b border-r border-blue-100 px-4 py-3 text-center">
                            {{ $user->id }}
                        </td>

                        <td class="border-b border-r border-blue-100 px-4 py-3">
                            {{ $user->name }}
                        </td>

                        <td class="border-b border-r border-blue-100 px-4 py-3">
                            {{ $user->email }}
                        </td>

                        <td class="border-b border-blue-100 px-4 py-3 text-center">

                            @if ($user->role === 'admin')
                                <span class="inline-block min-w-[5rem] whitespace-nowrap rounded bg-rose-100 px-3 py-1 text-xs font-medium text-rose-600">
                                    管理者
                                </span>

                            @elseif ($user->role === 'manager')
                                <span class="inline-block min-w-[5rem] whitespace-nowrap rounded bg-purple-100 px-3 py-1 text-xs font-medium text-purple-700">
                                    責任者
                                </span>

                            @elseif ($user->role === 'staff')
                                <span class="inline-block min-w-[5rem] whitespace-nowrap rounded bg-blue-100 px-3 py-1 text-xs font-medium text-blue-600">
                                    スタッフ
                                </span>

                            @else
                                <span class="inline-block min-w-[5rem] whitespace-nowrap rounded bg-gray-100 px-3 py-1 text-xs font-medium text-gray-600">
                                    未設定
                                </span>
                            @endif

                        </td>

                        @if ($canManageUsers)
                            <td class="border-b border-l border-blue-100 px-4 py-3">
                                <div class="flex justify-center gap-2">

                                    <button
                                        type="button"
                                        wire:click="editUser({{ $user->id }})"
                                        class="cursor-pointer whitespace-nowrap rounded border border-blue-200 px-3 py-1 text-xs text-blue-600 hover:bg-blue-100"
                                    >
                                        編集
                                    </button>

                                    <button
                                        type="button"
                                        wire:click="deleteUser({{ $user->id }})"
                                        wire:confirm="本当に削除しますか？"
                                        class="cursor-pointer whitespace-nowrap rounded border border-red-200 px-3 py-1 text-xs text-red-600 hover:bg-red-50"
                                    >
                                        削除
                                    </button>

                                </div>
                            </td>
                        @endif

                    </tr>
                @empty
                    <tr>
                        <td
                            colspan="{{ $canManageUsers ? 5 : 4 }}"
                            class="px-4 py-8 text-center text-gray-500"
                        >
                            登録されているユーザーはいません。
                        </td>
                    </tr>
                @endforelse
            </tbody>

        </table>
    </div>

</div>