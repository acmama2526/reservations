<?php

namespace App\Livewire;

use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use Livewire\Component;

class UserManagement extends Component
{
    public $name = '';
    public $email = '';
    public $password = '';
    public $role = 'staff';

    public $showForm = false;
    public $editingId = null;

    private function canManageUsers(): bool
    {
        return Auth::user()?->role === 'admin';
    }

    private function ensureAdmin(): bool
    {
        $this->resetValidation('permission');

        if (! $this->canManageUsers()) {
            $this->clearForm();

            $this->addError(
                'permission',
                'ユーザー管理を変更する権限がありません。'
            );

            return false;
        }

        return true;
    }

    private function clearForm()
    {
        $this->reset([
            'name',
            'email',
            'password',
            'role',
            'showForm',
            'editingId',
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
            'role' => [
                'required',
                Rule::in(['admin', 'manager', 'staff']),
            ],
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
            'password.required' => 'パスワードを入力してください。',
            'password.min' => 'パスワードは8文字以上で入力してください。',
            'role.required' => '権限を選択してください。',
            'role.in' => '権限を正しく選択してください。',
        ]);
    }

    // 新規登録フォームを表示
    public function showCreateForm()
    {
        if (! $this->ensureAdmin()) {
            return;
        }

        $this->clearForm();
        $this->showForm = true;
    }

    // ユーザーを登録
    public function createUser()
    {
        if (! $this->ensureAdmin()) {
            return;
        }

        $data = $this->validateUser();
        $data['password'] = Hash::make($data['password']);

        User::create($data);

        $this->clearForm();

        session()->flash('user-message', 'ユーザーを登録しました。');
    }

    // 編集フォームを表示
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

    // ユーザーを更新
    public function updateUser()
    {
        if (! $this->ensureAdmin()) {
            return;
        }

        $user = User::findOrFail($this->editingId);
        $data = $this->validateUser($user);

        $user->update($data);

        $this->clearForm();

        session()->flash('user-message', 'ユーザーを更新しました。');
    }

    // ユーザーを削除
    public function deleteUser($id)
    {
        if (! $this->ensureAdmin()) {
            return;
        }

        User::findOrFail($id)->delete();

        $this->clearForm();

        session()->flash('user-message', 'ユーザーを削除しました。');
    }

    // フォームを閉じる
    public function cancelForm()
    {
        $this->clearForm();
    }

    // ログイン前後とも一覧を表示
    public function render()
    {
        $canManageUsers = $this->canManageUsers();

        // 管理者以外はメールアドレスを取得しない
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

        return view('livewire.user-management', [
            'users' => $users,
            'canManageUsers' => $canManageUsers,
        ]);
    }
}