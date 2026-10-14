<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('user_face_templates', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->unique()->constrained()->cascadeOnDelete();
            $table->longText('encrypted_template');
            $table->string('model_version', 40)->default('human-faceres');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('user_face_templates');
    }
};
