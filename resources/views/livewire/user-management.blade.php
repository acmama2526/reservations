<div class="w-full min-w-0 overflow-hidden rounded-xl border border-stone-300 bg-white text-stone-900 shadow-sm">
    @php
        $inputClass =
            'h-11 w-full min-w-0 rounded-lg border border-stone-400 bg-white px-3 text-base text-stone-900 transition-colors placeholder:text-stone-500 focus:border-emerald-700 focus:outline-none focus:ring-2 focus:ring-emerald-700/20';
        $primaryButtonClass =
            'inline-flex min-h-11 cursor-pointer items-center justify-center rounded-lg bg-emerald-700 px-5 text-sm font-semibold text-white transition-colors hover:bg-emerald-800 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-emerald-700 disabled:cursor-wait disabled:opacity-50';
        $secondaryButtonClass =
            'inline-flex min-h-11 cursor-pointer items-center justify-center rounded-lg border border-stone-400 bg-white px-5 text-sm font-medium text-stone-800 transition-colors hover:bg-stone-100 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-emerald-700 disabled:cursor-wait disabled:opacity-50';
        $roleStyles = [
            'admin' => [
                'label' => '管理者',
                'class' => 'border-rose-300 bg-rose-100 text-rose-900',
            ],
            'manager' => [
                'label' => '責任者',
                'class' => 'border-amber-300 bg-amber-100 text-amber-950',
            ],
            'staff' => [
                'label' => 'スタッフ',
                'class' => 'border-stone-300 bg-stone-100 text-stone-700',
            ],
        ];
    @endphp
    {{-- 見出し --}}
    <div class="flex flex-wrap items-center justify-between gap-4 border-b border-emerald-200 bg-emerald-50 px-5 py-4 sm:px-6">
        <div class="flex min-w-0 items-center gap-3">
            <svg
                xmlns="http://www.w3.org/2000/svg"
                class="h-5 w-5 shrink-0 text-emerald-700"
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
            <h2 class="m-0 text-lg font-semibold">
                ユーザー管理
            </h2>
            <span class="whitespace-nowrap rounded-md border border-stone-300 bg-white px-2.5 py-1 text-xs font-medium text-stone-700">
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
            <span class="text-xs font-medium text-stone-600">
                閲覧のみ
            </span>
        @endif
    </div>
    {{-- 通知 --}}
    @if (session()->has('user-message'))
        <p
            role="status"
            class="mx-5 mb-0 mt-5 rounded-lg border border-emerald-200 bg-emerald-100 px-4 py-3 text-sm text-emerald-900 sm:mx-6"
        >
            {{ session('user-message') }}
        </p>
    @endif
    @error('permission')
        <p
            role="alert"
            class="mx-5 mb-0 mt-5 rounded-lg border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-700 sm:mx-6"
        >
            {{ $message }}
        </p>
    @enderror
    {{-- 登録・編集 --}}
    @if ($canManageUsers && $showForm)
        <section
            aria-labelledby="user-form-title"
            class="mx-5 mt-5 rounded-lg border border-stone-300 bg-stone-50 p-4 sm:mx-6 sm:p-5"
        >
            <h3 id="user-form-title" class="m-0 text-base font-semibold">
                {{ $editingId ? 'ユーザー情報の編集' : 'ユーザーの新規登録' }}
            </h3>
            <p class="mb-5 mt-2 text-sm leading-relaxed text-stone-600">
                {{ $editingId ? '変更する情報を入力して更新してください。' : '名前・メールアドレス・パスワード・権限を入力してください。' }}
            </p>
            <form
                wire:submit.prevent="{{ $editingId ? 'updateUser' : 'createUser' }}"
                class="space-y-5"
            >
                <div class="grid grid-cols-1 gap-5 md:grid-cols-2">
                    <div>
                        <label for="user-name" class="mb-2 block text-sm font-medium text-stone-700">
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
                            <p id="user-name-error" class="mb-0 mt-2 text-sm text-red-700">
                                {{ $message }}
                            </p>
                        @enderror
                    </div>
                    <div>
                        <label for="user-email" class="mb-2 block text-sm font-medium text-stone-700">
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
                            <p id="user-email-error" class="mb-0 mt-2 text-sm text-red-700">
                                {{ $message }}
                            </p>
                        @enderror
                    </div>
                    @if (! $editingId)
                        <div>
                            <label for="user-password" class="mb-2 block text-sm font-medium text-stone-700">
                                新規ユーザーのパスワード
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
                            <p id="user-password-help" class="mb-0 mt-2 text-sm text-stone-600">
                                8文字以上で入力してください。
                            </p>
                            @error('password')
                                <p id="user-password-error" class="mb-0 mt-2 text-sm text-red-700">
                                    {{ $message }}
                                </p>
                            @enderror
                        </div>
                    @endif
                    <div>
                        <label for="user-role" class="mb-2 block text-sm font-medium text-stone-700">
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
                            <p id="user-role-error" class="mb-0 mt-2 text-sm text-red-700">
                                {{ $message }}
                            </p>
                        @enderror
                    </div>
                </div>
                @if (! $editingId)
                    <div class="rounded-lg border border-stone-300 bg-white p-4">
                        <label for="current-password" class="mb-2 block text-sm font-medium text-stone-700">
                            ログイン中の管理者のパスワード
                        </label>
                        <input
                            id="current-password"
                            type="password"
                            wire:model="currentPassword"
                            autocomplete="current-password"
                            required
                            class="{{ $inputClass }}"
                            aria-invalid="{{ $errors->has('currentPassword') ? 'true' : 'false' }}"
                            aria-describedby="current-password-help{{ $errors->has('currentPassword') ? ' current-password-error' : '' }}"
                        >
                        <p id="current-password-help" class="mb-0 mt-2 text-sm text-stone-600">
                            登録を承認するため、現在ログインしている管理者ご自身のパスワードを入力してください。
                        </p>
                        @error('currentPassword')
                            <p id="current-password-error" role="alert" class="mb-0 mt-2 text-sm text-red-700">
                                {{ $message }}
                            </p>
                        @enderror
                    </div>
                @endif
                <div class="flex flex-wrap justify-end gap-3 border-t border-stone-300 pt-5">
                    <button
                        type="button"
                        wire:click="cancelForm"
                        wire:loading.attr="disabled"
                        wire:target="createUser,updateUser"
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
            <p id="user-email-note" class="mb-4 mt-0 text-sm leading-relaxed text-stone-600">
                メールアドレスは管理者のみ閲覧できます。
            </p>
        @endif
        <div
            role="region"
            aria-label="ユーザー一覧"
            @if (! $canManageUsers)
                aria-describedby="user-email-note"
            @endif
            tabindex="0"
            class="overflow-x-auto rounded-lg border border-stone-300 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-emerald-700"
        >
            <table class="w-full min-w-[560px] border-collapse text-sm">
                <caption class="sr-only">
                    登録ユーザーの名前、メールアドレス、権限の一覧。メールアドレスは管理者のみ閲覧できます。
                </caption>
                <thead class="bg-stone-100 text-stone-700">
                    <tr>
                        <th scope="col" class="w-16 border-b border-stone-300 px-4 py-3 text-center font-semibold">
                            No
                        </th>
                        <th scope="col" class="border-b border-stone-300 px-4 py-3 text-left font-semibold">
                            名前
                        </th>
                        <th scope="col" class="border-b border-stone-300 px-4 py-3 text-left font-semibold">
                            メールアドレス
                        </th>
                        <th scope="col" class="w-28 border-b border-stone-300 px-4 py-3 text-center font-semibold">
                            権限
                        </th>
                        @if ($canManageUsers)
                            <th scope="col" class="w-44 border-b border-stone-300 px-4 py-3 text-center font-semibold">
                                操作
                            </th>
                        @endif
                    </tr>
                </thead>
                <tbody class="divide-y divide-stone-200">
                    @forelse ($users as $user)
                        @php
                            $badge = $roleStyles[$user->role] ?? [
                                'label' => '未設定',
                                'class' => 'border-stone-300 bg-stone-100 text-stone-700',
                            ];
                        @endphp
                        <tr
                            wire:key="user-row-{{ $user->id }}"
                            class="transition-colors hover:bg-emerald-50"
                        >
                            <td class="px-4 py-3 text-center tabular-nums text-stone-600">
                                {{ $user->id }}
                            </td>
                            <th scope="row" class="break-words px-4 py-3 text-left font-medium">
                                {{ $user->name }}
                            </th>
                            <td class="px-4 py-3 text-stone-700">
                                @if ($canManageUsers)
                                    <span class="break-all">{{ $user->email }}</span>
                                @else
                                    <span class="inline-flex items-center gap-1.5 text-stone-600">
                                        <svg
                                            xmlns="http://www.w3.org/2000/svg"
                                            class="h-4 w-4 shrink-0"
                                            fill="none"
                                            viewBox="0 0 24 24"
                                            stroke="currentColor"
                                            stroke-width="1.8"
                                            stroke-linecap="round"
                                            stroke-linejoin="round"
                                            aria-hidden="true"
                                        >
                                            <rect x="5" y="10" width="14" height="11" rx="2" />
                                            <path d="M8 10V7a4 4 0 0 1 8 0v3" />
                                        </svg>
                                        非公開
                                    </span>
                                @endif
                            </td>
                            <td class="px-4 py-3 text-center">
                                <span class="inline-block min-w-20 whitespace-nowrap rounded-md border px-3 py-1 text-xs font-semibold {{ $badge['class'] }}">
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
                                            class="inline-flex min-h-11 cursor-pointer items-center justify-center whitespace-nowrap rounded-lg border border-stone-400 bg-white px-3 text-sm font-medium text-emerald-800 transition-colors hover:border-emerald-700 hover:bg-emerald-100 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-emerald-700"
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
                                            class="inline-flex min-h-11 cursor-pointer items-center justify-center whitespace-nowrap rounded-lg border border-stone-300 bg-white px-3 text-sm font-medium text-red-700 transition-colors hover:border-red-300 hover:bg-red-50 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-red-700 disabled:cursor-wait disabled:opacity-50"
                                        >
                                            削除
                                        </button>
                                    </div>
                                </td>
                            @endif
                        </tr>
                    @empty
                        <tr>
                            <td colspan="{{ $canManageUsers ? 5 : 4 }}" class="px-4 py-10 text-center">
                                <p class="m-0 font-medium text-stone-700">
                                    登録されているユーザーはいません。
                                </p>
                                @if ($canManageUsers)
                                    <p class="mb-0 mt-2 text-sm text-stone-600">
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
