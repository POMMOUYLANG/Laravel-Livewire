<?php

namespace App\Livewire;

use App\Models\Post;
use Livewire\Component;
use Livewire\Attributes\Layout;
use Livewire\WithPagination;

#[Layout('layouts.app')]
class PostPage extends Component
{
    use WithPagination;

    // Form fields
    public string $title = '';
    public string $body = '';
    public bool $is_published = true;
    public int $formKey = 0;


    // UI State
    public ?int $editingId = null;
    public string $search = '';

    protected function rules(): array
    {
        return [
            'title' => ['required', 'string', 'min:3', 'max:255'],
            'body' => ['required', 'string', 'min:5'],
            'is_published' => ['boolean'],
        ];
    }

    public function updatingSearch(): void
    {
        $this->resetPage();
    }

    public function resetForm(): void
    {
        $this->title = '';
        $this->body = '';
        $this->is_published = true;
        $this->editingId = null;

        $this->resetValidation();

        $this->formKey++; // ✅ force the form to re-render & clear inputs
    }


    // ✅ One method for create + update
    public function submit(): void
    {
        $data = $this->validate();

        if ($this->editingId) {
            Post::findOrFail($this->editingId)->update($data);
            session()->flash('message', 'Post updated successfully ✅');
        } else {
            Post::create($data);
            session()->flash('message', 'Post created successfully ✅');
        }

        $this->resetForm();
        $this->resetPage();
    }


    // ✅ Fill the form for editing
    public function edit($id)
    {
        $post = Post::findOrFail($id);

        $this->editingId = $post->id;
        $this->title = $post->title;
        $this->body = $post->body;
        $this->is_published = (bool) $post->is_published;

        $this->resetValidation();

        // Increment this to force Livewire to re-draw the form with the new data
        $this->formKey++;
    }

    public function delete(int $id): void
    {
        Post::whereKey($id)->delete();

        if ($this->editingId === $id) {
            $this->resetForm();
        }

        session()->flash('message', 'Post deleted ✅');
        $this->resetPage();
    }

    public function render()
    {
        $posts = Post::query()
            ->when($this->search !== '', function ($q) {
                $q->where(function ($qq) {
                    $qq->where('title', 'like', "%{$this->search}%")
                        ->orWhere('body', 'like', "%{$this->search}%");
                });
            })
            ->latest()
            ->paginate(7);

        return view('livewire.post-page', compact('posts'));
    }
}
