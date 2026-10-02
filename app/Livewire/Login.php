<?php

namespace App\Livewire;

use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class Login extends Component
{
    public $email = '';
    public $password = '';

    public function login()
    {
        session()->forget('error');

        $credentials = $this->validate([
            'email' => ['required', 'email'],
            'password' => ['required', 'string'],
        ], [
            'email.required' => 'メールアドレスを入力してください。',
            'email.email' => 'メールアドレスを正しく入力してください。',
            'password.required' => 'パスワードを入力してください。',
        ]);

        if (! Auth::attempt($credentials)) {
            $this->reset('password');

            session()->flash(
                'error',
                'メールアドレスまたはパスワードが違います。'
            );

            return;
        }

        session()->regenerate();

        $this->reset('password');

        return redirect()->route('admin-dashboard');
    }

    public function render()
    {
        return view('livewire.login');
    }
}