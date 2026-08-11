<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('stok_masuk', function (Blueprint $table) {
            $table->foreignId('item_id')->nullable()->change();
            $table->foreignId('division_id')->nullable()->after('item_id')->constrained('divisions')->nullOnDelete();
            $table->string('nomor')->nullable()->after('division_id');
            $table->string('id_game')->nullable()->after('nomor');
            $table->string('spek')->nullable()->after('id_game');
            $table->string('zimbra')->nullable()->after('sumber');
        });
    }

    public function down(): void
    {
        Schema::table('stok_masuk', function (Blueprint $table) {
            $table->dropConstrainedForeignId('division_id');
            $table->dropColumn(['nomor', 'id_game', 'spek', 'zimbra']);
        });
    }
};
