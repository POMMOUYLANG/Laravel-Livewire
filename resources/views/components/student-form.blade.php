@props([
    'studentId' => null,
])

<form wire:submit.prevent="save" class="space-y-5">
    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">

        {{-- Name --}}
        <div class="form-control">
            <label class="label"><span class="label-text font-medium">Name *</span></label>
            <input type="text" class="input input-bordered w-full @error('name') input-error @enderror"
                placeholder="Student name" wire:model.defer="name" />
            @error('name')
                <label class="label"><span class="label-text-alt text-error">{{ $message }}</span></label>
            @enderror
        </div>

        {{-- Email --}}
        <div class="form-control">
            <label class="label"><span class="label-text font-medium">Email *</span></label>
            <input type="email" class="input input-bordered w-full @error('email') input-error @enderror"
                placeholder="student@email.com" wire:model.defer="email" />
            @error('email')
                <label class="label"><span class="label-text-alt text-error">{{ $message }}</span></label>
            @enderror
        </div>

        {{-- Class --}}
        <div class="form-control">
            <label class="label"><span class="label-text font-medium">Class</span></label>
            <input type="text" class="input input-bordered w-full" placeholder="10A, 11B..."
                wire:model.defer="class" />
        </div>

        {{-- Phone --}}
        <div class="form-control">
            <label class="label"><span class="label-text font-medium">Phone</span></label>
            <input type="text" class="input input-bordered w-full" placeholder="+855..." wire:model.defer="phone" />
        </div>

        {{-- DOB --}}
        <div class="form-control md:col-span-2">
            <label class="label"><span class="label-text font-medium">Date of Birth</span></label>
            <input type="date" class="input input-bordered w-full" wire:model.defer="dob" />
        </div>
    </div>

    {{-- Footer --}}
    <div class="flex flex-col-reverse md:flex-row md:justify-end gap-2 pt-4 border-t border-base-200">
        <button type="button" class="btn btn-ghost gap-2" wire:click="$set('showModal', false)">
            <span class="icon-[tabler--x] text-lg"></span>
            Cancel
        </button>

        <button type="submit" class="btn btn-primary gap-2" wire:loading.attr="disabled" wire:target="save">
            <span wire:loading wire:target="save" class="loading loading-spinner loading-xs"></span>

            <span wire:loading.remove wire:target="save" class="flex items-center gap-2">
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
