<div class="p-6 space-y-6">
    {{-- Header --}}
    <div class="flex flex-col gap-2 md:flex-row md:items-center md:justify-between">
        <div>
            <h2 class="text-2xl font-bold">Students</h2>
            <p class="text-sm opacity-70">Manage student records</p>
        </div>

        {{-- CHANGED: Calling openCreate instead of toggle --}}
        <button class="btn btn-primary gap-2" wire:click="openCreate">
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
                <label class="input input-bordered flex items-center gap-2 w-full md:w-96">
                    <span class="icon-[tabler--search] text-lg opacity-70"></span>
                    <input type="text" class="grow" placeholder="Search..."
                        wire:model.live.debounce.300ms="search" />
                </label>


                <div class="flex items-center gap-2">
                    <span class="text-sm opacity-70">Per page</span>
                    <select class="select select-bordered select-sm" wire:model.live="perPage">
                        <option value="5">5</option>
                        <option value="10">10</option>
                        <option value="25">25</option>
                    </select>
                </div>
            </div>
        </div>
    </div>

    {{-- Table --}}
    <div class="overflow-x-auto bg-base-100 rounded-xl shadow border border-base-200">
        <table class="table table-zebra w-full">
            <thead>
                <tr class="bg-base-200">
                    <th>#</th>
                    <th>Student</th>
                    <th>Class</th>
                    <th>Phone</th>
                    <th class="text-right">Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse($students as $i => $s)
                    <tr>
                        <td>{{ $students->firstItem() + $i }}</td>
                        <td>
                            <div class="font-bold">{{ $s->name }}</div>
                            <div class="text-sm opacity-50">{{ $s->email }}</div>
                        </td>
                        <td><span class="badge badge-ghost">{{ $s->class ?? 'N/A' }}</span></td>
                        <td>{{ $s->phone ?? '—' }}</td>
                        {{-- <td class="text-right"> <button class="btn btn-sm btn-ghost"
                                wire:click="openEdit({{ $s->id }})">Edit</button> <button
                                class="btn btn-sm btn-error btn-outline"
                                onclick="confirm('Are you sure?') || event.stopImmediatePropagation()"
                                wire:click="delete({{ $s->id }})">Delete</button> </td> --}}
                        <td class="text-right">
                            <div class="flex justify-end gap-2">
                                {{-- EDIT --}}
                                <button class="btn btn-sm btn-outline gap-2" wire:click="openEdit({{ $s->id }})">
                                    <span class="icon-[tabler--pencil] text-base"></span>
                                    Edit
                                </button>

                                {{-- DELETE --}}
                                <button class="btn btn-sm btn-error btn-outline gap-2"
                                    onclick="confirm('Are you sure?') || event.stopImmediatePropagation()"
                                    wire:click="delete({{ $s->id }})">
                                    <span class="icon-[tabler--trash] text-base"></span>
                                    Delete
                                </button>
                            </div>
                        </td>
                    @empty
                    <tr>
                        <td colspan="5" class="text-center py-10">No students found.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
        <div class="p-4">{{ $students->links() }}</div>
    </div>

    {{-- Modal --}}
    @if ($showModal)
        <div class="fixed inset-0 z-[9999] flex items-center justify-center overflow-y-auto"
            style="background: rgba(0,0,0,0.5); backdrop-filter: blur(4px);">

            <div class="modal-box relative max-w-2xl bg-base-100 p-6 rounded-xl shadow-2xl border border-base-300"
                style="opacity: 1; transform: scale(1);">

                <button class="btn btn-sm btn-circle btn-ghost absolute right-2 top-2"
                    wire:click="$set('showModal', false)">
                    <span class="icon-[tabler--x] text-xl"></span>
                </button>


                <h3 class="text-xl font-bold mb-4">
                    {{ $studentId ? 'Edit Student Profile' : 'Register New Student' }}
                </h3>

                <form wire:submit.prevent="save" class="space-y-4">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div class="form-control">
                            <label class="label"><span class="label-text">Name</span></label>
                            <input type="text" class="input input-bordered w-full" wire:model="name">
                            @error('name')
                                <span class="text-error text-xs">{{ $message }}</span>
                            @enderror
                        </div>

                        <div class="form-control">
                            <label class="label"><span class="label-text">Email</span></label>
                            <input type="email" class="input input-bordered w-full" wire:model="email">
                            @error('email')
                                <span class="text-error text-xs">{{ $message }}</span>
                            @enderror
                        </div>

                        <div class="form-control">
                            <label class="label"><span class="label-text">Class</span></label>
                            <input type="text" class="input input-bordered w-full" wire:model="class">
                        </div>

                        <div class="form-control">
                            <label class="label"><span class="label-text">Phone</span></label>
                            <input type="text" class="input input-bordered w-full" wire:model="phone">
                        </div>

                        <div class="form-control md:col-span-2">
                            <label class="label"><span class="label-text">Date of Birth</span></label>
                            <input type="date" class="input input-bordered w-full" wire:model="dob">
                        </div>
                    </div>

                    <div class="modal-action flex gap-2">
                        <button type="button" class="btn btn-ghost gap-2" wire:click="$set('showModal', false)">
                            <span class="icon-[tabler--x] text-lg"></span>
                            Cancel
                        </button>

                        <button type="submit" class="btn btn-primary px-8 gap-2" wire:loading.attr="disabled">
                            <span wire:loading class="loading loading-spinner loading-xs"></span>

                            @if ($studentId)
                                <span class="icon-[tabler--device-floppy] text-lg" wire:loading.remove></span>
                                Update
                            @else
                                <span class="icon-[tabler--plus] text-lg" wire:loading.remove></span>
                                Create
                            @endif
                        </button>
                    </div>

                </form>
            </div>
        </div>
    @endif
</div>
