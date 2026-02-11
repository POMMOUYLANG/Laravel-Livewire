<?php

namespace App\Livewire;

use App\Models\Student;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Validation\Rule;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('components.layouts.app')]
class StudentsPage extends Component
{
    public string $search = '';
    public int $perPage = 10;

    public ?int $studentId = null;
    public string $name = '';
    public string $email = '';
    public ?string $phone = null;
    public ?string $class = null;
    public ?string $dob = null;

    public bool $showModal = false;

    public bool $confirmingDelete = false;
    public ?int $deleteId = null;

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

    // When search/perPage changes -> refresh grid
    public function updatedSearch(): void
    {
        $this->dispatch('grid-refresh', perPage: $this->perPage);
    }

    public function updatedPerPage(): void
    {
        $this->dispatch('grid-refresh', perPage: $this->perPage);
    }

    public function openCreate(): void
    {
        $this->resetForm();
        $this->showModal = true;
    }

    public function openEdit(int $id): void
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
        session()->flash('message', 'Saved.');
        $this->resetForm();

        $this->dispatch('students-updated');
    }

    public function confirmDelete(int $id): void
    {
        $this->deleteId = $id;
        $this->confirmingDelete = true;
    }

    public function cancelDelete(): void
    {
        $this->confirmingDelete = false;
        $this->deleteId = null;
    }

    public function deleteConfirmed(): void
    {
        Student::findOrFail($this->deleteId)->delete();
        $this->cancelDelete();

        session()->flash('message', 'Student deleted successfully.');
        $this->dispatch('students-updated');
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

    // Called by AG Grid datasource
    public function gridRows(array $payload): array
    {
        $startRow    = (int)($payload['startRow'] ?? 0);
        $endRow      = (int)($payload['endRow'] ?? ($startRow + $this->perPage));
        $sortModel   = $payload['sortModel'] ?? [];
        $filterModel = $payload['filterModel'] ?? [];

        $q = Student::query()->select('id', 'name', 'email', 'phone', 'class', 'dob');

        // Search
        if ($this->search) {
            $search = $this->search;
            $q->where(function (Builder $qq) use ($search) {
                $qq->where('name', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%")
                    ->orWhere('phone', 'like', "%{$search}%")
                    ->orWhere('class', 'like', "%{$search}%");
            });
        }

        // Basic text filters
        foreach ($filterModel as $field => $f) {
            $type  = $f['type'] ?? null;
            $value = $f['filter'] ?? null;

            if ($value === null || $value === '') continue;

            if ($type === 'contains')      $q->where($field, 'like', "%{$value}%");
            elseif ($type === 'equals')    $q->where($field, $value);
            elseif ($type === 'startsWith') $q->where($field, 'like', "{$value}%");
            elseif ($type === 'endsWith')  $q->where($field, 'like', "%{$value}");
        }

        // Sort
        if (!empty($sortModel)) {
            foreach ($sortModel as $s) {
                $colId = $s['colId'] ?? 'id';
                $dir   = $s['sort'] ?? 'desc';
                $q->orderBy($colId, $dir);
            }
        } else {
            $q->latest('id');
        }

        $total = (clone $q)->count();

        $rows = $q->skip($startRow)
            ->take(max(0, $endRow - $startRow))
            ->get()
            ->toArray();

        return ['rows' => $rows, 'total' => $total];
    }

    public function render()
    {
        return view('livewire.students-page');
    }
}
