<?php

namespace Database\Seeders;

use App\Models\Employee;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class AdminUserSeeder extends Seeder
{
    public function run(): void
    {
        $admin = User::firstOrCreate(
            ['username' => 'admin'],
            [
                'name' => 'Administrator',
                'email' => 'gonzaga@johen.com',
                'password' => Hash::make('password'),
                'role' => 'super_admin',
            ]
        );

        $gm = User::firstOrCreate(
            ['username' => 'gm'],
            [
                'name' => 'General Manager',
                'email' => 'gm@johen.com',
                'password' => Hash::make('password'),
                'role' => 'gm_ceo',
            ]
        );

        User::where('username', 'admin')->where('role', 'staff')->update(['role' => 'super_admin']);
        User::where('username', 'gm')->where('role', 'staff')->update(['role' => 'gm_ceo']);

        if (!$admin->employee) {
            $emp = Employee::create([
                'nik' => 'ADM001',
                'nama' => $admin->name,
                'email' => $admin->email,
                'tipe' => 'karyawan_aktif',
            ]);
            $admin->employee_id = $emp->id;
            $admin->save();
        }

        if (!$gm->employee) {
            $emp = Employee::create([
                'nik' => 'GM001',
                'nama' => $gm->name,
                'email' => $gm->email,
                'tipe' => 'karyawan_aktif',
            ]);
            $gm->employee_id = $emp->id;
            $gm->save();
        }
    }
}
