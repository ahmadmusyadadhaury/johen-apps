<?php

namespace App\Livewire;

use App\Models\Employee;
use App\Models\User;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Hash;
use Livewire\Component;
use Livewire\WithPagination;

class UserTable extends Component
{
    use WithPagination;
    public bool $showCreateModal = false;
    public bool $showEditModal = false;
    public ?int $editId = null;

    public string $name = '';
    public string $username = '';
    public string $password = '';
    public string $password_confirmation = '';
    public string $role = 'staff';
    public ?int $linkEmployeeId = null;
    public string $filterRole = '';
    public string $search = '';
    public bool $showDeleteConfirm = false;
    public ?int $deleteId = null;

    public function updatingSearch(): void
    {
        $this->resetPage();
    }

    public function updatingFilterRole(): void
    {
        $this->resetPage();
    }

    protected function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'username' => ['required', 'string', 'max:255', 'regex:/^\S*$/', 'unique:users,username' . ($this->editId ? ',' . $this->editId : '')],
            'password' => [$this->editId ? 'nullable' : 'required', 'string', 'min:4', 'confirmed'],
            'role' => ['required', 'in:super_admin,gm_ceo,manager,koordinator,staff,koordinator_it,staff_it,koordinator_creative,koordinator_admin,koordinator_stock,koordinator_pubg,koordinator_ff,koordinator_mlbb,koordinator_efootball,koordinator_valorant,koordinator_roblox,koordinator_monkey_pubg,staff_creative,staff_host_pubg,staff_host_ff,staff_host_mlbb,staff_host_efootball,staff_host_valorant,staff_host_roblox,staff_host_monkey_pubg,staff_admin,staff_stock,staff_hr'],
        ];
    }

    protected function messages(): array
    {
        return [
            'name.required' => 'Nama wajib diisi.',
            'username.required' => 'Username wajib diisi.',
            'username.regex' => 'Username tidak boleh mengandung spasi.',
            'username.unique' => 'Username sudah digunakan.',
            'password.required' => 'Password wajib diisi.',
            'password.min' => 'Password minimal 4 karakter.',
            'password.confirmed' => 'Konfirmasi password tidak cocok.',
            'role.required' => 'Role wajib dipilih.',
        ];
    }

    public function openCreateModal(): void
    {
        Gate::authorize('create-data');
        $this->resetForm();
        $this->showCreateModal = true;
    }

    public function openEditModal(int $id): void
    {
        Gate::authorize('update-data');
        $user = User::findOrFail($id);
        $this->editId = $user->id;
        $this->name = $user->name;
        $this->username = $user->username;
        $this->role = $user->role;
        $this->linkEmployeeId = $user->employee?->id;
        $this->password = '';
        $this->password_confirmation = '';
        $this->showEditModal = true;
    }

    public function closeModal(): void
    {
        $this->showCreateModal = false;
        $this->showEditModal = false;
        $this->editId = null;
        $this->resetErrorBag();
    }

    public function save(): void
    {
        Gate::authorize('create-data');
        $this->validate();

        $user = User::create([
            'name' => $this->name,
            'username' => $this->username,
            'password' => Hash::make($this->password),
            'role' => $this->role,
        ]);

        if ($this->linkEmployeeId) {
            $user->employee_id = $this->linkEmployeeId;
            $user->save();
        }

        $this->closeModal();
        $this->dispatch('notify', type: 'success', message: 'Akun berhasil ditambahkan.');
    }

    public function update(): void
    {
        Gate::authorize('update-data');
        $user = User::findOrFail($this->editId);

        $rules = $this->rules();
        if (!$this->password) {
            $rules['password'] = ['nullable', 'string', 'min:4', 'confirmed'];
        }
        $this->validate($rules);

        $data = [
            'name' => $this->name,
            'username' => $this->username,
            'role' => $this->role,
        ];

        if ($this->password) {
            $data['password'] = Hash::make($this->password);
        }

        $user->update($data);

        $user->employee_id = $this->linkEmployeeId;
        $user->save();

        $this->closeModal();
        $this->dispatch('notify', type: 'success', message: 'Akun berhasil diperbarui.');
    }

    public function confirmDelete(int $id): void
    {
        Gate::authorize('delete-data');
        $this->deleteId = $id;
        $this->showDeleteConfirm = true;
    }

    public function executeDelete(): void
    {
        if (!$this->deleteId) return;
        Gate::authorize('delete-data');
        $user = User::findOrFail($this->deleteId);

        if ($user->id === auth()->id()) {
            $this->dispatch('notify', type: 'error', message: 'Tidak dapat menghapus akun sendiri.');
            $this->cancelDelete();
            return;
        }

        $user->delete();
        $this->dispatch('notify', type: 'success', message: 'Akun berhasil dihapus.');
        $this->cancelDelete();
    }

    public function cancelDelete(): void
    {
        $this->showDeleteConfirm = false;
        $this->deleteId = null;
    }

    public function render()
    {
        abort_unless(auth()->user()->isSuperAdmin(), 403);

        $users = User::with('employee')
            ->when($this->search, function ($query) {
                $query->where(function ($q) {
                    $q->where('name', 'like', "%{$this->search}%")
                      ->orWhere('username', 'like', "%{$this->search}%");
                });
            })
            ->when($this->filterRole, fn($q) => $q->where('role', $this->filterRole))
            ->orderByRaw("CASE role
                WHEN 'super_admin' THEN 1
                WHEN 'gm_ceo' THEN 2
                WHEN 'manager' THEN 3
                WHEN 'koordinator' THEN 4
                WHEN 'koordinator_it' THEN 4
                WHEN 'koordinator_creative' THEN 4
                WHEN 'koordinator_admin' THEN 4
                WHEN 'koordinator_pubg' THEN 4
                WHEN 'koordinator_ff' THEN 4
                WHEN 'koordinator_mlbb' THEN 4
                WHEN 'koordinator_efootball' THEN 4
                WHEN 'koordinator_roblox' THEN 4
                WHEN 'koordinator_monkey_pubg' THEN 4
                ELSE 5
            END, name")->paginate(20);
        $allEmployees = Employee::where('status', 'aktif')->orderBy('nama')->get();
        return view('livewire.user-table', compact('users', 'allEmployees'));
    }

    private function resetForm(): void
    {
        $this->editId = null;
        $this->name = '';
        $this->username = '';
        $this->password = '';
        $this->password_confirmation = '';
        $this->role = 'staff';
        $this->linkEmployeeId = null;
        $this->resetErrorBag();
    }
}
