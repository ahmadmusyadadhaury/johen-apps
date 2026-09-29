<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // Pakai query builder, bukan model User: model memakai SoftDeletes,
        // sementara kolom users.deleted_at baru dibuat pada migrasi yang
        // dijalankan setelah migrasi ini. Pakai model akan menambah
        // klausa "deleted_at is null" pada kolom yang belum ada.
        DB::table('users')->where('role', 'admin')->update(['role' => 'super_admin']);
        DB::table('users')->where('role', 'direksi')->update(['role' => 'gm_ceo']);
        DB::table('users')->where('role', 'karyawan')->update(['role' => 'staff']);
    }

    public function down(): void
    {
        DB::table('users')->where('role', 'super_admin')->update(['role' => 'admin']);
        DB::table('users')->where('role', 'gm_ceo')->update(['role' => 'direksi']);
        DB::table('users')->where('role', 'manager')->update(['role' => 'direksi']);
        DB::table('users')->where('role', 'koordinator')->update(['role' => 'karyawan']);
        DB::table('users')->where('role', 'staff')->update(['role' => 'karyawan']);
    }
};
