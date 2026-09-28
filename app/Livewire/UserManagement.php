<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class UserManagement extends Component
{
    public $name = '';
    public $email = '';
    public $password = '';
    public $role = 'staff';

    public $showForm = false;

    public $editingId = null;

    public function showCreateForm()
    {
        $this->reset([
            'name',
            'email',
            'password',
        ]);

        $this->role = 'staff';
        $this->editingId = null;
        $this->showForm = true;
    }

    public function createUser()
    {
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
        $this->showForm = false;
    }

    public function editUser($id)
    {
        $user = User::find($id);

        $this->editingId = $user->id;
        $this->name = $user->name;
        $this->email = $user->email;
        $this->role = $user->role;

        $this->showForm = true;
    }

    public function updateUser()
    {
        User::find($this->editingId)->update([
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

    public function deleteUser($id)
    {
        User::find($id)?->delete();
    }

    public function render()
    {
        return view('livewire.user-management', [
            'users' => User::all()
        ]);
    }
}