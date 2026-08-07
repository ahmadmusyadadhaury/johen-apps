<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('digital_asset_registries', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('source_id')->nullable()->unique();
            $table->string('nama_aset');
            $table->string('email')->nullable();
            $table->date('mulai')->nullable();
            $table->date('berakhir')->nullable();
            $table->decimal('biaya', 15, 2)->default(0);
            $table->string('pic')->nullable();
            $table->string('jabatan')->nullable();
            $table->text('keperluan')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('digital_asset_registries');
    }
};
