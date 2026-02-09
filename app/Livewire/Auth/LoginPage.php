<?php

namespace App\Livewire\Auth;

use Livewire\Component;
use Livewire\Attributes\Layout;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;

#[Layout('layouts.guest')]
class LoginPage extends Component
{
    public string $email = '';
    public string $password = '';
    public bool $remember = false;

    public function login(): void
    {
        $credentials = $this->validate([
            'email'    => ['required', 'email'],
            'password' => ['required', 'string', 'min:6'],
        ]);

        if (! Auth::attempt($credentials, $this->remember)) {
            throw ValidationException::withMessages([
                'email' => 'These credentials do not match our records.',
            ]);
        }

        session()->regenerate();

        $this->redirectIntended(route('dashboard'), navigate: true);
    }

    public function logout(): void
    {
        Auth::logout();

        request()->session()->invalidate();
        request()->session()->regenerateToken();

        $this->redirect(route('login'), navigate: true);
    }

    public function render()
    {
        return view('livewire.auth.login-page');
    }
}
