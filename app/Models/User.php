<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Hidden;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

#[Fillable(['name', 'username', 'email', 'password', 'role', 'pin_hash'])]
#[Hidden(['password', 'remember_token', 'pin_hash'])]
class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasApiTokens, HasFactory, Notifiable;

    public function employee(): BelongsTo
    {
        return $this->belongsTo(Employee::class, 'employee_id');
    }

    public function hasPin(): bool
    {
        return !is_null($this->pin_hash);
    }

    public function verifyPin(string $pin): bool
    {
        if (!$this->hasPin()) return false;
        return password_verify($pin, $this->pin_hash);
    }

    public function setPin(string $pin): void
    {
        $this->pin_hash = bcrypt($pin);
        $this->save();
    }

    public function requiresPinApproval(): bool
    {
        return in_array($this->role, [
            self::ROLE_KOORDINATOR,
            self::ROLE_MANAGER,
            self::ROLE_SUPER_ADMIN,
            self::ROLE_GM_CEO,
        ]);
    }

    public function meetings(): HasMany
    {
        return $this->hasMany(Meeting::class, 'created_by');
    }

    public function approvedRequests(): HasMany
    {
        return $this->hasMany(MeetingRequest::class, 'approved_by');
    }

    public const ROLE_SUPER_ADMIN = 'super_admin';
    public const ROLE_GM_CEO = 'gm_ceo';
    public const ROLE_MANAGER = 'manager';
    public const ROLE_KOORDINATOR = 'koordinator';
    public const ROLE_STAFF = 'staff';
    public const ROLE_KOORDINATOR_IT = 'koordinator_it';
    public const ROLE_STAFF_IT = 'staff_it';
    public const ROLE_KOORDINATOR_CREATIVE = 'koordinator_creative';
    public const ROLE_STAFF_CREATIVE = 'staff_creative';
    public const ROLE_STAFF_HOST_PUBG = 'staff_host_pubg';
    public const ROLE_STAFF_ADMIN = 'staff_admin';
    public const ROLE_KOORDINATOR_ADMIN = 'koordinator_admin';
    public const ROLE_KOORDINATOR_PUBG = 'koordinator_pubg';
    public const ROLE_KOORDINATOR_FF = 'koordinator_ff';
    public const ROLE_STAFF_HOST_FF = 'staff_host_ff';
    public const ROLE_STAFF_HOST_MLBB = 'staff_host_mlbb';
    public const ROLE_KOORDINATOR_MLBB = 'koordinator_mlbb';
    public const ROLE_KOORDINATOR_EFOOTBALL = 'koordinator_efootball';
    public const ROLE_STAFF_HOST_EFOOTBALL = 'staff_host_efootball';
    public const ROLE_KOORDINATOR_VALORANT = 'koordinator_valorant';
    public const ROLE_STAFF_HOST_VALORANT = 'staff_host_valorant';
    public const ROLE_KOORDINATOR_ROBLOX = 'koordinator_roblox';
    public const ROLE_STAFF_HOST_ROBLOX = 'staff_host_roblox';
    public const ROLE_KOORDINATOR_MONKEY_PUBG = 'koordinator_monkey_pubg';
    public const ROLE_STAFF_HOST_MONKEY_PUBG = 'staff_host_monkey_pubg';
    public const ROLE_KOORDINATOR_STOCK = 'koordinator_stock';
    public const ROLE_STAFF_STOCK = 'staff_stock';

    public function isSuperAdmin(): bool
    {
        return $this->role === self::ROLE_SUPER_ADMIN;
    }

    public function isGmCeo(): bool
    {
        return $this->role === self::ROLE_GM_CEO;
    }

    public function isManager(): bool
    {
        return $this->role === self::ROLE_MANAGER;
    }

    public function isKoordinator(): bool
    {
        return $this->role === self::ROLE_KOORDINATOR;
    }

    public function isStaff(): bool
    {
        return $this->role === self::ROLE_STAFF;
    }

    public function isKoordinatorIt(): bool
    {
        return $this->role === self::ROLE_KOORDINATOR_IT;
    }

    public function isStaffIt(): bool
    {
        return $this->role === self::ROLE_STAFF_IT;
    }

    public function isKoordinatorCreative(): bool
    {
        return $this->role === self::ROLE_KOORDINATOR_CREATIVE;
    }

    public function isStaffCreative(): bool
    {
        return $this->role === self::ROLE_STAFF_CREATIVE;
    }

    public function canSeeBiaya(): bool
    {
        if ($this->isKoordinatorCreative()) return true;
        if ($this->isStaffCreative() && $this->employee?->mainPosition()?->nama === 'Admin KOL') return true;
        return false;
    }

    public function isKoordinatorPubg(): bool
    {
        return $this->role === self::ROLE_KOORDINATOR_PUBG;
    }

    public function isKoordinatorFf(): bool
    {
        return $this->role === self::ROLE_KOORDINATOR_FF;
    }

    public function isKoordinatorMlbb(): bool
    {
        return $this->role === self::ROLE_KOORDINATOR_MLBB;
    }

    public function isKoordinatorEfootball(): bool
    {
        return $this->role === self::ROLE_KOORDINATOR_EFOOTBALL;
    }

    public function isKoordinatorValorant(): bool
    {
        return $this->role === self::ROLE_KOORDINATOR_VALORANT;
    }

    public function isKoordinatorRoblox(): bool
    {
        return $this->role === self::ROLE_KOORDINATOR_ROBLOX;
    }

    public function isKoordinatorMonkeyPubg(): bool
    {
        return $this->role === self::ROLE_KOORDINATOR_MONKEY_PUBG;
    }

    public function isKoordinatorGame(): bool
    {
        return in_array($this->role, [
            self::ROLE_KOORDINATOR_PUBG,
            self::ROLE_KOORDINATOR_FF,
            self::ROLE_KOORDINATOR_MLBB,
            self::ROLE_KOORDINATOR_EFOOTBALL,
            self::ROLE_KOORDINATOR_VALORANT,
            self::ROLE_KOORDINATOR_ROBLOX,
            self::ROLE_KOORDINATOR_MONKEY_PUBG,
        ]);
    }

    public function isAnyKoordinator(): bool
    {
        return in_array($this->role, [
            self::ROLE_KOORDINATOR,
            self::ROLE_KOORDINATOR_IT,
            self::ROLE_KOORDINATOR_CREATIVE,
            self::ROLE_KOORDINATOR_ADMIN,
            self::ROLE_KOORDINATOR_PUBG,
            self::ROLE_KOORDINATOR_FF,
            self::ROLE_KOORDINATOR_MLBB,
            self::ROLE_KOORDINATOR_EFOOTBALL,
            self::ROLE_KOORDINATOR_VALORANT,
            self::ROLE_KOORDINATOR_ROBLOX,
            self::ROLE_KOORDINATOR_MONKEY_PUBG,
            self::ROLE_KOORDINATOR_STOCK,
        ]);
    }

    public function isStaffHostFf(): bool
    {
        if ($this->role === self::ROLE_STAFF_HOST_FF) {
            return true;
        }

        if (!in_array($this->role, [self::ROLE_KOORDINATOR, self::ROLE_KOORDINATOR_PUBG])) {
            return false;
        }

        $employee = $this->employee;
        if (!$employee) return false;

        $root = Position::where('nama', 'Koordinator Free Fire')->first();
        if (!$root) return false;

        $descendantIds = $this->getAllDescendantIdsForPosition($root);
        $descendantIds[] = $root->id;

        return $employee->positions()->whereIn('position_id', $descendantIds)->exists();
    }

    public function isStaffHostPubg(): bool
    {
        if ($this->role === self::ROLE_STAFF_HOST_PUBG) {
            return true;
        }

        if (!in_array($this->role, [self::ROLE_KOORDINATOR, self::ROLE_KOORDINATOR_FF])) {
            return false;
        }

        $employee = $this->employee;
        if (!$employee) return false;

        $root = Position::where('nama', 'Koordinator Johen PUBG')->first();
        if (!$root) return false;

        $descendantIds = $this->getAllDescendantIdsForPosition($root);
        $descendantIds[] = $root->id;

        return $employee->positions()->whereIn('position_id', $descendantIds)->exists();
    }

    public function isStaffHostMlbb(): bool
    {
        if ($this->role === self::ROLE_STAFF_HOST_MLBB) {
            return true;
        }

        if ($this->role !== self::ROLE_KOORDINATOR) {
            return false;
        }

        $employee = $this->employee;
        if (!$employee) return false;

        $root = Position::where('nama', 'Koordinator MLBB')->first();
        if (!$root) return false;

        $descendantIds = $this->getAllDescendantIdsForPosition($root);
        $descendantIds[] = $root->id;

        return $employee->positions()->whereIn('position_id', $descendantIds)->exists();
    }

    public function isStaffHostEfootball(): bool
    {
        if ($this->role === self::ROLE_STAFF_HOST_EFOOTBALL) {
            return true;
        }

        if ($this->role !== self::ROLE_KOORDINATOR) {
            return false;
        }

        $employee = $this->employee;
        if (!$employee) return false;

        $root = Position::where('nama', 'Koordinator E-football')->first();
        if (!$root) return false;

        $descendantIds = $this->getAllDescendantIdsForPosition($root);
        $descendantIds[] = $root->id;

        return $employee->positions()->whereIn('position_id', $descendantIds)->exists();
    }

    public function isStaffHostValorant(): bool
    {
        if ($this->role === self::ROLE_STAFF_HOST_VALORANT) {
            return true;
        }

        if ($this->role !== self::ROLE_KOORDINATOR) {
            return false;
        }

        $employee = $this->employee;
        if (!$employee) return false;

        $root = Position::where('nama', 'Koordinator Valorant')->first();
        if (!$root) return false;

        $descendantIds = $this->getAllDescendantIdsForPosition($root);
        $descendantIds[] = $root->id;

        return $employee->positions()->whereIn('position_id', $descendantIds)->exists();
    }

    public function isStaffHostRoblox(): bool
    {
        if ($this->role === self::ROLE_STAFF_HOST_ROBLOX) {
            return true;
        }

        if ($this->role !== self::ROLE_KOORDINATOR) {
            return false;
        }

        $employee = $this->employee;
        if (!$employee) return false;

        $root = Position::where('nama', 'Koordinator Roblox')->first();
        if (!$root) return false;

        $descendantIds = $this->getAllDescendantIdsForPosition($root);
        $descendantIds[] = $root->id;

        return $employee->positions()->whereIn('position_id', $descendantIds)->exists();
    }

    public function isStaffHostMonkeyPubg(): bool
    {
        if ($this->role === self::ROLE_STAFF_HOST_MONKEY_PUBG) {
            return true;
        }

        if ($this->role !== self::ROLE_KOORDINATOR) {
            return false;
        }

        $employee = $this->employee;
        if (!$employee) return false;

        $root = Position::where('nama', 'Koordinator Monkey PUBG')->first();
        if (!$root) return false;

        $descendantIds = $this->getAllDescendantIdsForPosition($root);
        $descendantIds[] = $root->id;

        return $employee->positions()->whereIn('position_id', $descendantIds)->exists();
    }

    public function getAllDescendantIdsForPosition(Position $position): array
    {
        $ids = [];
        foreach ($position->children as $child) {
            $ids[] = $child->id;
            $ids = array_merge($ids, $this->getAllDescendantIdsForPosition($child));
        }
        return $ids;
    }

    public function isStaffAdmin(): bool
    {
        return $this->role === self::ROLE_STAFF_ADMIN;
    }

    public function isKoordinatorAdmin(): bool
    {
        return $this->role === self::ROLE_KOORDINATOR_ADMIN;
    }

    public function isKoordinatorStock(): bool
    {
        return $this->role === self::ROLE_KOORDINATOR_STOCK;
    }

    public function isStaffStock(): bool
    {
        return $this->role === self::ROLE_STAFF_STOCK;
    }

    public function isHeadOfStore(): bool
    {
        $employee = $this->employee;
        if (!$employee) return false;
        $position = $employee->mainPosition();
        if (!$position) return false;
        return str_contains(strtolower($position->nama), 'head of store');
    }

    public function roleLevel(): int
    {
        return match ($this->role) {
            self::ROLE_SUPER_ADMIN => 5,
            self::ROLE_GM_CEO => 4,
            self::ROLE_MANAGER => 3,
            self::ROLE_KOORDINATOR => 2,
            self::ROLE_STAFF => 1,
            self::ROLE_KOORDINATOR_IT => 1,
            self::ROLE_STAFF_IT => 1,
            self::ROLE_KOORDINATOR_CREATIVE => 1,
            self::ROLE_KOORDINATOR_ADMIN => 1,
            self::ROLE_KOORDINATOR_PUBG => 1,
            self::ROLE_STAFF_CREATIVE => 1,
            self::ROLE_STAFF_HOST_PUBG => 1,
            self::ROLE_STAFF_ADMIN => 1,
            self::ROLE_KOORDINATOR_FF => 1,
            self::ROLE_KOORDINATOR_MLBB => 1,
            self::ROLE_KOORDINATOR_EFOOTBALL => 1,
            self::ROLE_KOORDINATOR_VALORANT => 1,
            self::ROLE_STAFF_HOST_FF => 1,
            self::ROLE_STAFF_HOST_MLBB => 1,
            self::ROLE_STAFF_HOST_EFOOTBALL => 1,
            self::ROLE_STAFF_HOST_VALORANT => 1,
            self::ROLE_KOORDINATOR_ROBLOX => 1,
            self::ROLE_STAFF_HOST_ROBLOX => 1,
            self::ROLE_KOORDINATOR_MONKEY_PUBG => 1,
            self::ROLE_STAFF_HOST_MONKEY_PUBG => 1,
            self::ROLE_KOORDINATOR_STOCK => 1,
            self::ROLE_STAFF_STOCK => 1,
            default => 0,
        };
    }

    /** @deprecated Use isSuperAdmin() instead */
    public function isAdmin(): bool
    {
        return $this->isSuperAdmin();
    }

    /** @deprecated Use isGmCeo() instead */
    public function isDireksi(): bool
    {
        return $this->isGmCeo();
    }

    /** @deprecated Use isStaff() instead */
    public function isKaryawan(): bool
    {
        return $this->isStaff();
    }

    public function getRoleDisplayName(): ?string
    {
        return match (true) {
            $this->isKoordinatorPubg() => 'Koordinator Johen PUBG',
            $this->isKoordinatorFf() => 'Koordinator Free Fire',
            $this->isKoordinatorMlbb() => 'Koordinator MLBB',
            $this->isKoordinatorEfootball() => 'Koordinator E-football',
            $this->isKoordinatorValorant() => 'Koordinator Valorant',
            $this->isKoordinatorRoblox() => 'Koordinator Roblox',
            $this->isKoordinatorMonkeyPubg() => 'Koordinator Monkey PUBG',
            $this->isKoordinatorIt() => 'Koordinator IT',
            $this->isKoordinatorCreative() => 'Koordinator Creative',
            $this->isKoordinatorAdmin() => 'Koordinator Admin',
            $this->isKoordinatorStock() => 'Koordinator Stock',
            $this->isStaffHostPubg() => 'Staff Host PUBG',
            $this->isStaffHostFf() => 'Staff Host Free Fire',
            $this->isStaffHostMlbb() => 'Staff Host MLBB',
            $this->isStaffHostEfootball() => 'Staff Host E-football',
            $this->isStaffHostValorant() => 'Staff Host Valorant',
            $this->isStaffHostRoblox() => 'Staff Host Roblox',
            $this->isStaffHostMonkeyPubg() => 'Staff Host Monkey PUBG',
            $this->isStaffIt() => 'Staff IT',
            $this->isStaffCreative() => 'Staff Creative',
            $this->isStaffAdmin() => 'Staff Admin',
            $this->isStaffStock() => 'Staff Stock',
            $this->isManager() => 'Manager',
            $this->isKoordinator() => 'Koordinator',
            $this->isStaff() => 'Staff',
            $this->isSuperAdmin() => 'Super Admin',
            $this->isGmCeo() => 'GM / CEO',
            default => $this->employee?->position,
        };
    }

    public function getRoleDivisionName(): ?string
    {
        return match (true) {
            $this->isKoordinatorPubg(), $this->isStaffHostPubg() => 'PUBG',
            $this->isKoordinatorFf(), $this->isStaffHostFf() => 'Free Fire',
            $this->isKoordinatorMlbb(), $this->isStaffHostMlbb() => 'MLBB',
            $this->isKoordinatorEfootball(), $this->isStaffHostEfootball() => 'E-football',
            $this->isKoordinatorValorant(), $this->isStaffHostValorant() => 'Valorant',
            $this->isKoordinatorRoblox(), $this->isStaffHostRoblox() => 'Roblox',
            $this->isKoordinatorMonkeyPubg(), $this->isStaffHostMonkeyPubg() => 'Monkey PUBG',
            $this->isKoordinatorAdmin(), $this->isStaffAdmin() => 'Admin',
            $this->isKoordinatorIt(), $this->isStaffIt() => 'IT',
            $this->isKoordinatorCreative(), $this->isStaffCreative() => 'Creative',
            $this->isKoordinatorStock(), $this->isStaffStock() => 'Stock',
            default => $this->employee?->division?->nama,
        };
    }

    public function canCreateData(): bool
    {
        return in_array($this->role, [self::ROLE_SUPER_ADMIN, self::ROLE_GM_CEO]);
    }

    public function canUpdateData(): bool
    {
        return in_array($this->role, [self::ROLE_SUPER_ADMIN, self::ROLE_GM_CEO, self::ROLE_MANAGER]);
    }

    public function canDeleteData(): bool
    {
        return in_array($this->role, [self::ROLE_SUPER_ADMIN, self::ROLE_GM_CEO]);
    }

    public function canViewAll(): bool
    {
        return in_array($this->role, [self::ROLE_SUPER_ADMIN, self::ROLE_GM_CEO, self::ROLE_MANAGER, self::ROLE_KOORDINATOR]);
    }

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'pin_hash' => 'hashed',
        ];
    }
}
