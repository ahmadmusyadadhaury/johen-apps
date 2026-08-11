<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MachineUser extends Model
{
    public $incrementing = false;

    protected $primaryKey = 'machine_user_id';

    protected $keyType = 'string';

    protected $fillable = [
        'machine_user_id',
        'name',
        'role',
        'last_seen_at',
    ];
}
