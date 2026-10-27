<?php

namespace App\Livewire;

use App\Facades\FileVault;
use App\Models\Employee;
use App\Models\Position;
use App\Models\TrainingRegistration;
use App\Models\TrainingSession;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;
use Livewire\Component;
use Livewire\WithFileUploads;

class TrainingWorkspace extends Component
{
    use WithFileUploads;

    protected $queryString = ['tab'];

    public string $tab = 'jadwal';

    public bool $showForm = false;

    public ?int $editId = null;

    public ?int $selectedSessionId = null;

    public ?int $deletingSessionId = null;

    public ?int $momRegistrationId = null;

    public string $momText = '';

    public string $judul = '';

    public string $jenis = 'pelatihan';

    public string $deskripsi = '';

    public string $mulaiPada = '';

    public string $selesaiPada = '';

    public string $lokasi = '';

    public string $tautan = '';

    public $thumbnail = null;

    public ?string $existingThumbnailUrl = null;

    public string $kuota = '';

    public bool $isPublished = true;

    public function mount(): void
    {
        $user = auth()->user();
        abort_unless($user && ($user->canJoinTraining() || $user->canManageTraining() || $user->canViewTrainingTeam()), 403);

        $requestedTab = request()->query('tab');
        if (in_array($requestedTab, ['jadwal', 'saya', 'tim', 'riwayat', 'kelola'], true)) {
            $this->tab = $requestedTab;
        }
        $this->normalizeTab();
    }

    public function setTab(string $tab): void
    {
        if (! in_array($tab, ['jadwal', 'saya', 'tim', 'riwayat'], true)) {
            return;
        }
        if ($tab === 'saya' && ! auth()->user()->canJoinTraining()) {
            abort(403);
        }
        if ($tab === 'tim' && ! auth()->user()->canViewTrainingTeam()) {
            abort(403);
        }
        if ($tab === 'riwayat' && ! auth()->user()->canManageTraining()) {
            abort(403);
        }
        if ($tab === 'kelola' && ! auth()->user()->canManageTraining()) {
            abort(403);
        }

        $this->tab = $tab;
        $this->selectedSessionId = null;
    }

    public function openCreate(): void
    {
        $this->authorizeManager();
        $this->resetForm();
        $this->mulaiPada = now()->addDay()->startOfHour()->format('Y-m-d\TH:i');
        $this->selesaiPada = now()->addDay()->addHour()->startOfHour()->format('Y-m-d\TH:i');
        $this->showForm = true;
    }

    public function edit(int $id): void
    {
        $session = TrainingSession::findOrFail($id);
        $this->authorizeSessionEditor($session);
        $this->resetValidation();
        $this->thumbnail = null;
        $this->editId = $session->id;
        $this->judul = $session->judul;
        $this->jenis = $session->jenis;
        $this->deskripsi = $session->deskripsi ?? '';
        $this->mulaiPada = $session->mulai_pada->format('Y-m-d\TH:i');
        $this->selesaiPada = $session->selesai_pada->format('Y-m-d\TH:i');
        $this->lokasi = $session->lokasi ?? '';
        $this->tautan = $session->tautan ?? '';
        $this->existingThumbnailUrl = $session->thumbnail
            ? route('hris.training.thumbnail', $session->id)
            : null;
        $this->kuota = $session->kuota ? (string) $session->kuota : '';
        $this->isPublished = $session->is_published;
        $this->showForm = true;
    }

    public function save(): void
    {
        $sessionToUpdate = $this->editId ? TrainingSession::findOrFail($this->editId) : null;
        if ($sessionToUpdate) {
            $this->authorizeSessionEditor($sessionToUpdate);
        } else {
            $this->authorizeManager();
        }
        $validated = $this->validate([
            'judul' => ['required', 'string', 'max:255'],
            'jenis' => ['required', 'in:seminar,workshop,pelatihan'],
            'deskripsi' => ['nullable', 'string', 'max:10000'],
            'mulaiPada' => ['required', 'date'],
            'selesaiPada' => ['required', 'date', 'after:mulaiPada'],
            'lokasi' => ['nullable', 'string', 'max:255'],
            'tautan' => ['nullable', 'url', 'max:2048'],
            'thumbnail' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:4096'],
            'kuota' => ['nullable', 'integer', 'min:1', 'max:100000'],
            'isPublished' => ['boolean'],
        ]);

        $data = [
            'judul' => $validated['judul'],
            'jenis' => $validated['jenis'],
            'deskripsi' => $validated['deskripsi'] ?: null,
            'mulai_pada' => $validated['mulaiPada'],
            'selesai_pada' => $validated['selesaiPada'],
            'lokasi' => $validated['lokasi'] ?: null,
            'tautan' => $validated['tautan'] ?: null,
            'kuota' => $validated['kuota'] ?: null,
            'is_published' => $this->isPublished,
        ];

        $oldThumbnailPath = $sessionToUpdate?->thumbnail;
        if ($this->thumbnail) {
            $newThumbnailPath = FileVault::store($this->thumbnail, 'training/thumbnails', 'public');
            $data['thumbnail'] = $newThumbnailPath;
        }

        if ($this->editId) {
            $sessionToUpdate->update($data);
            $message = 'Jadwal pelatihan berhasil diperbarui.';
        } else {
            $data['dibuat_oleh'] = auth()->id();
            TrainingSession::create($data);
            $message = 'Jadwal pelatihan berhasil dibuat.';
        }

        if ($this->thumbnail && $oldThumbnailPath) {
            FileVault::delete('public', $oldThumbnailPath);
        }

        $this->resetForm();
        $this->showForm = false;
        $this->dispatch('notify', type: 'success', message: $message);
    }

    public function cancelForm(): void
    {
        $this->resetForm();
        $this->showForm = false;
    }

    public function register(int $id): void
    {
        $user = auth()->user();
        abort_unless($user->canJoinTraining(), 403);

        $employee = $user->employee;
        if (! $employee) {
            throw ValidationException::withMessages(['registration' => 'Akun Anda belum terhubung ke data pegawai. Hubungi HR.']);
        }

        DB::transaction(function () use ($id, $employee) {
            $session = TrainingSession::query()->whereKey($id)->lockForUpdate()->firstOrFail();
            if (! $session->is_published) {
                throw ValidationException::withMessages(['registration' => 'Pendaftaran untuk kegiatan ini belum dibuka.']);
            }
            if (now()->greaterThan($session->selesai_pada)) {
                throw ValidationException::withMessages(['registration' => 'Pendaftaran kegiatan ini sudah ditutup.']);
            }
            if ($session->registrations()->where('employee_id', $employee->id)->exists()) {
                throw ValidationException::withMessages(['registration' => 'Anda sudah terdaftar pada kegiatan ini.']);
            }
            if ($session->kuota !== null && $session->registrations()->count() >= $session->kuota) {
                throw ValidationException::withMessages(['registration' => 'Kuota kegiatan sudah penuh.']);
            }

            $session->registrations()->create([
                'employee_id' => $employee->id,
                'status' => 'registered',
                'terdaftar_pada' => now(),
            ]);
        });

        $this->dispatch('notify', type: 'success', message: 'Pendaftaran berhasil. Kegiatan akan tercatat di Pelatihan Saya.');
    }

    public function selectSession(int $id): void
    {
        $this->authorizeManager();
        $this->selectedSessionId = TrainingSession::query()->whereKey($id)->value('id');
    }

    public function confirmDelete(int $id): void
    {
        $this->authorizeManager();
        $this->deletingSessionId = TrainingSession::query()->whereKey($id)->value('id');
    }

    public function deleteSession(): void
    {
        $this->authorizeManager();
        if (! $this->deletingSessionId) {
            return;
        }

        $sessionId = $this->deletingSessionId;
        $session = TrainingSession::findOrFail($sessionId);
        $thumbnailPath = $session->thumbnail;
        $session->delete();
        if ($thumbnailPath) {
            FileVault::delete('public', $thumbnailPath);
        }
        $this->deletingSessionId = null;
        if ($this->selectedSessionId === $sessionId) {
            $this->selectedSessionId = null;
        }
        $this->dispatch('notify', type: 'success', message: 'Kegiatan berhasil dihapus.');
    }

    public function markAttendance(int $registrationId, string $status): void
    {
        $this->authorizeManager();
        if (! in_array($status, ['registered', 'attended', 'absent'], true)) {
            abort(422);
        }

        $registration = TrainingRegistration::findOrFail($registrationId);
        $registration->update([
            'status' => $status,
            'dihadiri_pada' => $status === 'attended' ? now() : null,
            'dicatat_oleh' => auth()->id(),
        ]);
    }

    public function openMom(int $registrationId): void
    {
        abort_unless(auth()->user()?->canJoinTraining(), 403);
        $employeeId = auth()->user()->employee?->id;
        abort_unless($employeeId, 403);

        $registration = TrainingRegistration::query()
            ->with('session')
            ->where('employee_id', $employeeId)
            ->where('status', 'attended')
            ->findOrFail($registrationId);

        abort_unless($registration->session && now()->greaterThanOrEqualTo($registration->session->selesai_pada), 403);

        $this->momRegistrationId = $registration->id;
        $this->momText = $registration->mom ?? '';
        $this->resetValidation('momText');
    }

    public function saveMom(): void
    {
        abort_unless(auth()->user()?->canJoinTraining(), 403);
        $employeeId = auth()->user()->employee?->id;
        abort_unless($employeeId && $this->momRegistrationId, 403);

        $registration = TrainingRegistration::query()
            ->with('session')
            ->where('employee_id', $employeeId)
            ->where('status', 'attended')
            ->findOrFail($this->momRegistrationId);
        abort_unless($registration->session && now()->greaterThanOrEqualTo($registration->session->selesai_pada), 403);

        $validated = $this->validate([
            'momText' => ['required', 'string', 'max:20000'],
        ], [
            'momText.required' => 'MOM wajib diisi sebelum disimpan.',
            'momText.max' => 'MOM maksimal 20.000 karakter.',
        ]);

        $registration->update([
            'mom' => $validated['momText'],
            'mom_diisi_pada' => now(),
        ]);

        $this->momRegistrationId = null;
        $this->momText = '';
        $this->dispatch('notify', type: 'success', message: 'MOM pelatihan berhasil disimpan.');
    }

    public function closeMom(): void
    {
        $this->momRegistrationId = null;
        $this->momText = '';
        $this->resetValidation('momText');
    }

    private function authorizeManager(): void
    {
        abort_unless(auth()->user()?->canManageTraining(), 403);
    }

    private function authorizeSessionEditor(TrainingSession $session): void
    {
        $user = auth()->user();
        abort_unless($user && ($user->canManageTraining() || (int) $session->dibuat_oleh === (int) $user->id), 403);
    }

    private function normalizeTab(): void
    {
        $user = auth()->user();
        if ($this->tab === 'saya' && ! $user->canJoinTraining()) {
            $this->tab = 'jadwal';
        } elseif ($this->tab === 'tim' && ! $user->canViewTrainingTeam()) {
            $this->tab = 'jadwal';
        } elseif ($this->tab === 'riwayat' && ! $user->canManageTraining()) {
            $this->tab = 'jadwal';
        } elseif ($this->tab === 'kelola') {
            $this->tab = 'jadwal';
        }
    }

    private function resetForm(): void
    {
        $this->resetValidation();
        $this->editId = null;
        $this->judul = '';
        $this->jenis = 'pelatihan';
        $this->deskripsi = '';
        $this->mulaiPada = '';
        $this->selesaiPada = '';
        $this->lokasi = '';
        $this->tautan = '';
        $this->thumbnail = null;
        $this->existingThumbnailUrl = null;
        $this->kuota = '';
        $this->isPublished = true;
    }

    private function teamEmployeeIds(): array
    {
        $employee = auth()->user()->employee;
        $root = $employee?->mainPosition();
        if (! $employee || ! $root) {
            return [];
        }

        $positionIds = [];
        $frontier = [$root->id];
        while ($frontier !== []) {
            $children = Position::query()->whereIn('parent_id', $frontier)->pluck('id')->all();
            $children = array_values(array_diff($children, $positionIds, [$root->id]));
            if ($children === []) {
                break;
            }
            $positionIds = array_merge($positionIds, $children);
            $frontier = $children;
        }

        if ($positionIds === []) {
            return [];
        }

        return Employee::query()
            ->where('tipe', Employee::TIPE_KARYAWAN_AKTIF)
            ->whereHas('positions', fn ($query) => $query
                ->whereIn('position_id', $positionIds)
                ->where('employee_position.is_main', true))
            ->pluck('id')
            ->all();
    }

    public function render()
    {
        $user = auth()->user();
        $sessions = TrainingSession::query()
            ->with(['registrations' => fn ($query) => $query->where('employee_id', $user->employee?->id)])
            ->withCount('registrations')
            ->when(! $user->canManageTraining(), fn ($query) => $query->where('is_published', true))
            ->orderByDesc('mulai_pada')
            ->paginate(10, ['*'], 'training-page');

        $trainingHistory = $user->canManageTraining()
            ? TrainingSession::query()
                ->withCount('registrations')
                ->where('selesai_pada', '<=', now())
                ->orderByDesc('selesai_pada')
                ->orderByDesc('id')
                ->paginate(15, ['*'], 'training-history-page')
            : null;

        $myRegistrations = collect();
        if ($user->canJoinTraining() && $user->employee) {
            $myRegistrations = TrainingRegistration::query()
                ->with('session')
                ->where('employee_id', $user->employee->id)
                ->where('status', 'attended')
                ->latest('dihadiri_pada')
                ->get();
        }

        $teamRegistrations = collect();
        if ($user->canViewTrainingTeam()) {
            $teamIds = $this->teamEmployeeIds();
            $teamRegistrations = TrainingRegistration::query()
                ->with(['session', 'employee'])
                ->whereIn('employee_id', $teamIds)
                ->latest('terdaftar_pada')
                ->paginate(15, ['*'], 'team-page');
        }

        $selectedSession = null;
        if ($user->canManageTraining() && $this->selectedSessionId) {
            $selectedSession = TrainingSession::with(['registrations.employee'])
                ->find($this->selectedSessionId);
        }

        return view('livewire.training-workspace', [
            'sessions' => $sessions,
            'trainingHistory' => $trainingHistory,
            'myRegistrations' => $myRegistrations,
            'teamRegistrations' => $teamRegistrations,
            'selectedSession' => $selectedSession,
            'canManage' => $user->canManageTraining(),
            'canJoin' => $user->canJoinTraining(),
            'canViewTeam' => $user->canViewTrainingTeam(),
            'employeeLinked' => (bool) $user->employee,
        ]);
    }
}
