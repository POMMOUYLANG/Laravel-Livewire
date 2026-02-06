@props([
    'show' => false, // boolean
    'title' => 'Student', // string
    'subtitle' => null, // string|null
])

@if ($show)
    <div class="fixed inset-0 z-9999 flex items-center justify-center p-4" x-data
        x-on:keydown.escape.window="$wire.set('showModal', false)">

        {{-- Backdrop --}}
        <div class="absolute inset-0 backdrop-blur-sm" wire:click="$set('showModal', false)"></div>

        {{-- Modal box --}}
        <div class="relative w-full max-w-2xl rounded-2xl bg-base-100 shadow-2xl border border-base-300" role="dialog"
            aria-modal="true">

            {{-- Header --}}
            <div class="flex items-start justify-between gap-3 px-6 py-5 border-b border-base-200">
                <div>
                    <h3 class="text-xl font-bold leading-tight">{{ $title }}</h3>
                    @if ($subtitle)
                        <p class="text-sm opacity-70 mt-1">{{ $subtitle }}</p>
                    @endif
                </div>

                <button type="button" class="btn btn-sm btn-circle btn-ghost" aria-label="Close"
                    wire:click="$set('showModal', false)">
                    <span class="icon-[tabler--x] text-xl"></span>
                </button>
            </div>

            {{-- Body --}}
            <div class="px-6 py-5">
                {{ $slot }}
            </div>
        </div>
    </div>
@endif
