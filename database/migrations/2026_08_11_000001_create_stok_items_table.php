<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('stok_items', function (Blueprint $table) {
            $table->id();
            $table->string('nama');
            $table->string('satuan')->nullable();
            $table->foreignId('division_id')->nullable()->constrained('divisions')->nullOnDelete();
            $table->integer('target_stok')->default(0);
            $table->boolean('is_active')->default(true);
            $table->timestamps();

            $table->index('division_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('stok_items');
    }
};
