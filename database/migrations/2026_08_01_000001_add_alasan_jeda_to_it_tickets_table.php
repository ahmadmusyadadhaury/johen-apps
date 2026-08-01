<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('it_tickets', function (Blueprint $table) {
            $table->text('alasan_jeda')->nullable()->after('catatan_it');
        });
    }

    public function down(): void
    {
        Schema::table('it_tickets', function (Blueprint $table) {
            $table->dropColumn('alasan_jeda');
        });
    }
};
