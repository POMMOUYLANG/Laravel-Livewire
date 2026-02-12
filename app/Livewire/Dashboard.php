<?php

namespace App\Livewire;

use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('components.layouts.app')]
class Dashboard extends Component
{
    public $username;
    public $email;

    public function mount()
    {
        // Get from Laravel Session
        $this->username = session('sso_username');
        $this->email = session('sso_email');

        // Or get from the Authenticated User object
        // $this->username = auth()->user()->name;
    }
}
