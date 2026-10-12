<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        $driver = Schema::getConnection()->getDriverName();

        Schema::create('stored_files', function (Blueprint $table) use ($driver) {
            $table->id();
            $table->string('disk', 30);
            $table->string('path', 500);
            $table->string('original_name')->nullable();
            $table->string('mime_type', 150)->nullable();
            $table->unsignedBigInteger('size')->default(0);
            $table->string('checksum', 64)->nullable();
            $table->timestamps();

            $table->unique(['disk', 'path']);
            $table->index('checksum');

            if ($driver === 'mysql') {
                $table->binary('content');
            } else {
                $table->binary('content');
            }
        });

        if ($driver === 'mysql') {
            DB::statement('ALTER TABLE stored_files MODIFY content LONGBLOB NOT NULL');
        } else {
            // keep as binary
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('stored_files');
    }
};
