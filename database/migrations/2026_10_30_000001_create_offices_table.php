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
        Schema::create('offices', function (Blueprint $table) {
            $table->id();
            $table->string('code', 30)->unique();
            $table->string('name');
            $table->string('slug')->unique();
            $table->text('address')->nullable();
            $table->string('attendance_method', 30)->default(Office::ATTENDANCE_MACHINE);
            $table->boolean('is_active')->default(true);
            $table->unsignedSmallInteger('sort_order')->default(0);
            $table->timestamps();
        });

        $now = now();

        DB::table('offices')->insert([
            [
                'code' => Office::CODE_SUMMARECON_BANDUNG,
                'name' => 'Kantor Summarecon Bandung',
                'slug' => 'summarecon-bandung',
                'address' => null,
                'attendance_method' => Office::ATTENDANCE_MACHINE,
                'is_active' => true,
                'sort_order' => 1,
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'code' => Office::CODE_BALEENDAH,
                'name' => 'Kantor Baleendah',
                'slug' => 'baleendah',
                'address' => null,
                'attendance_method' => Office::ATTENDANCE_WEB_FACE,
                'is_active' => true,
                'sort_order' => 2,
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'code' => Office::CODE_JOHEN_STREAMER,
                'name' => 'Kantor Johen Streamer',
                'slug' => 'johen-streamer',
                'address' => null,
                'attendance_method' => Office::ATTENDANCE_WEB_FACE,
                'is_active' => true,
                'sort_order' => 3,
                'created_at' => $now,
                'updated_at' => $now,
            ],
        ]);
    }

    public function down(): void
    {
        Schema::dropIfExists('offices');
    }
};
