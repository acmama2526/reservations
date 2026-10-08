<?php

namespace App\Livewire;

use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Validation\Rule;
use Livewire\Attributes\Locked;
use Livewire\Component;

class UserManagement extends Component
{
    public $name = '';
    public $email = '';
    public $password = '';
    public $currentPassword = '';
    public $role = 'staff';
    public $showForm = false;
    #[Locked]
    public $editingId = null;

    #[Locked]
    public $deletingId = null;

    private function canManageUsers(): bool
    {
        return Auth::user()?->role === 'admin';
    }

    private function ensureAdmin(): bool
    {
        $this->resetValidation('permission');

        if (! $this->canManageUsers()) {
            $this->clearForm();
            $this->addError('permission', 'ユーザー管理を変更する権限がありません。');
            return false;
        }

        return true;
    }

    private function clearForm()
    {
        $this->reset([
            'name', 'email', 'password', 'currentPassword',
            'role', 'showForm', 'editingId', 'deletingId',
        ]);
        $this->resetValidation();
    }

    private function validateUser(?User $user = null): array
    {
        $emailRule = Rule::unique('users', 'email');
        if ($user !== null) {
            $emailRule->ignore($user);
        }

        $rules = [
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', $emailRule],
            'role' => ['required', Rule::in(['admin', 'manager', 'staff'])],
        ];
        if ($user === null) {
            $rules['password'] = ['required', 'string', 'min:8'];
        }

        return $this->validate($rules, [
            'name.required' => '名前を入力してください。',
            'name.max' => '名前は255文字以内で入力してください。',
            'email.required' => 'メールアドレスを入力してください。',
            'email.email' => 'メールアドレスを正しく入力してください。',
            'email.max' => 'メールアドレスは255文字以内で入力してください。',
            'email.unique' => 'このメールアドレスは既に登録されています。',
            'password.required' => '新規ユーザーのパスワードを入力してください。',
            'password.min' => 'パスワードは8文字以上で入力してください。',
            'role.required' => '権限を選択してください。',
            'role.in' => '権限を正しく選択してください。',
        ]);
    }

    private function confirmCurrentPassword(): bool
    {
        $this->resetValidation('currentPassword');
        $key = 'user-management-password:'.Auth::id();

        if (RateLimiter::tooManyAttempts($key, 5)) {
            $seconds = RateLimiter::availableIn($key);
            $this->addError('currentPassword', "入力を繰り返し間違えたため、{$seconds}秒後に再度お試しください。");
            return false;
        }

        $this->validate([
            'currentPassword' => ['required', 'string'],
        ], [
            'currentPassword.required' => 'ログイン中の管理者のパスワードを入力してください。',
            'currentPassword.string' => 'パスワードを正しく入力してください。',
        ]);

        // 操作対象ではなく、現在ログインしている管理者と照合する。
        $admin = User::query()->find(Auth::id());
        if (! $admin || $admin->role !== 'admin'
            || ! Hash::check($this->currentPassword, $admin->password)) {
            RateLimiter::hit($key, 60);
            $this->addError('currentPassword', 'ログイン中の管理者のパスワードが違います。');
            return false;
        }

        RateLimiter::clear($key);
        return true;
    }

    public function showCreateForm()
    {
        if (! $this->ensureAdmin()) {
            return;
        }
        $this->clearForm();
        $this->showForm = true;
    }

    public function createUser()
    {
        try {
            if (! $this->ensureAdmin()) {
                return;
            }

            // 毎回、サーバー側でパスワードを確認してから登録する。
            if (! $this->confirmCurrentPassword()) {
                return;
            }

            $data = $this->validateUser();
            $data['password'] = Hash::make($data['password']);
            User::create($data);

            $this->clearForm();
            session()->flash('user-message', 'ユーザーを登録しました。');
        } finally {
            // エラーになった場合も管理者のパスワードを保持しない。
            $this->reset('currentPassword');
        }
    }

    public function editUser($id)
    {
        if (! $this->ensureAdmin()) {
            return;
        }
        $user = User::findOrFail($id);
        $this->clearForm();
        $this->editingId = $user->id;
        $this->name = $user->name;
        $this->email = $user->email;
        $this->role = $user->role;
        $this->showForm = true;
    }

    public function updateUser()
    {
        try {
            if (! $this->ensureAdmin()) {
                return;
            }

            if (! $this->confirmCurrentPassword()) {
                return;
            }

            $user = User::findOrFail($this->editingId);
            $data = $this->validateUser($user);
            $user->update($data);

            $this->clearForm();
            session()->flash('user-message', 'ユーザーを更新しました。');
        } finally {
            $this->reset('currentPassword');
        }
    }

    // 削除ボタンでは、まだ削除せず確認欄を表示する。
    public function showDeleteForm($id)
    {
        if (! $this->ensureAdmin()) {
            return;
        }

        $user = User::findOrFail($id);
        $this->clearForm();
        $this->deletingId = $user->id;
    }

    // 確認欄で選んだユーザーを、パスワード照合後に削除する。
    public function deleteUser()
    {
        try {
            if (! $this->ensureAdmin()) {
                return;
            }

            if (! $this->confirmCurrentPassword()) {
                return;
            }

            User::findOrFail($this->deletingId)->delete();

            $this->clearForm();
            session()->flash('user-message', 'ユーザーを削除しました。');
        } finally {
            $this->reset('currentPassword');
        }
    }

    public function cancelForm()
    {
        $this->clearForm();
    }

    public function render()
    {
        $canManageUsers = $this->canManageUsers();
        $columns = $canManageUsers
            ? ['id', 'name', 'email', 'role']
            : ['id', 'name', 'role'];

        $users = User::query()
            ->select($columns)
            ->orderBy('id')
            ->get()
            ->map(function ($user) use ($canManageUsers) {
                return (object) [
                    'id' => $user->id,
                    'name' => $user->name,
                    'email' => $canManageUsers ? $user->email : '********',
                    'role' => $user->role,
                ];
            });

        $deleteTarget = $canManageUsers && $this->deletingId !== null
            ? User::query()->select(['id', 'name'])->find($this->deletingId)
            : null;

        return view('livewire.user-management', [
            'users' => $users,
            'canManageUsers' => $canManageUsers,
            'deleteTarget' => $deleteTarget,
        ]);
    }
}
