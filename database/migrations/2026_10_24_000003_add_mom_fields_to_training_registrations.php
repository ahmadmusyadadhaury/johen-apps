<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('training_registrations', function (Blueprint $table) {
            $table->longText('mom')->nullable()->after('dihadiri_pada');
            $table->timestamp('mom_diisi_pada')->nullable()->after('mom');
        });
    }

    public function down(): void
    {
        Schema::table('training_registrations', function (Blueprint $table) {
            $table->dropColumn(['mom', 'mom_diisi_pada']);
        });
    }
};
