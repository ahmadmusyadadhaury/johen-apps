<?php

namespace App\Livewire;

use App\Models\Employee;
use App\Models\Office;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
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

    public array $officeIds = [];

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
            'username' => ['required', 'string', 'max:255', 'regex:/^\S*$/', 'unique:users,username'.($this->editId ? ','.$this->editId : '')],
            'password' => [$this->editId ? 'nullable' : 'required', 'string', 'min:4', 'confirmed'],
            'role' => ['required', 'in:'.(auth()->user()->isSuperAdmin()
                ? 'super_admin,admin_master,gm_ceo,manager,koordinator,staff,streamer,koordinator_it,staff_it,koordinator_creative,koordinator_admin,koordinator_stock,koordinator_pubg,koordinator_ff,koordinator_mlbb,koordinator_efootball,koordinator_valorant,koordinator_roblox,koordinator_monkey_pubg,staff_creative,staff_host_pubg,staff_host_ff,staff_host_mlbb,staff_host_efootball,staff_host_valorant,staff_host_roblox,staff_host_monkey_pubg,staff_host_fc_mobile,staff_admin,staff_stock,staff_hr,koordinator_project'
                : 'admin_master,gm_ceo,manager,koordinator,staff,streamer,koordinator_it,staff_it,koordinator_creative,koordinator_admin,koordinator_stock,koordinator_pubg,koordinator_ff,koordinator_mlbb,koordinator_efootball,koordinator_valorant,koordinator_roblox,koordinator_monkey_pubg,staff_creative,staff_host_pubg,staff_host_ff,staff_host_mlbb,staff_host_efootball,staff_host_valorant,staff_host_roblox,staff_host_monkey_pubg,staff_host_fc_mobile,staff_admin,staff_stock,staff_hr,koordinator_project')],
            'officeIds' => ['required', 'array', 'min:1'],
            'officeIds.*' => ['integer', 'distinct', 'exists:offices,id'],
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
            'officeIds.required' => 'Pilih minimal satu kantor.',
            'officeIds.min' => 'Pilih minimal satu kantor.',
            'officeIds.*.exists' => 'Kantor yang dipilih tidak ditemukan.',
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
        abort_unless(auth()->user()->isSuperAdmin(), 403);
        $user = User::findOrFail($id);
        $this->editId = $user->id;
        $this->name = $user->name;
        $this->username = $user->username;
        $this->role = $user->role;
        $this->linkEmployeeId = $user->employee?->id;
        $this->officeIds = $user->hasGlobalOfficeAccess()
            ? Office::active()->orderBy('sort_order')->pluck('id')->map(fn ($id) => (int) $id)->all()
            : $user->offices()->orderByPivot('is_default', 'desc')->orderBy('sort_order')->pluck('offices.id')->map(fn ($id) => (int) $id)->all();
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
        $this->normalizeOfficeIds();
        $this->validate();

        DB::transaction(function (): void {
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

            $user->offices()->sync($this->officeSyncData());
        });

        $this->closeModal();
        $this->dispatch('notify', type: 'success', message: 'Akun berhasil ditambahkan.');
    }

    public function update(): void
    {
        abort_unless(auth()->user()->isSuperAdmin(), 403);
        $user = User::findOrFail($this->editId);

        $rules = $this->rules();
        if (! $this->password) {
            $rules['password'] = ['nullable', 'string', 'min:4', 'confirmed'];
        }
        $this->normalizeOfficeIds();
        $this->validate($rules);

        $data = [
            'name' => $this->name,
            'username' => $this->username,
            'role' => $this->role,
        ];

        if ($this->password) {
            $data['password'] = Hash::make($this->password);
        }

        DB::transaction(function () use ($user, $data): void {
            $user->update($data);

            $user->employee_id = $this->linkEmployeeId;
            $user->save();
            $user->offices()->sync($this->officeSyncData());
        });

        $this->closeModal();
        $this->dispatch('notify', type: 'success', message: 'Akun berhasil diperbarui.');
    }

    public function confirmDelete(int $id): void
    {
        abort_unless(auth()->user()->isSuperAdmin(), 403);
        $this->deleteId = $id;
        $this->showDeleteConfirm = true;
    }

    public function executeDelete(): void
    {
        if (! $this->deleteId) {
            return;
        }
        abort_unless(auth()->user()->isSuperAdmin(), 403);
        $user = User::findOrFail($this->deleteId);

        if ($user->id === auth()->id()) {
            $this->dispatch('notify', type: 'error', message: 'Tidak dapat menghapus akun sendiri.');
            $this->cancelDelete();

            return;
        }

        DB::transaction(function () use ($user): void {
            // Putuskan relasi akun-karyawan agar akun pengganti bisa ditautkan.
            Employee::where('user_id', $user->id)->update(['user_id' => null]);

            // Arsipkan akun agar foreign key riwayat transaksi tetap valid,
            // sekaligus hapus identitas login dan token aksesnya.
            $identifier = Str::uuid()->toString();
            $user->forceFill([
                'name' => 'Akun dihapus',
                'username' => 'deleted_'.$user->id.'_'.$identifier,
                'email' => 'deleted+'.$user->id.'+'.$identifier.'@invalid.local',
                'password' => Hash::make(Str::random(64)),
                'pin_hash' => null,
                'employee_id' => null,
            ])->save();

            $user->tokens()->delete();
            $user->delete();
        });

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
        abort_unless(auth()->user()->isSuperAdmin() || auth()->user()->isAdminMaster(), 403);

        $users = User::with(['employee:id,nama', 'offices:id,name'])
            ->when($this->search, function ($query) {
                $query->where(function ($q) {
                    $q->where('name', 'like', "%{$this->search}%")
                        ->orWhere('username', 'like', "%{$this->search}%");
                });
            })
            ->when($this->filterRole, fn ($q) => $q->where('role', $this->filterRole))
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
                WHEN 'streamer' THEN 5
                ELSE 5
            END, name")->paginate(20);
        $allEmployees = Employee::where('tipe', 'karyawan_aktif')->orderBy('nama')->get(['id', 'nama', 'nik']);
        $offices = Office::active()->orderBy('sort_order')->get(['id', 'name']);

        return view('livewire.user-table', compact('users', 'allEmployees', 'offices'));
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
        $this->officeIds = Office::active()
            ->where('code', Office::CODE_SUMMARECON_BANDUNG)
            ->pluck('id')
            ->map(fn ($id) => (int) $id)
            ->all();
        $this->resetErrorBag();
    }

    private function normalizeOfficeIds(): void
    {
        $query = Office::active()->orderBy('sort_order');

        if (! in_array($this->role, [User::ROLE_SUPER_ADMIN, User::ROLE_GM_CEO], true)) {
            $query->whereIn('id', array_map('intval', $this->officeIds));
        }

        $this->officeIds = $query->pluck('id')->map(fn ($id) => (int) $id)->all();
    }

    private function officeSyncData(): array
    {
        return collect($this->officeIds)
            ->values()
            ->mapWithKeys(fn (int $officeId, int $index) => [
                $officeId => ['is_default' => $index === 0],
            ])
            ->all();
    }
}
