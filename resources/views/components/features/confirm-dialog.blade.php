@props([
    'show' => false,
    'title' => 'Confirm action',
    'message' => 'Are you sure?',
])

@if ($show)
    <div class="fixed inset-0 z-9999 flex items-center justify-center p-4" x-data
        x-on:keydown.escape.window="$wire.cancelDelete()">

        {{-- Backdrop (NOT black, NOT white) --}}
        <div class="absolute inset-0 bg-black/10" wire:click="cancelDelete"></div>

        {{-- Dialog --}}
        <div class="relative w-full max-w-md bg-base-100 rounded-2xl shadow-xl border border-base-300">
            <div class="px-6 py-4 border-b border-base-200 flex items-center gap-2">
                <span class="icon-[tabler--alert-triangle] text-error text-lg"></span>
                <h3 class="text-lg font-bold">{{ $title }}</h3>
            </div>


            <div class="px-6 py-5 space-y-2">
                <p class="text-sm text-base-content">
                    {{ $message }}
                </p>
            </div>


            <div class="flex justify-end gap-2 px-6 py-4 border-t border-base-200">
                <button class="btn btn-ghost" wire:click="cancelDelete">Cancel</button>

                <button class="btn btn-error gap-2" wire:click="deleteConfirmed">
                    <span class="icon-[tabler--trash]"></span>
                    Yes, delete
                </button>
            </div>
        </div>
    </div>
@endif
