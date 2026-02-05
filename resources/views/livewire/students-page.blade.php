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

    {{-- Stats Row (New Addition for Design Depth) --}}
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
        {{-- Repeat for other stats if needed --}}
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

                        {{-- Clear button (only show if there is search text) --}}
                        @if ($search)
                            <button type="button" class="btn btn-ghost btn-sm btn-circle" aria-label="Clear search"
                                wire:click="$set('search','')">
                                <span class="icon-[tabler--x] text-lg"></span>
                            </button>
                        @endif
                    </label>

                    {{-- Small helper text --}}
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

        {{-- Top padding like the screenshot --}}
        <div class="p-6 pb-2">
            <h3 class="text-xl font-semibold">Students Info</h3>
            <p class="text-sm opacity-70">Browse a list of student information such as name, email, class, phone & more.
            </p>
        </div>

        <div class="overflow-x-auto">
            <table class="table w-full">
                {{-- Optional caption (kept minimal; screenshot uses a header area, but caption is valid) --}}
                <caption class="sr-only">Students table</caption>

                <thead>
                    <tr class="text-xs uppercase tracking-wide opacity-70">
                        <th class="pl-6">No.</th>
                        <th>Student</th>
                        <th>Class</th>
                        <th>Phone</th>
                        <th class="pr-6 text-right">Actions</th>
                    </tr>
                </thead>

                <tbody>
                    @forelse($students as $i => $s)
                        <tr class="hover">
                            <td class="pl-6 align-middle">
                                {{ $students->firstItem() + $i }}
                            </td>

                            <td class="align-middle">
                                <div class="flex items-center gap-3">
                                    <div class="avatar placeholder">
                                        {{-- Added 'flex items-center justify-center' to center the text --}}
                                        <div
                                            class="bg-primary/10 text-primary w-10 rounded-lg flex items-center justify-center">
                                            <span class="text-xs font-bold">{{ substr($s->name, 0, 2) }}</span>
                                        </div>
                                    </div>
                                    <div>
                                        <div class="font-semibold leading-tight">{{ $s->name }}</div>
                                        <div class="text-[11px] opacity-60 uppercase">ID: TCH-{{ $s->id }}</div>
                                    </div>
                                </div>
                            </td>

                            <td class="align-middle">
                                <span class="badge badge-soft badge-neutral">
                                    {{ $s->class ?? 'N/A' }}
                                </span>
                            </td>

                            <td class="align-middle">
                                <div class="flex flex-col text-sm opacity-80">
                                    <div class="flex items-center gap-1">
                                        <span class="icon-[tabler--mail] text-xs"></span> {{ $s->email }}
                                    </div>
                                    <div class="flex items-center gap-1">
                                        <span class="icon-[tabler--phone] text-xs"></span> {{ $s->phone ?? '—' }}
                                    </div>
                                </div>
                            </td>

                            <td class="pr-6 align-middle">
                                <div class="flex justify-end items-center gap-1">

                                    {{-- Edit (icon-only like screenshot) --}}
                                    <button type="button" class="btn btn-sm btn-text text-primary" aria-label="Edit"
                                        wire:click="openEdit({{ $s->id }})" title="Edit">
                                        <span class="icon-[tabler--pencil] text-lg"></span>
                                    </button>

                                    {{-- Delete --}}
                                    <button type="button" class="btn btn-sm btn-text text-error" aria-label="Delete"
                                        title="Delete" wire:click="confirmDelete({{ $s->id }})">
                                        <span class="icon-[tabler--trash] text-lg"></span>
                                    </button>
                                    <x-features.confirm-dialog :show="$confirmingDelete" title="Delete student"
                                        message="Are you sure you want to delete this student?" />


                                    <button type="button" class="btn btn-sm btn-text" aria-label="More" title="More"
                                        tabindex="0">
                                        <span class="icon-[tabler--dots-vertical] text-lg"></span>
                                    </button>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="text-center py-12 opacity-70">
                                No students found.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{-- Footer (pagination area like screenshot spacing) --}}
        <div class="px-6 py-4 border-t border-base-200">
            {{ $students->links() }}
        </div>
    </div>


    {{-- Modal --}}
    <x-students.student-modal :show="$showModal" :title="$studentId ? 'Edit Student Profile' : 'Register New Student'"
        subtitle="Fill in the information below. Fields with * are required.">
        <x-students.student-form :student-id="$studentId" />
    </x-students.student-modal>


</div>
