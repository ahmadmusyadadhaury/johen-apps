<?php

use App\Models\Office;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('employees', function (Blueprint $table) {
            $table->foreignId('office_id')->nullable()->after('id')->constrained()->restrictOnDelete();
        });

        Schema::table('divisions', function (Blueprint $table) {
            $table->foreignId('office_id')->nullable()->after('id')->constrained()->restrictOnDelete();
        });

        Schema::table('positions', function (Blueprint $table) {
            $table->foreignId('office_id')->nullable()->after('id')->constrained()->restrictOnDelete();
        });

        $summareconId = DB::table('offices')
            ->where('code', Office::CODE_SUMMARECON_BANDUNG)
            ->value('id');

        if ($summareconId) {
            DB::table('employees')->whereNull('office_id')->update(['office_id' => $summareconId]);
            DB::table('divisions')->whereNull('office_id')->update(['office_id' => $summareconId]);
            DB::table('positions')->whereNull('office_id')->update(['office_id' => $summareconId]);
        }
    }

    public function down(): void
    {
        Schema::table('positions', function (Blueprint $table) {
            $table->dropConstrainedForeignId('office_id');
        });

        Schema::table('divisions', function (Blueprint $table) {
            $table->dropConstrainedForeignId('office_id');
        });

        Schema::table('employees', function (Blueprint $table) {
            $table->dropConstrainedForeignId('office_id');
        });
    }
};
