<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('influencer_pengajuans', function (Blueprint $table) {
            $table->foreignId('approved_coordinator_by')->nullable()->after('approved_hos1_at')->constrained('users');
            $table->timestamp('approved_coordinator_at')->nullable()->after('approved_coordinator_by');
            $table->text('keterangan')->nullable()->after('biaya');
        });
    }

    public function down(): void
    {
        Schema::table('influencer_pengajuans', function (Blueprint $table) {
            $table->dropConstrainedForeignId('approved_coordinator_by');
            $table->dropColumn(['approved_coordinator_at', 'keterangan']);
        });
    }
};
