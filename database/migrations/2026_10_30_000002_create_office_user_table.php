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
        Schema::create('office_user', function (Blueprint $table) {
            $table->id();
            $table->foreignId('office_id')->constrained()->cascadeOnDelete();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->boolean('is_default')->default(false);
            $table->timestamps();

            $table->unique(['office_id', 'user_id']);
            $table->index(['user_id', 'is_default']);
        });

        $summareconId = DB::table('offices')
            ->where('code', Office::CODE_SUMMARECON_BANDUNG)
            ->value('id');

        if (! $summareconId) {
            return;
        }

        DB::table('users')
            ->whereNull('deleted_at')
            ->orderBy('id')
            ->chunkById(500, function ($users) use ($summareconId): void {
                $now = now();
                $rows = $users->map(fn ($user) => [
                    'office_id' => $summareconId,
                    'user_id' => $user->id,
                    'is_default' => true,
                    'created_at' => $now,
                    'updated_at' => $now,
                ])->all();

                if ($rows !== []) {
                    DB::table('office_user')->insert($rows);
                }
            });
    }

    public function down(): void
    {
        Schema::dropIfExists('office_user');
    }
};
