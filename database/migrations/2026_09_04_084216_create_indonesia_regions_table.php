<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('indonesia_regions', function (Blueprint $table) {
            $table->string('id', 20)->primary();
            $table->string('parent_id', 20)->nullable()->index();
            $table->string('type', 20)->index();
            $table->string('name', 150);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('indonesia_regions');
    }
};
