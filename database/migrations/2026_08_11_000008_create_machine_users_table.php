<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('machine_users', function (Blueprint $table) {
            $table->string('machine_user_id', 20)->primary();
            $table->string('name')->nullable();
            $table->unsignedSmallInteger('role')->nullable();
            $table->timestamp('last_seen_at')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('machine_users');
    }
};
