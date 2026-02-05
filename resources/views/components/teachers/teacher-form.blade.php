@props([
    'teacherId' => null,
])

<form wire:submit.prevent="save" class="space-y-4">
    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">

        <div class="form-control">
            <label class="label"><span class="label-text font-bold">Full Name *</span></label>
            <input type="text" class="input input-bordered w-full @error('name') input-error @enderror"
                   wire:model.defer="name" placeholder="e.g. Dr. Sarah Smith" />
            @error('name')
                <label class="label"><span class="label-text-alt text-error">{{ $message }}</span></label>
            @enderror
        </div>

        <div class="form-control">
            <label class="label"><span class="label-text font-bold">Email Address *</span></label>
            <input type="email" class="input input-bordered w-full @error('email') input-error @enderror"
                   wire:model.defer="email" placeholder="teacher@email.com" />
            @error('email')
                <label class="label"><span class="label-text-alt text-error">{{ $message }}</span></label>
            @enderror
        </div>

        <div class="form-control">
            <label class="label"><span class="label-text font-bold">Department</span></label>
            <select class="select select-bordered w-full" wire:model.defer="department">
                <option value="">Select department</option>
                <option value="Mathematics">Mathematics</option>
                <option value="Science">Science</option>
                <option value="Language">Language</option>
            </select>
        </div>

        <div class="form-control">
            <label class="label"><span class="label-text font-bold">Subject Specialty</span></label>
            <input type="text" class="input input-bordered w-full"
                   wire:model.defer="subject" placeholder="e.g. Physics" />
        </div>

    </div>

    <div class="flex justify-end gap-2 pt-4 border-t border-base-200">
        <button type="button" class="btn btn-ghost"
                wire:click="$set('showModal', false)">
            Cancel
        </button>

        <button type="submit" class="btn btn-primary px-8" wire:loading.attr="disabled" wire:target="save">
            <span wire:loading wire:target="save" class="loading loading-spinner loading-xs"></span>

            <span wire:loading.remove wire:target="save">
                @if ($teacherId)
                    Update Teacher
                @else
                    Save Teacher
                @endif
            </span>
        </button>
    </div>
</form>
