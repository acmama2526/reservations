<?php

namespace App\Livewire;

use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Livewire\Component;

class UserManagement extends Component
{
    public $name = '';
    public $email = '';
    public $password = '';
    public $role = 'staff';

    public $showForm = false;
    public $editingId = null;

    // ログイン中のユーザーが管理者か確認する
    private function ensureAdmin(): bool
    {
        $this->resetValidation('permission');

        if (auth()->user()?->role !== 'admin') {
            $this->showForm = false;

            $this->addError(
                'permission',
                'ユーザー管理を変更する権限がありません。'
            );

            return false;
        }

        return true;
    }

    // 新規登録フォームを表示
    public function showCreateForm()
    {
        if (! $this->ensureAdmin()) {
            return;
        }

        $this->resetValidation();

        $this->reset([
            'name',
            'email',
            'password',
        ]);

        $this->role = 'staff';
        $this->editingId = null;
        $this->showForm = true;
    }

    // ユーザーを登録
    public function createUser()
    {
        if (! $this->ensureAdmin()) {
            return;
        }

        User::create([
            'name' => $this->name,
            'email' => $this->email,
            'password' => Hash::make($this->password),
            'role' => $this->role,
        ]);

        $this->reset([
            'name',
            'email',
            'password',
        ]);

        $this->role = 'staff';
        $this->editingId = null;
        $this->showForm = false;
    }

    // 編集フォームを表示
    public function editUser($id)
    {
        if (! $this->ensureAdmin()) {
            return;
        }

        $user = User::findOrFail($id);

        $this->resetValidation();

        $this->editingId = $user->id;
        $this->name = $user->name;
        $this->email = $user->email;
        $this->password = '';
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

        $user->update([
            'name' => $this->name,
            'email' => $this->email,
            'role' => $this->role,
        ]);

        $this->showForm = false;
        $this->editingId = null;

        $this->reset([
            'name',
            'email',
            'password',
        ]);

        $this->role = 'staff';
    }

    // ユーザーを削除
    public function deleteUser($id)
    {
        if (! $this->ensureAdmin()) {
            return;
        }

        User::find($id)?->delete();
    }

    // 一覧は閲覧できる
    public function render()
    {
        return view('livewire.user-management', [
            'users' => User::all(),
        ]);
    }
}