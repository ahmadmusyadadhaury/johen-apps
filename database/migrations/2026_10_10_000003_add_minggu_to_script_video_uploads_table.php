<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('script_video_uploads', function (Blueprint $table) {
            $table->unsignedTinyInteger('minggu')->nullable();
        });

        $uploads = DB::table('script_video_uploads')->get();

        foreach ($uploads as $upload) {
            $hari = (int) \Illuminate\Support\Carbon::parse($upload->created_at)->format('j');
            DB::table('script_video_uploads')
                ->where('id', $upload->id)
                ->update(['minggu' => min((int) ceil($hari / 7), 4)]);
        }

        DB::table('script_video_targets')->where('target', 70)->update(['target' => 75]);
    }

    public function down(): void
    {
        Schema::table('script_video_uploads', function (Blueprint $table) {
            $table->dropColumn('minggu');
        });
    }
};