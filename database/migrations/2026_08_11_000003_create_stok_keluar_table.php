<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('stok_keluar', function (Blueprint $table) {
            $table->id();
            $table->foreignId('item_id')->constrained('stok_items')->cascadeOnDelete();
            $table->date('tanggal');
            $table->integer('jumlah')->unsigned();
            $table->string('tujuan')->nullable();
            $table->string('keterangan')->nullable();
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();

            $table->index('item_id');
            $table->index('tanggal');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('stok_keluar');
    }
};
