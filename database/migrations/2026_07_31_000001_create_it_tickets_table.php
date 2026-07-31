<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('it_tickets', function (Blueprint $table) {
            $table->id();
            $table->string('kode', 30)->unique();
            $table->foreignId('requester_id')->constrained('users')->cascadeOnDelete();
            $table->foreignId('assignee_id')->nullable()->constrained('users')->nullOnDelete();
            $table->string('judul');
            $table->text('deskripsi');
            $table->enum('kategori', ['perangkat', 'aplikasi', 'akun_akses', 'jaringan', 'lainnya'])->default('lainnya');
            $table->enum('prioritas', ['rendah', 'sedang', 'tinggi', 'mendesak'])->default('sedang');
            $table->enum('status', ['baru', 'diproses', 'menunggu_konfirmasi', 'selesai', 'ditolak'])->default('baru');
            $table->text('catatan_it')->nullable();
            $table->timestamp('mulai_ditangani_at')->nullable();
            $table->timestamp('selesai_at')->nullable();
            $table->timestamps();

            $table->index(['status', 'assignee_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('it_tickets');
    }
};
