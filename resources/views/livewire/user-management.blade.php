<div class="mx-4 w-auto min-w-0 overflow-hidden rounded-xl border border-blue-100 bg-white text-blue-900 shadow-sm sm:mx-12">

    @php
        $inputClass = 'h-11 w-full min-w-0 rounded-lg border border-blue-200 bg-white px-3 text-sm transition-colors focus:border-blue-500 focus:outline-none focus:ring-2 focus:ring-blue-100';

        $primaryButtonClass = 'inline-flex min-h-11 cursor-pointer items-center justify-center rounded-lg bg-blue-600 px-5 text-sm font-semibold text-white transition-colors hover:bg-blue-700 focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-blue-500 focus-visible:ring-offset-2 disabled:cursor-wait disabled:opacity-50';

        $secondaryButtonClass = 'inline-flex min-h-11 cursor-pointer items-center justify-center rounded-lg border border-blue-200 bg-white px-5 text-sm font-medium text-blue-900 transition-colors hover:bg-blue-50 focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-blue-500 focus-visible:ring-offset-2';

        $roleStyles = [
            'admin' => [
                'label' => '管理者',
                'class' => 'bg-rose-100 text-rose-700',
            ],
            'manager' => [
                'label' => '責任者',
                'class' => 'bg-purple-100 text-purple-700',
            ],
            'staff' => [
                'label' => 'スタッフ',
                'class' => 'bg-blue-100 text-blue-700',
            ],
        ];
    @endphp

    {{-- 見出し --}}
    <div class="flex flex-wrap items-center justify-between gap-4 border-b border-blue-100 bg-blue-50 px-5 py-4 sm:px-6">

        <div class="flex min-w-0 items-center gap-3">
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

            <h2 class="m-0 text-base font-bold">
                ユーザー管理
            </h2>

            <span class="whitespace-nowrap rounded-full bg-white px-2.5 py-1 text-xs font-medium text-blue-700">
                {{ count($users) }}名
            </span>
        </div>

        @if ($canManageUsers)
            <button
                type="button"
                wire:click="showCreateForm"
                class="{{ $primaryButtonClass }}"
            >
                <span aria-hidden="true" class="mr-2 text-lg">＋</span>
                新規登録
            </button>
        @else
            <span class="text-xs font-medium text-slate-500">
                閲覧のみ
            </span>
        @endif

    </div>

    {{-- 通知 --}}
    @if (session()->has('user-message'))
        <p
            role="status"
            class="mx-5 mt-5 mb-0 rounded-lg border border-green-100 bg-green-50 px-4 py-3 text-sm text-green-700 sm:mx-6"
        >
            {{ session('user-message') }}
        </p>
    @endif

    @error('permission')
        <p
            role="alert"
            class="mx-5 mt-5 mb-0 rounded-lg border border-red-100 bg-red-50 px-4 py-3 text-sm text-red-700 sm:mx-6"
        >
            {{ $message }}
        </p>
    @enderror

    {{-- 登録・編集フォーム --}}
    @if ($canManageUsers && $showForm)
        <section
            aria-labelledby="user-form-title"
            class="mx-5 mt-5 rounded-lg border border-blue-100 bg-slate-50 p-4 sm:mx-6 sm:p-5"
        >

            <h3 id="user-form-title" class="m-0 text-base font-bold">
                {{ $editingId ? 'ユーザー情報の編集' : 'ユーザーの新規登録' }}
            </h3>

            <p class="mt-2 mb-5 text-xs leading-relaxed text-slate-500">
                {{ $editingId ? '変更する情報を入力して更新してください。' : '名前・メールアドレス・パスワード・権限を入力してください。' }}
            </p>

            <form
                wire:submit.prevent="{{ $editingId ? 'updateUser' : 'createUser' }}"
                class="space-y-5"
            >

                <div class="grid grid-cols-1 gap-5 md:grid-cols-2">

                    <div>
                        <label for="user-name" class="mb-2 block text-sm font-medium">
                            名前
                        </label>

                        <input
                            id="user-name"
                            type="text"
                            wire:model="name"
                            autocomplete="name"
                            required
                            class="{{ $inputClass }}"
                            aria-invalid="{{ $errors->has('name') ? 'true' : 'false' }}"
                            @error('name')
                                aria-describedby="user-name-error"
                            @enderror
                        >

                        @error('name')
                            <p id="user-name-error" class="mt-2 mb-0 text-xs text-red-600">
                                {{ $message }}
                            </p>
                        @enderror
                    </div>

                    <div>
                        <label for="user-email" class="mb-2 block text-sm font-medium">
                            メールアドレス
                        </label>

                        <input
                            id="user-email"
                            type="email"
                            wire:model="email"
                            autocomplete="email"
                            required
                            class="{{ $inputClass }}"
                            aria-invalid="{{ $errors->has('email') ? 'true' : 'false' }}"
                            @error('email')
                                aria-describedby="user-email-error"
                            @enderror
                        >

                        @error('email')
                            <p id="user-email-error" class="mt-2 mb-0 text-xs text-red-600">
                                {{ $message }}
                            </p>
                        @enderror
                    </div>

                    @if (! $editingId)
                        <div>
                            <label for="user-password" class="mb-2 block text-sm font-medium">
                                パスワード
                            </label>

                            <input
                                id="user-password"
                                type="password"
                                wire:model="password"
                                autocomplete="new-password"
                                required
                                minlength="8"
                                class="{{ $inputClass }}"
                                aria-invalid="{{ $errors->has('password') ? 'true' : 'false' }}"
                                aria-describedby="user-password-help{{ $errors->has('password') ? ' user-password-error' : '' }}"
                            >

                            <p id="user-password-help" class="mt-2 mb-0 text-xs text-slate-500">
                                8文字以上で入力してください。
                            </p>

                            @error('password')
                                <p id="user-password-error" class="mt-2 mb-0 text-xs text-red-600">
                                    {{ $message }}
                                </p>
                            @enderror
                        </div>
                    @endif

                    <div>
                        <label for="user-role" class="mb-2 block text-sm font-medium">
                            権限
                        </label>

                        <select
                            id="user-role"
                            wire:model="role"
                            class="{{ $inputClass }}"
                            aria-invalid="{{ $errors->has('role') ? 'true' : 'false' }}"
                            @error('role')
                                aria-describedby="user-role-error"
                            @enderror
                        >
                            <option value="admin">管理者</option>
                            <option value="manager">責任者</option>
                            <option value="staff">スタッフ</option>
                        </select>

                        @error('role')
                            <p id="user-role-error" class="mt-2 mb-0 text-xs text-red-600">
                                {{ $message }}
                            </p>
                        @enderror
                    </div>

                </div>

                <div class="flex flex-wrap justify-end gap-3 border-t border-blue-100 pt-5">
                    <button
                        type="button"
                        wire:click="cancelForm"
                        class="{{ $secondaryButtonClass }}"
                    >
                        キャンセル
                    </button>

                    <button
                        type="submit"
                        wire:loading.attr="disabled"
                        wire:target="createUser,updateUser"
                        class="{{ $primaryButtonClass }}"
                    >
                        <span wire:loading.remove wire:target="createUser,updateUser">
                            {{ $editingId ? '更新する' : '登録する' }}
                        </span>

                        <span wire:loading wire:target="createUser,updateUser">
                            保存中…
                        </span>
                    </button>
                </div>

            </form>
        </section>
    @endif

    {{-- 一覧 --}}
    <div class="p-5 sm:p-6">

        @if (! $canManageUsers)
            <p class="mt-0 mb-4 text-xs leading-relaxed text-slate-500">
                メールアドレスは管理者のみ閲覧できます。
            </p>
        @endif

        <div
            role="region"
            aria-label="ユーザー一覧"
            tabindex="0"
            class="overflow-x-auto rounded-lg border border-blue-100 focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-blue-500"
        >
            <table class="w-full min-w-[560px] border-collapse text-sm">

                <caption class="sr-only">
                    登録ユーザーの名前、メールアドレス、権限の一覧
                </caption>

                <thead class="bg-slate-50 text-slate-600">
                    <tr>
                        <th scope="col" class="w-16 border-b border-blue-100 px-4 py-3 text-center font-medium">
                            No
                        </th>

                        <th scope="col" class="border-b border-blue-100 px-4 py-3 text-left font-medium">
                            名前
                        </th>

                        <th scope="col" class="border-b border-blue-100 px-4 py-3 text-left font-medium">
                            メールアドレス
                        </th>

                        <th scope="col" class="w-28 border-b border-blue-100 px-4 py-3 text-center font-medium">
                            権限
                        </th>

                        @if ($canManageUsers)
                            <th scope="col" class="w-44 border-b border-blue-100 px-4 py-3 text-center font-medium">
                                操作
                            </th>
                        @endif
                    </tr>
                </thead>

                <tbody class="divide-y divide-blue-100">
                    @forelse ($users as $user)

                        @php
                            $badge = $roleStyles[$user->role] ?? [
                                'label' => '未設定',
                                'class' => 'bg-slate-100 text-slate-600',
                            ];
                        @endphp

                        <tr
                            wire:key="user-row-{{ $user->id }}"
                            class="transition-colors hover:bg-blue-50/50"
                        >
                            <td class="px-4 py-3 text-center tabular-nums text-slate-500">
                                {{ $user->id }}
                            </td>

                            <th scope="row" class="px-4 py-3 text-left font-medium">
                                {{ $user->name }}
                            </th>

                            <td class="px-4 py-3 text-slate-600">
                                @if ($canManageUsers)
                                    {{ $user->email }}
                                @else
                                    <span aria-label="非公開" class="tracking-wider">
                                        ********
                                    </span>
                                @endif
                            </td>

                            <td class="px-4 py-3 text-center">
                                <span class="inline-block min-w-[5rem] whitespace-nowrap rounded-md px-3 py-1 text-xs font-medium {{ $badge['class'] }}">
                                    {{ $badge['label'] }}
                                </span>
                            </td>

                            @if ($canManageUsers)
                                <td class="px-4 py-3">
                                    <div class="flex justify-center gap-2">

                                        <button
                                            type="button"
                                            wire:click="editUser({{ $user->id }})"
                                            aria-label="{{ $user->name }}さんを編集"
                                            class="inline-flex min-h-10 cursor-pointer items-center justify-center whitespace-nowrap rounded-md border border-blue-200 bg-white px-3 text-xs font-medium text-blue-700 transition-colors hover:bg-blue-50 focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-blue-500"
                                        >
                                            編集
                                        </button>

                                        <button
                                            type="button"
                                            wire:click="deleteUser({{ $user->id }})"
                                            wire:confirm="{{ $user->name }}さんを削除しますか？この操作は取り消せません。"
                                            wire:loading.attr="disabled"
                                            wire:target="deleteUser({{ $user->id }})"
                                            aria-label="{{ $user->name }}さんを削除"
                                            class="inline-flex min-h-10 cursor-pointer items-center justify-center whitespace-nowrap rounded-md border border-red-200 bg-white px-3 text-xs font-medium text-red-600 transition-colors hover:bg-red-50 focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-red-500 disabled:opacity-50"
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
                                class="px-4 py-10 text-center"
                            >
                                <p class="m-0 font-medium text-slate-600">
                                    登録されているユーザーはいません。
                                </p>

                                @if ($canManageUsers)
                                    <p class="mt-2 mb-0 text-xs text-slate-500">
                                        「新規登録」からユーザーを追加できます。
                                    </p>
                                @endif
                            </td>
                        </tr>
                    @endforelse
                </tbody>

            </table>
        </div>

    </div>
</div>