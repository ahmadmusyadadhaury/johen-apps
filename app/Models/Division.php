<?php

namespace App\Models;

use App\Models\Concerns\HasOfficeOwnership;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Division extends Model
{
    use HasOfficeOwnership;

    protected $fillable = [
        'office_id',
        'nama',
        'koordinator',
        'deskripsi',
        'is_active',
    ];

    protected function casts(): array
    {
        return [
            'is_active' => 'boolean',
        ];
    }

    public function employees(): BelongsToMany
    {
        return $this->belongsToMany(Employee::class, 'employee_division')
            ->withTimestamps();
    }
}
