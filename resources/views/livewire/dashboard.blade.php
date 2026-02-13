<div class="p-6 space-y-8">

    {{-- HEADER --}}
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
        <div>
            <h1 class="text-3xl font-bold tracking-tight">
                Welcome back, {{ (string) $user->name }}
            </h1>
            <p class="text-sm opacity-60">
                {{ (string) $user->email }}
            </p>
        </div>

        <div class="flex flex-wrap gap-2">
            @forelse($roles as $role)
                <span class="badge badge-primary badge-sm">
                    {{-- FIX: Handle potential nested role arrays --}}
                    {{ is_array($role) ? implode(', ', $role) : (string) $role }}
                </span>
            @empty
                <span class="badge badge-error badge-sm">No Role Assigned</span>
            @endforelse
        </div>
    </div>

    {{-- STATS CARDS --}}
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
        <div class="card bg-base-100 shadow-md border border-base-200">
            <div class="card-body">
                <h2 class="text-sm opacity-60 font-bold uppercase tracking-widest">Total Roles</h2>
                <div class="text-3xl font-bold text-primary">{{ count($roles) }}</div>
            </div>
        </div>

        <div class="card bg-base-100 shadow-md border border-base-200">
            <div class="card-body">
                <h2 class="text-sm opacity-60 font-bold uppercase tracking-widest">Branches</h2>
                <div class="text-3xl font-bold text-secondary">{{ count($context['branches'] ?? []) }}</div>
            </div>
        </div>

        <div class="card bg-base-100 shadow-md border border-base-200">
            <div class="card-body">
                <h2 class="text-sm opacity-60 font-bold uppercase tracking-widest">Departments</h2>
                <div class="text-3xl font-bold text-accent">
                    {{ collect($context['branches'] ?? [])->flatMap(fn($b) => $b['departments'] ?? [])->count() }}
                </div>
            </div>
        </div>
    </div>

    {{-- ORGANIZATION CONTEXT --}}
    <div class="card bg-base-100 shadow-lg border border-base-200">
        <div class="card-body">
            <h2 class="card-title text-xl flex items-center gap-2">
                <span class="icon-[tabler--hierarchy-2] text-primary"></span>
                Organization Structure
            </h2>

            @if (!empty($context['branches']))
                <div class="space-y-6 mt-4">
                    @foreach ($context['branches'] as $branch)
                        <div class="p-4 rounded-xl bg-base-200/50 border border-base-300">
                            <h3 class="font-bold text-primary text-lg flex items-center gap-2">
                                <span class="icon-[tabler--building]"></span>
                                {{ is_array($branch['branch'] ?? null) ? 'Invalid Branch Data' : $branch['branch'] ?? 'Default Branch' }}
                            </h3>

                            <div class="mt-4 space-y-3">
                                @foreach ($branch['departments'] ?? [] as $dept)
                                    <div class="p-3 bg-base-100 rounded-lg border border-base-200">
                                        <div class="font-semibold text-sm flex items-center gap-2">
                                            <span class="icon-[tabler--armchair] opacity-40"></span>
                                            {{ is_array($dept['department'] ?? null) ? 'Invalid Dept Data' : $dept['department'] ?? 'General Department' }}
                                        </div>

                                        <div class="flex flex-wrap gap-2 mt-2 ml-6">
                                            @foreach ($dept['roles'] ?? [] as $deptRole)
                                                <span class="badge badge-outline badge-xs opacity-70">
                                                    {{-- FIX: Ensure deptRole is not an array --}}
                                                    {{ is_array($deptRole) ? implode(', ', $deptRole) : (string) $deptRole }}
                                                </span>
                                            @endforeach
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    @endforeach
                </div>
            @else
                <div class="alert alert-warning mt-4 rounded-xl border-none shadow-sm">
                    <span class="icon-[tabler--alert-triangle]"></span>
                    No organization context assigned.
                </div>
            @endif
        </div>
    </div>

    {{-- QUICK ACTIONS --}}
    <div class="flex justify-between items-center bg-base-100 p-6 rounded-2xl border border-base-200 shadow-sm">
        <div>
            <h2 class="text-xl font-bold tracking-tight">Quick Actions</h2>
            <p class="text-sm opacity-60">Manage your students and system reports</p>
        </div>

        <div class="flex gap-2">
            {{-- Use the flat permissions array extracted in the Livewire component --}}
            @if (in_array('enrollment.create', $permissions))
                <button class="btn btn-primary btn-sm rounded-lg shadow-md shadow-primary/20 gap-2">
                    <span class="icon-[tabler--plus] text-lg"></span>
                    New Enrollment
                </button>
            @endif
        </div>
    </div>
</div>
