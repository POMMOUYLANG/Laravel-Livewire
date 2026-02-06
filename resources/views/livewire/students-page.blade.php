<div class="p-6 space-y-6">
    {{-- Header --}}
    <div class="flex flex-col gap-2 md:flex-row md:items-center md:justify-between">
        <div>
            <h2 class="font-sans text-2xl font-semibold">Students Management</h2>
            <p class="text-sm opacity-70">Manage student records</p>
        </div>

        <button class="btn btn-primary btn-outline gap-2" wire:click="openCreate">
            <span class="icon-[tabler--user-plus] text-lg"></span>
            Add Student
        </button>
    </div>

    {{-- Stats Row --}}
    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
        <div class="card bg-base-100 border border-base-200 shadow-sm">
            <div class="card-body p-4 flex-row items-center gap-4">
                <div class="btn btn-square btn-soft btn-info no-animation">
                    <span class="icon-[tabler--users-group] text-xl"></span>
                </div>
                <div>
                    <p class="text-xs opacity-60 uppercase font-bold tracking-tighter">Total Students</p>
                    <p class="text-xl font-bold">30</p>
                </div>
            </div>
        </div>
    </div>

    {{-- Flash message --}}
    @if (session('message'))
        <div class="alert alert-success shadow mb-4">
            <span>{{ session('message') }}</span>
        </div>
    @endif

    {{-- Filters & Search --}}
    <div class="card bg-base-100 shadow">
        <div class="card-body p-4">
            <div class="flex flex-col gap-3 md:flex-row md:items-center md:justify-between">

                {{-- Search --}}
                <div class="w-full md:w-md">
                    <label class="input input-bordered flex items-center gap-2 w-full">
                        <span class="icon-[tabler--search] text-lg opacity-70"></span>

                        <input type="text" class="grow" placeholder="Search name, email, phone..."
                            wire:model.live.debounce.300ms="search" />

                        @if ($search)
                            <button type="button" class="btn btn-ghost btn-sm btn-circle" aria-label="Clear search"
                                wire:click="$set('search','')">
                                <span class="icon-[tabler--x] text-lg"></span>
                            </button>
                        @endif
                    </label>

                    <div class="mt-1 text-xs opacity-60">
                        Tip: type to filter results instantly
                    </div>
                </div>

                {{-- Per page --}}
                <div class="flex items-center justify-between md:justify-end gap-2 w-full md:w-auto">
                    <span class="text-sm opacity-70 whitespace-nowrap">Rows</span>

                    <select class="select select-bordered select-sm w-full md:w-32" wire:model.live="perPage">
                        <option value="5">5 / page</option>
                        <option value="10">10 / page</option>
                        <option value="25">25 / page</option>
                        <option value="50">50 / page</option>
                    </select>
                </div>

            </div>
        </div>
    </div>

    {{-- Table --}}
    <div class="bg-base-100 rounded-xl border border-base-200 shadow-sm overflow-hidden">
        <div class="p-6 pb-2">
            <h3 class="text-xl font-semibold">Students Info</h3>
            <p class="text-sm opacity-70">Browse student records with server-side processing.</p>
        </div>

        <div class="px-6 pb-6">
            <div wire:ignore id="studentsGrid" data-per-page="{{ $perPage }}" style="height: 520px; width: 100%;">
            </div>
        </div>
    </div>

    @push('scripts')
        @vite('resources/js/pages/students.js')
    @endpush

    {{-- Modal --}}
    <x-students.student-modal :show="$showModal" :title="$studentId ? 'Edit Student Profile' : 'Register New Student'"
        subtitle="Fill in the information below. Fields with * are required.">
        <x-students.student-form :student-id="$studentId" />
    </x-students.student-modal>
</div>
