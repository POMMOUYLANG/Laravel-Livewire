<div class="p-6 space-y-6">

    {{-- Header --}}
    <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
        <div>
            <h1 class="text-2xl font-bold">Manage Posts</h1>
            <p class="text-sm opacity-70">Create, update, and delete posts</p>
        </div>

        <div class="flex items-center gap-2">
            <button type="button" class="btn btn-ghost btn-sm" wire:click="resetForm">
                Clear
            </button>
        </div>
    </div>

    {{-- Flash message --}}
    @if (session('message'))
        <div class="alert alert-success shadow">
            <span>{{ session('message') }}</span>
        </div>
    @endif

    {{-- Search + stats --}}
    <div class="card bg-base-100 shadow">
        <div class="card-body p-4">
            <div class="flex flex-col gap-3 md:flex-row md:items-center md:justify-between">
                <label class="input input-bordered flex items-center gap-2 w-full md:w-96">
                    <span class="opacity-60">🔎</span>
                    <input type="text" class="grow" placeholder="Search title or content..."
                        wire:model.live="search" />
                </label>

                <div class="text-sm opacity-70">
                    Showing <span class="font-semibold">{{ $posts->count() }}</span> of
                    <span class="font-semibold">{{ $posts->total() }}</span>
                </div>
            </div>
        </div>
    </div>

    {{-- Form --}}
    <div id="post-form" class="card bg-base-100 shadow" wire:key="post-form-{{ $formKey }}">

        <div class="card-body space-y-4">

            <div class="flex items-center justify-between">
                <h2 class="text-lg font-semibold">
                    {{ $editingId ? 'Edit Post' : 'Create Post' }}
                </h2>

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

                    <input type="text" wire:model="title" placeholder="Enter a post title..."
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

                    <textarea wire:model="body" placeholder="Write your content..."
                        class="textarea textarea-bordered w-full min-h-[140px] @error('body') textarea-error @enderror"></textarea>

                    @error('body')
                        <label class="label">
                            <span class="label-text-alt text-error">{{ $message }}</span>
                        </label>
                    @enderror
                </div>

                {{-- Published --}}
                <div class="flex items-center justify-between rounded-lg border border-base-300 p-3">
                    <div>
                        <div class="font-medium">Published</div>
                        <div class="text-sm opacity-70">Toggle visibility for this post</div>
                    </div>

                    <input type="checkbox" class="toggle toggle-primary" wire:model="is_published">
                </div>

                {{-- Actions --}}
                <div class="flex flex-col gap-2 sm:flex-row sm:items-center sm:justify-end">
                    <button type="button" class="btn btn-ghost" wire:click="resetForm">
                        Cancel
                    </button>

                    <button type="submit" class="btn btn-primary">
                        <span wire:loading.remove wire:target="submit">
                            {{ $editingId ? 'Update Post' : 'Create Post' }}
                        </span>
                        <span wire:loading wire:target="submit" class="loading loading-spinner loading-sm"></span>
                        <span wire:loading wire:target="submit">
                            {{ $editingId ? 'Updating...' : 'Saving...' }}
                        </span>
                    </button>
                </div>
            </form>
        </div>
    </div>

    {{-- Posts List --}}
    <div class="card bg-base-100 shadow">
        <div class="card-body space-y-4">
            <div class="flex items-center justify-between">
                <h2 class="text-lg font-semibold">All Posts</h2>
            </div>

            <div class="divider my-0"></div>

            @if ($posts->isEmpty())
                <div class="p-8 text-center rounded-xl bg-base-200">
                    <div class="text-lg font-semibold">No posts found</div>
                    <div class="text-sm opacity-70">Try creating a post or change your search.</div>
                </div>
            @else
                <div class="space-y-3">
                    @foreach ($posts as $post)
                        <div class="rounded-xl border border-base-300 p-4 hover:shadow transition">
                            <div class="flex flex-col gap-3 md:flex-row md:items-start md:justify-between">

                                <div class="space-y-1">
                                    <div class="flex items-center gap-2">
                                        <h3 class="font-semibold text-base">{{ $post->title }}</h3>

                                        @if ($post->is_published)
                                            <span class="badge badge-success badge-sm">Published</span>
                                        @else
                                            <span class="badge badge-ghost badge-sm">Draft</span>
                                        @endif
                                    </div>

                                    <p class="text-sm opacity-70 leading-relaxed">
                                        {{ \Illuminate\Support\Str::limit($post->body, 180) }}
                                    </p>

                                    <div class="text-xs opacity-60">
                                        {{ $post->created_at?->diffForHumans() }}
                                    </div>
                                </div>

                                <div class="flex items-center gap-2">
                                    <button type="button" class="btn btn-sm btn-outline"
                                        wire:click="edit({{ $post->id }})"
                                        onclick="document.getElementById('post-form')?.scrollIntoView({behavior:'smooth'})">
                                        Update
                                    </button>

                                    <button type="button" class="btn btn-sm btn-error btn-outline"
                                        onclick="confirm('Delete this post?') || event.stopImmediatePropagation()"
                                        wire:click="delete({{ $post->id }})">
                                        Delete
                                    </button>
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
