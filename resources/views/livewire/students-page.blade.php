<div class="p-6 space-y-6">
    {{-- Header --}}
    <div class="flex flex-col gap-2 md:flex-row md:items-center md:justify-between">
        <div>
            <h2 class="font-sans text-2xl font-semibold">Students Management</h2>
            <p class="text-sm opacity-70">Manage student records</p>
        </div>

        <button class="btn btn-primary btn-outline gap-2" wire:click="openCreate">
            <span class="icon-[tabler--plus] text-lg"></span>
            Add Student
        </button>

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
                <div class="w-full md:w-[28rem]">
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
                        <th class="pl-6">#</th>
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
                                <div class="font-semibold leading-tight">{{ $s->name }}</div>
                                <div class="text-sm opacity-60">{{ $s->email }}</div>
                            </td>

                            <td class="align-middle">
                                <span class="badge badge-soft badge-neutral">
                                    {{ $s->class ?? 'N/A' }}
                                </span>
                            </td>

                            <td class="align-middle">
                                <span class="text-sm opacity-80">{{ $s->phone ?? '—' }}</span>
                            </td>

                            <td class="pr-6 align-middle">
                                <div class="flex justify-end items-center gap-1">

                                    {{-- Edit (icon-only like screenshot) --}}
                                    <button type="button" class="btn btn-sm btn-text" aria-label="Edit"
                                        wire:click="openEdit({{ $s->id }})" title="Edit">
                                        <span class="icon-[tabler--pencil] text-lg"></span>
                                    </button>

                                    {{-- Delete --}}
                                    <button type="button" class="btn btn-sm btn-text" aria-label="Delete"
                                        title="Delete"
                                        onclick="confirm('Are you sure?') || event.stopImmediatePropagation()"
                                        wire:click="delete({{ $s->id }})">
                                        <span class="icon-[tabler--trash] text-lg"></span>
                                    </button>

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
    @if ($showModal)
        <div class="fixed inset-0 z-[9999] flex items-center justify-center p-4" x-data
            x-on:keydown.escape.window="$wire.set('showModal', false)">
            {{-- Backdrop (click to close) --}}
            <div class="absolute inset-0 bg-black/50 backdrop-blur-sm" wire:click="$set('showModal', false)"></div>

            {{-- Modal Box --}}
            <div class="relative w-full max-w-2xl rounded-2xl bg-base-100 shadow-2xl border border-base-300"
                role="dialog" aria-modal="true">
                {{-- Header --}}
                <div class="flex items-start justify-between gap-3 px-6 py-5 border-b border-base-200">
                    <div>
                        <h3 class="text-xl font-bold leading-tight">
                            {{ $studentId ? 'Edit Student Profile' : 'Register New Student' }}
                        </h3>
                        <p class="text-sm opacity-70 mt-1">
                            Fill in the information below. Fields with * are required.
                        </p>
                    </div>

                    <button type="button" class="btn btn-sm btn-circle btn-ghost" aria-label="Close"
                        wire:click="$set('showModal', false)">
                        <span class="icon-[tabler--x] text-xl"></span>
                    </button>
                </div>

                {{-- Body --}}
                <form wire:submit.prevent="save" class="px-6 py-5 space-y-5">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        {{-- Name --}}
                        <div class="form-control">
                            <label class="label">
                                <span class="label-text font-medium">Name *</span>
                            </label>
                            <input type="text"
                                class="input input-bordered w-full @error('name') input-error @enderror"
                                placeholder="Student name" wire:model.defer="name" />
                            @error('name')
                                <label class="label">
                                    <span class="label-text-alt text-error">{{ $message }}</span>
                                </label>
                            @enderror
                        </div>

                        {{-- Email --}}
                        <div class="form-control">
                            <label class="label">
                                <span class="label-text font-medium">Email *</span>
                            </label>
                            <input type="email"
                                class="input input-bordered w-full @error('email') input-error @enderror"
                                placeholder="student@email.com" wire:model.defer="email" />
                            @error('email')
                                <label class="label">
                                    <span class="label-text-alt text-error">{{ $message }}</span>
                                </label>
                            @enderror
                        </div>

                        {{-- Class --}}
                        <div class="form-control">
                            <label class="label">
                                <span class="label-text font-medium">Class</span>
                            </label>
                            <input type="text" class="input input-bordered w-full" placeholder="10A, 11B..."
                                wire:model.defer="class" />
                        </div>

                        {{-- Phone --}}
                        <div class="form-control">
                            <label class="label">
                                <span class="label-text font-medium">Phone</span>
                            </label>
                            <input type="text" class="input input-bordered w-full" placeholder="+855..."
                                wire:model.defer="phone" />
                        </div>

                        {{-- DOB --}}
                        <div class="form-control md:col-span-2">
                            <label class="label">
                                <span class="label-text font-medium">Date of Birth</span>
                            </label>
                            <input type="date" class="input input-bordered w-full" wire:model.defer="dob" />
                        </div>
                    </div>

                    {{-- Footer / Actions --}}
                    <div class="flex flex-col-reverse md:flex-row md:justify-end gap-2 pt-2 border-t border-base-200">
                        <button type="button" class="btn btn-ghost gap-2" wire:click="$set('showModal', false)">
                            <span class="icon-[tabler--x] text-lg"></span>
                            Cancel
                        </button>

                        <button type="submit" class="btn btn-primary gap-2" wire:loading.attr="disabled"
                            wire:target="save">
                            <span wire:loading wire:target="save" class="loading loading-spinner loading-xs"></span>

                            <span wire:loading.remove wire:target="save">
                                @if ($studentId)
                                    <span class="icon-[tabler--device-floppy] text-lg"></span>
                                    Update
                                @else
                                    <span class="icon-[tabler--plus] text-lg"></span>
                                    Create
                                @endif
                            </span>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    @endif

</div>
