<?php

namespace App\Support;

use App\Models\Office;
use App\Models\User;
use Illuminate\Database\Eloquent\Collection;

class OfficeContext
{
    public const SESSION_KEY = 'active_office_id';

    public const ALL_OFFICES = 'all';

    private bool $resolved = false;

    private bool $allOffices = false;

    private ?Office $office = null;

    /** @var Collection<int, Office> */
    private Collection $availableOffices;

    public function __construct()
    {
        $this->availableOffices = new Collection;
    }

    public function resolve(User $user): void
    {
        if ($this->resolved) {
            return;
        }

        $this->availableOffices = $user->hasGlobalOfficeAccess()
            ? Office::active()->orderBy('sort_order')->get()
            : $user->offices()->where('offices.is_active', true)->orderBy('sort_order')->get();

        $selection = session(self::SESSION_KEY);

        if ($user->hasGlobalOfficeAccess() && ($selection === null || $selection === self::ALL_OFFICES)) {
            $this->useAllOffices();

            return;
        }

        $office = $this->availableOffices->firstWhere('id', (int) $selection);

        if ($office) {
            $this->office = $office;
            $this->allOffices = false;
            $this->resolved = true;

            return;
        }

        if ($user->hasGlobalOfficeAccess()) {
            $this->useAllOffices();

            return;
        }

        $this->office = $this->availableOffices->first();
        $this->allOffices = false;
        $this->resolved = true;

        if ($this->office) {
            session([self::SESSION_KEY => (string) $this->office->id]);
        } else {
            session()->forget(self::SESSION_KEY);
        }
    }

    public function select(User $user, string $selection): bool
    {
        if ($selection === self::ALL_OFFICES) {
            if (! $user->hasGlobalOfficeAccess()) {
                return false;
            }

            session([self::SESSION_KEY => self::ALL_OFFICES]);
            $this->reset();
            $this->resolve($user);

            return true;
        }

        if (! ctype_digit($selection)) {
            return false;
        }

        $office = Office::active()->find((int) $selection);

        if (! $office || ! $user->canAccessOffice($office)) {
            return false;
        }

        session([self::SESSION_KEY => (string) $office->id]);
        $this->reset();
        $this->resolve($user);

        return true;
    }

    public function isAllOffices(): bool
    {
        return $this->allOffices;
    }

    public function office(): ?Office
    {
        return $this->office;
    }

    /** @return Collection<int, Office> */
    public function availableOffices(): Collection
    {
        return $this->availableOffices;
    }

    public function currentSelection(): string
    {
        return $this->allOffices ? self::ALL_OFFICES : (string) ($this->office?->id ?? '');
    }

    public function label(): string
    {
        if ($this->allOffices) {
            return 'Semua Kantor';
        }

        return $this->office?->name ?? 'Belum Ada Kantor';
    }

    private function useAllOffices(): void
    {
        $this->office = null;
        $this->allOffices = true;
        $this->resolved = true;
        session([self::SESSION_KEY => self::ALL_OFFICES]);
    }

    private function reset(): void
    {
        $this->resolved = false;
        $this->allOffices = false;
        $this->office = null;
        $this->availableOffices = new Collection;
    }
}
