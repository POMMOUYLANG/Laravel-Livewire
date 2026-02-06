<div class="p-6 space-y-6">

    {{-- Header --}}
    <div class="flex flex-col gap-3 sm:flex-row sm:items-end sm:justify-between">
        <div class="space-y-1">
            <h1 class="text-2xl font-bold tracking-tight">Manage Posts</h1>
            <p class="text-sm opacity-70">Create, update, and delete posts</p>
        </div>

        <div class="flex items-center gap-2">
            <button type="button" class="btn btn-ghost btn-sm" wire:click="resetForm">
                <span class="icon-[tabler--eraser]"></span>
                Clear
            </button>
        </div>
    </div>

    {{-- Flash message --}}
    @if (session('message'))
        <div class="alert alert-success shadow-sm">
            <span class="icon-[tabler--circle-check]"></span>
            <span>{{ session('message') }}</span>
        </div>
    @endif

    {{-- Search + stats --}}
    <div class="card bg-base-100 shadow-sm border border-base-200">
        <div class="card-body p-4">
            <div class="flex flex-col gap-3 md:flex-row md:items-center md:justify-between">
                <label class="input input-bordered flex items-center gap-2 w-full md:w-md">
                    <span class="icon-[tabler--search] opacity-70"></span>
                    <input type="text" class="grow" placeholder="Search title or content..."
                        wire:model.live="search" />
                    @if ($search)
                        <button type="button" class="btn btn-ghost btn-xs" wire:click="$set('search','')">
                            <span class="icon-[tabler--x]"></span>
                        </button>
                    @endif
                </label>

                <div class="text-sm opacity-70">
                    Showing <span class="font-semibold">{{ $posts->count() }}</span> of
                    <span class="font-semibold">{{ $posts->total() }}</span>
                </div>
            </div>
        </div>
    </div>

    {{-- Layout: Form + List --}}
    <div class="grid gap-6 lg:grid-cols-5">
        {{-- Form --}}
        <div class="lg:col-span-2">
            <div id="post-form" class="card bg-base-100 shadow-sm border border-base-200"
                wire:key="post-form-{{ $formKey }}">
                <div class="card-body p-5 space-y-4">

                    <div class="flex items-start justify-between gap-3">
                        <div class="space-y-1">
                            <h2 class="text-lg font-semibold">
                                {{ $editingId ? 'Edit Post' : 'Create Post' }}
                            </h2>
                            <p class="text-xs opacity-60">
                                {{ $editingId ? 'Update the post details below.' : 'Fill in the form to publish a new post.' }}
                            </p>
                        </div>

                        @if ($editingId)
                            <span class="badge badge-warning badge-sm">Editing #{{ $editingId }}</span>
                        @else
                            <span class="badge badge-ghost badge-sm">New</span>
                        @endif
                    </div>

                    <div class="divider my-0"></div>

                    <form class="space-y-4" wire:submit.prevent="submit">

                        {{-- Title --}}
                        <div class="form-control">
                            <label class="label">
                                <span class="label-text font-medium">Title</span>
                            </label>

                            <input type="text" wire:model.live="title" placeholder="Enter a post title..."
                                class="input input-bordered w-full @error('title') input-error @enderror" />

                            @error('title')
                                <label class="label">
                                    <span class="label-text-alt text-error">{{ $message }}</span>
                                </label>
                            @enderror
                        </div>

                        {{-- Body --}}
                        <div class="form-control">
                            <label class="label">
                                <span class="label-text font-medium">Content</span>
                            </label>

                            <textarea wire:key="body-{{ $formKey }}" wire:model="body" placeholder="Write your content..."
                                class="textarea textarea-bordered w-full min-h-40 @error('body') textarea-error @enderror"></textarea>

                            @error('body')
                                <label class="label">
                                    <span class="label-text-alt text-error">{{ $message }}</span>
                                </label>
                            @enderror
                        </div>

                        {{-- Published --}}
                        <div class="rounded-xl border border-base-300 p-4 bg-base-200/30">
                            <div class="flex items-center justify-between gap-4">
                                <div>
                                    <div class="font-medium flex items-center gap-2">
                                        <span class="icon-[tabler--world] opacity-70"></span>
                                        Published
                                    </div>
                                    <div class="text-sm opacity-70">Toggle visibility for this post</div>
                                </div>

                                <div class="flex items-center gap-2">
                                    <span class="text-xs opacity-60">{{ $is_published ? 'On' : 'Off' }}</span>
                                    <input wire:key="pub-{{ $formKey }}" type="checkbox"
                                        class="toggle toggle-primary" wire:model="is_published">
                                </div>
                            </div>
                        </div>

                        {{-- Actions (sticky feel) --}}
                        <div class="pt-2">
                            <div class="flex flex-col gap-2 sm:flex-row sm:items-center sm:justify-end">
                                <button type="button" class="btn btn-ghost" wire:click="resetForm">
                                    <span class="icon-[tabler--arrow-back-up]"></span>
                                    Cancel
                                </button>

                                <button type="submit" class="btn btn-primary">
                                    <span wire:loading.remove wire:target="submit" class="flex items-center gap-2">
                                        <span class="icon-[tabler--device-floppy]"></span>
                                        {{ $editingId ? 'Update Post' : 'Create Post' }}
                                    </span>

                                    <span wire:loading wire:target="submit" class="flex items-center gap-2">
                                        <span class="loading loading-spinner loading-sm"></span>
                                        {{ $editingId ? 'Updating...' : 'Saving...' }}
                                    </span>
                                </button>
                            </div>
                        </div>

                    </form>
                </div>
            </div>
        </div>

        {{-- Posts List --}}
        <div class="lg:col-span-3">
            <div class="card bg-base-100 shadow-sm border border-base-200">
                <div class="card-body p-5 space-y-4">

                    <div class="flex items-center justify-between">
                        <h2 class="text-lg font-semibold">All Posts</h2>

                        <div class="join">
                            <button type="button" class="btn btn-sm btn-ghost join-item"
                                onclick="window.scrollTo({top:0, behavior:'smooth'})">
                                <span class="icon-[tabler--arrow-up]"></span>
                            </button>
                            <button type="button" class="btn btn-sm btn-ghost join-item"
                                onclick="document.getElementById('post-form')?.scrollIntoView({behavior:'smooth'})">
                                <span class="icon-[tabler--edit]"></span>
                            </button>
                        </div>
                    </div>

                    <div class="divider my-0"></div>

                    @if ($posts->isEmpty())
                        <div
                            class="rounded-2xl border border-dashed border-base-300 bg-base-200/40 p-10 text-center space-y-2">
                            <div
                                class="mx-auto w-12 h-12 rounded-2xl bg-base-100 flex items-center justify-center shadow-sm">
                                <span class="icon-[tabler--notes] text-xl"></span>
                            </div>
                            <div class="text-lg font-semibold">No posts found</div>
                            <div class="text-sm opacity-70">Try creating a post or adjust your search.</div>
                            <div class="pt-2">
                                <button type="button" class="btn btn-primary btn-sm"
                                    onclick="document.getElementById('post-form')?.scrollIntoView({behavior:'smooth'})">
                                    <span class="icon-[tabler--plus]"></span>
                                    Create your first post
                                </button>
                            </div>
                        </div>
                    @else
                        <div class="space-y-3">
                            @foreach ($posts as $post)
                                <div
                                    class="rounded-2xl border border-base-200 bg-base-100 p-4 hover:shadow-md transition">
                                    <div class="flex flex-col gap-3 md:flex-row md:items-start md:justify-between">

                                        <div class="space-y-2 min-w-0">
                                            <div class="flex items-center gap-2 flex-wrap">
                                                <h3 class="font-semibold text-base truncate max-w-[32rem]">
                                                    {{ $post->title }}
                                                </h3>

                                                @if ($post->is_published)
                                                    <span class="badge badge-success badge-sm">Published</span>
                                                @else
                                                    <span class="badge badge-ghost badge-sm">Draft</span>
                                                @endif
                                            </div>

                                            <p class="text-sm opacity-70 leading-relaxed">
                                                {{ \Illuminate\Support\Str::limit($post->body, 180) }}
                                            </p>

                                            <div class="text-xs opacity-60 flex items-center gap-2">
                                                <span class="icon-[tabler--clock]"></span>
                                                {{ $post->created_at?->diffForHumans() }}
                                            </div>
                                        </div>

                                        <div class="flex items-center gap-2 shrink-0">
                                            <div class="tooltip" data-tip="Update">
                                                <button type="button" class="btn btn-sm btn-outline"
                                                    wire:click="edit({{ $post->id }})"
                                                    x-on:click="$nextTick(() => document.getElementById('post-form')?.scrollIntoView({behavior:'smooth'}))">
                                                    Update
                                                </button>

                                            </div>

                                            <div class="tooltip" data-tip="Delete">
                                                <button type="button" class="btn btn-sm btn-error btn-outline"
                                                    onclick="confirm('Delete this post?') || event.stopImmediatePropagation()"
                                                    wire:click="delete({{ $post->id }})">
                                                    <span class="icon-[tabler--trash]"></span>
                                                    Delete
                                                </button>
                                            </div>
                                        </div>

                                    </div>
                                </div>
                            @endforeach
                        </div>

                        <div class="pt-2">
                            {{ $posts->links() }}
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

</div>
