<?php

namespace App\Livewire;

use Livewire\Attributes\Layout;
use Livewire\Component;
use Illuminate\Contracts\Auth\Authenticatable;

#[Layout('components.layouts.app')]
class Dashboard extends Component
{
    public ?Authenticatable $user = null;

    public array $roles = [];
    public array $permissions = [];
    public array $context = [];

    public int $branchCount = 0;
    public int $departmentCount = 0;

    public function mount(): void
    {
        $this->user = auth()->user();

        $this->roles = session('smis.roles', []);
        $this->context = session('smis.context', []);

        $this->permissions = $this->extractPermissions($this->context);

        $this->branchCount = count($this->context['branches'] ?? []);

        $this->departmentCount = collect($this->context['branches'] ?? [])
            ->flatMap(fn($branch) => $branch['departments'] ?? [])
            ->count();
    }

    // app/Livewire/Dashboard.php
    private function extractPermissions(array $context): array
    {
        $permissions = [];

        foreach ($context['branches'] ?? [] as $branch) {
            foreach ($branch['departments'] ?? [] as $dept) {
                $permissions = array_merge(
                    $permissions,
                    $dept['permissions'] ?? []
                );
            }
        }

        return array_values(array_unique($permissions));
    }

    public function render()
    {
        return view('livewire.dashboard');
    }
}