<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('stok_ketersediaan', function (Blueprint $table) {
            $table->id();
            $table->foreignId('division_id')->nullable()->constrained('divisions')->nullOnDelete();
            $table->date('tanggal');
            $table->integer('stok_hari_ini')->unsigned()->default(0);
            $table->integer('stok_sebelum')->nullable();
            $table->integer('stok_setelah')->nullable();
            $table->integer('jumlah_stok')->nullable();
            $table->string('status')->default('kosong');
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();

            $table->index('division_id');
            $table->index('tanggal');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('stok_ketersediaan');
    }
};
