<?php

namespace App\Livewire\Auth;

use Livewire\Component;
use Livewire\Attributes\Layout;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;

#[Layout('components.layouts.guest')]
class LoginPage extends Component
{
    public string $email = '';
    public string $password = '';
    public bool $remember = false;

    public function mount()
    {
        // If we are already authenticated in Laravel, go to dashboard
        if (Auth::check()) {
            $this->redirectRoute('dashboard', navigate: true);
        }
    }

    public function login(): void
    {
        $credentials = $this->validate([
            'email'    => ['required', 'email'],
            'password' => ['required', 'string'],
        ]);

        if (! Auth::attempt($credentials, $this->remember)) {
            throw ValidationException::withMessages([
                'email' => 'These credentials do not match our records.',
            ]);
        }

        session()->regenerate();

        $this->redirectIntended(route('dashboard'), navigate: true);
    }

    /**
     * Updated Logout Method
     */
    public function logout(): void
    {
        // 1. Log out from Laravel Auth
        Auth::logout();

        // 2. Explicitly clear SSO session data if it exists
        session()->forget([
            'sso_username',
            'sso_email',
            'sso_token',
            'accessToken'
        ]);

        // 3. Invalidate the entire session and regenerate CSRF token
        request()->session()->invalidate();
        request()->session()->regenerateToken();

        // 4. Redirect to login
        $this->redirect(route('login'), navigate: true);
    }

    public function render()
    {
        return view('livewire.auth.login-page');
    }
}
