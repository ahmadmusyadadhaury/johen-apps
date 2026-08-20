<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('pengarsipans', function (Blueprint $table) {
            $table->id();
            $table->string('jenis');
            $table->string('nomor')->nullable();
            $table->string('judul');
            $table->date('tanggal_surat');
            $table->string('file');
            $table->text('keterangan')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pengarsipans');
    }
};
