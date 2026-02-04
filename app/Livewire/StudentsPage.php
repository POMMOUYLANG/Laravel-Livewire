<?php

namespace App\Livewire;

use App\Models\Student;
use Livewire\Component;
use Livewire\Attributes\Layout;
use Livewire\WithPagination;
use Illuminate\Validation\Rule;

#[Layout('layouts.app')]
class StudentsPage extends Component
{
    use WithPagination;

    public string $search = '';
    public int $perPage = 10;

    public ?int $studentId = null;
    public string $name = '';
    public string $email = '';
    public ?string $phone = null;
    public ?string $class = null;
    public ?string $dob = null;

    public bool $showModal = false;

    protected function rules(): array
    {
        return [
            'name'  => ['required', 'string', 'max:255'],
            'email' => [
                'required',
                'email',
                'max:255',
                Rule::unique('students', 'email')->ignore($this->studentId),
            ],
            'phone' => ['nullable', 'string', 'max:50'],
            'class' => ['nullable', 'string', 'max:50'],
            'dob'   => ['nullable', 'date'],
        ];
    }

    public function updatingSearch(): void
    {
        $this->resetPage();
    }

    // FIX: Explicitly call this from the "Add Student" button
    public function openCreate(): void
    {
        $this->resetForm();
        $this->showModal = true;
    }

    public function openEdit($id): void
    {
        $this->resetForm();
        $student = Student::findOrFail($id);

        $this->studentId = $student->id;
        $this->name      = $student->name;
        $this->email     = $student->email;
        $this->class     = $student->class;
        $this->phone     = $student->phone;
        $this->dob       = $student->dob ? \Carbon\Carbon::parse($student->dob)->format('Y-m-d') : null;

        $this->showModal = true;
    }

    public function save(): void
    {
        $this->validate();
        $isUpdate = !is_null($this->studentId);

        Student::updateOrCreate(
            ['id' => $this->studentId],
            [
                'name'  => $this->name,
                'email' => $this->email,
                'phone' => $this->phone,
                'class' => $this->class,
                'dob'   => $this->dob,
            ]
        );

        $this->showModal = false;
        session()->flash('message', $isUpdate ? 'Student updated successfully.' : 'Student created successfully.');
        $this->resetForm();
    }

    public function delete(int $id): void
    {
        Student::findOrFail($id)->delete();
        session()->flash('message', 'Student deleted.');
    }

    public function resetForm(): void
    {
        $this->studentId = null;
        $this->name = '';
        $this->email = '';
        $this->phone = null;
        $this->class = null;
        $this->dob = null;
        $this->resetValidation();
    }

    public function render()
    {
        return view('livewire.students-page', [
            'students' => Student::query()
                ->when($this->search, function ($q) {
                    $q->where('name', 'like', "%{$this->search}%")
                        ->orWhere('email', 'like', "%{$this->search}%")
                        ->orWhere('class', 'like', "%{$this->search}%");
                })
                ->latest()
                ->paginate($this->perPage)
        ]);
    }
}
