<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('stok_target', function (Blueprint $table) {
            $table->id();
            $table->foreignId('division_id')->nullable()->constrained('divisions')->nullOnDelete();
            $table->string('nama_akun');
            $table->integer('stok_harian')->default(0);
            $table->integer('stok_mingguan')->default(0);
            $table->integer('stok_bulanan')->default(0);
            $table->string('status')->default('kosong');
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();

            $table->index('division_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('stok_target');
    }
};
