<div class="p-6 space-y-6">
    {{-- Header --}}
    <div class="flex flex-col gap-2 md:flex-row md:items-center md:justify-between">
        <div>
            <h2 class="font-sans text-2xl font-semibold ">Teacher Management</h2>
            <p class="text-sm opacity-70">Manage faculty records and departmental assignments</p>
        </div>

        <button class="btn btn-primary btn-outline gap-2" wire:click="openCreate">
            <span class="icon-[tabler--user-plus] text-lg"></span>
            Add Teacher
        </button>
    </div>

    {{-- Stats Row (New Addition for Design Depth) --}}
    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
        <div class="card bg-base-100 border border-base-200 shadow-sm">
            <div class="card-body p-4 flex-row items-center gap-4">
                <div class="btn btn-square btn-soft btn-info no-animation">
                    <span class="icon-[tabler--users-group] text-xl"></span>
                </div>
                <div>
                    <p class="text-xs opacity-60 uppercase font-bold tracking-tighter">Total Teachers</p>
                    <p class="text-xl font-bold">48</p>
                </div>
            </div>
        </div>
        {{-- Repeat for other stats if needed --}}
    </div>

    {{-- Flash message --}}
    @if (session('message'))
        <div class="alert alert-success shadow mb-4">
            <span>{{ session('message') }}</span>
        </div>
    @endif

    {{-- Filters & Search --}}
    <div class="card bg-base-100 shadow-sm border border-base-200">
        <div class="card-body p-4">
            <div class="flex flex-col gap-3 md:flex-row md:items-center md:justify-between">
                <div class="w-full md:w-md">
                    <label class="input input-bordered flex items-center gap-2 w-full">
                        <span class="icon-[tabler--search] text-lg opacity-70"></span>
                        <input type="text" class="grow" placeholder="Search by name, subject, or ID..."
                            wire:model.live.debounce.300ms="search" />
                    </label>
                </div>

                <div class="flex items-center gap-2">
                    <select class="select select-bordered select-sm w-full md:w-40" wire:model.live="filterDept">
                        <option value="">All Departments</option>
                        <option value="math">Mathematics</option>
                        <option value="science">Science</option>
                        <option value="arts">Arts</option>
                    </select>
                </div>
            </div>
        </div>
    </div>

    {{-- Table --}}
    <div class="bg-base-100 rounded-xl border border-base-200 shadow-sm overflow-hidden">
        <div class="p-6 pb-2">
            <h3 class="text-xl font-semibold">Faculty Information</h3>
            <p class="text-sm opacity-70">List of active teachers, their specialties, and contact details.</p>
        </div>

        <div class="overflow-x-auto">
            <table class="table w-full">
                <thead>
                    <tr class="text-xs uppercase tracking-wide opacity-70">
                        <th class="pl-6">No.</th>
                        <th>Teacher</th>
                        <th>Department/Subject</th>
                        <th>Contact</th>
                        <th>Status</th>
                        <th class="pr-6 text-right">Actions</th>
                    </tr>
                </thead>

                <tbody>
                    @forelse($teachers as $i => $t)
                        <tr class="hover transition-colors">
                            <td class="pl-6 align-middle">{{ $i + 1 }}</td>
                            <td class="align-middle">
                                <div class="flex items-center gap-3">
                                    <div class="avatar placeholder">
                                        {{-- Added 'flex items-center justify-center' to center the text --}}
                                        <div
                                            class="bg-primary/10 text-primary w-10 rounded-lg flex items-center justify-center">
                                            <span class="text-xs font-bold">{{ substr($t->name, 0, 2) }}</span>
                                        </div>
                                    </div>
                                    <div>
                                        <div class="font-semibold leading-tight">{{ $t->name }}</div>
                                        <div class="text-[11px] opacity-60 uppercase">ID: TCH-{{ $t->id }}</div>
                                    </div>
                                </div>
                            </td>
                            <td class="align-middle">
                                <div class="flex flex-col">
                                    <span class="text-sm font-medium">{{ $t->subject ?? 'General' }}</span>
                                    <span class="text-[11px] opacity-50">{{ $t->department ?? 'N/A' }}</span>
                                </div>
                            </td>
                            <td class="align-middle">
                                <div class="flex flex-col text-sm opacity-80">
                                    <div class="flex items-center gap-1">
                                        <span class="icon-[tabler--mail] text-xs"></span> {{ $t->email }}
                                    </div>
                                    <div class="flex items-center gap-1">
                                        <span class="icon-[tabler--phone] text-xs"></span> {{ $t->phone ?? '—' }}
                                    </div>
                                </div>
                            </td>
                            <td class="align-middle">
                                <span class="badge badge-soft badge-success badge-xs italic">Active</span>
                            </td>
                            <td class="pr-6 align-middle">
                                <div class="flex justify-end items-center gap-1">
                                    <button class="btn btn-sm btn-text text-primary"
                                        wire:click="openEdit({{ $t->id }})">
                                        {{-- <span class="icon-[tabler--edit-circle] text-lg"></span> --}}
                                        <span class="icon-[tabler--pencil] text-lg"></span>
                                    </button>
                                    <button class="btn btn-sm btn-text text-error"
                                        wire:click="confirmDelete({{ $t->id }})" title="Delete">
                                        <span class="icon-[tabler--trash] text-lg"></span>
                                    </button>
                                    <x-confirm-dialog :show="$confirmingDelete" title="Delete teacher" :message="'Are you sure you want to delete ' . $deleteName . '?'" />

                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="text-center py-12 opacity-50">No teachers found.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        {{-- Footer (pagination area like screenshot spacing) --}}
        <div class="px-6 py-4 border-t border-base-200">
            {{ $teachers->links() }}
        </div>
    </div>

    {{-- Modal (Adapted for Teachers) --}}
    <x-teacher-modal :show="$showModal" :title="$teacherId ? 'Edit Teacher Profile' : 'Register New Teacher'"
        subtitle="Fill in the information below. Fields with * are required.">
        <x-teacher-form :teacher-id="$teacherId" />
    </x-teacher-modal>

</div>
