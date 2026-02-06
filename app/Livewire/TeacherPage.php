<?php

namespace App\Livewire;

use App\Models\Teacher;
use Livewire\Attributes\Layout;
use Livewire\Component;
use Livewire\WithPagination;

#[Layout('layouts.app')]
class TeacherPage extends Component
{
    use WithPagination;

    // Filter and Search properties
    public $search = '';
    public $filterDept = '';

    // Modal & Form properties
    public $showModal = false;
    public $teacherId;
    public $name, $email, $department, $subject, $phone;

    public bool $confirmingDelete = false;
    public ?int $deleteId = null;
    public string $deleteName = '';


    // Reset pagination when searching
    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function openCreate()
    {
        $this->resetValidation();
        $this->reset(['name', 'email', 'department', 'subject', 'phone', 'teacherId']);
        $this->showModal = true;
    }

    public function openEdit($id)
    {
        $this->resetValidation();
        $teacher = Teacher::findOrFail($id);

        $this->teacherId = $teacher->id;
        $this->name = $teacher->name;
        $this->email = $teacher->email;
        $this->department = $teacher->department;
        $this->subject = $teacher->subject;
        $this->phone = $teacher->phone;

        $this->showModal = true;
    }

    public function save()
    {
        $rules = [
            'name' => 'required|string|min:3',
            'email' => 'required|email|unique:teachers,email,' . $this->teacherId,
            'department' => 'required',
            'subject' => 'nullable|string',
            'phone' => 'nullable|string',
        ];

        $validatedData = $this->validate($rules);

        Teacher::updateOrCreate(
            ['id' => $this->teacherId],
            $validatedData
        );

        $this->showModal = false;
        session()->flash('message', $this->teacherId ? 'Teacher updated successfully!' : 'Teacher created successfully!');
    }

    public function delete($id)
    {
        Teacher::findOrFail($id)->delete();
        session()->flash('message', 'Teacher record deleted.');
    }

    public function render()
    {
        $teachers = Teacher::query()
            ->when($this->search, function ($query) {
                $query->where('name', 'like', '%' . $this->search . '%')
                    ->orWhere('email', 'like', '%' . $this->search . '%')
                    ->orWhere('id', 'like', '%' . $this->search . '%');
            })
            ->when($this->filterDept, function ($query) {
                $query->where('department', $this->filterDept);
            })
            ->latest()
            ->paginate(10);

        return view('livewire.teacher-page', [
            'teachers' => $teachers,
            'totalFaculty' => Teacher::count() // Added to make the stat card dynamic
        ]);
    }

    public function confirmDelete(int $id): void
    {
        $teacher = \App\Models\Teacher::findOrFail($id);

        $this->deleteId = $id;
        $this->deleteName = $teacher->name;
        $this->confirmingDelete = true;
    }

    public function cancelDelete(): void
    {
        $this->confirmingDelete = false;
        $this->deleteId = null;
        $this->deleteName = '';
    }

    public function deleteConfirmed(): void
    {
        \App\Models\Teacher::findOrFail($this->deleteId)->delete();

        $this->cancelDelete();

        session()->flash('message', 'Teacher deleted successfully.');
    }
}