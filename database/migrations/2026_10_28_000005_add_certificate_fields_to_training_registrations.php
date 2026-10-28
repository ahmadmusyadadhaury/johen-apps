<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('training_registrations', function (Blueprint $table) {
            $table->string('certificate_path')->nullable()->after('mom_diisi_pada');
            $table->string('certificate_name')->nullable()->after('certificate_path');
            $table->string('certificate_mime', 120)->nullable()->after('certificate_name');
            $table->unsignedBigInteger('certificate_size')->nullable()->after('certificate_mime');
            $table->foreignId('certificate_issued_by')->nullable()->after('certificate_size')->constrained('users')->nullOnDelete();
            $table->timestamp('certificate_issued_at')->nullable()->after('certificate_issued_by');
        });
    }

    public function down(): void
    {
        Schema::table('training_registrations', function (Blueprint $table) {
            $table->dropConstrainedForeignId('certificate_issued_by');
            $table->dropColumn([
                'certificate_path',
                'certificate_name',
                'certificate_mime',
                'certificate_size',
                'certificate_issued_at',
            ]);
        });
    }
};
