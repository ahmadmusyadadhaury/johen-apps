<?php

use App\Models\Position;
use Illuminate\Database\Migrations\Migration;

return new class extends Migration
{
    public function up(): void
    {
        $headOfStore2 = Position::where('nama', 'Head of Store 2')->first();
        $coordinator = Position::where('nama', 'Koordinator Free Fire')->first();

        if ($headOfStore2 && $coordinator) {
            $coordinator->parent_id = $headOfStore2->id;
            $coordinator->save();
        }
    }

    public function down(): void
    {
        $headOfStore1 = Position::where('nama', 'Head of Store 1')->first();
        $coordinator = Position::where('nama', 'Koordinator Free Fire')->first();

        if ($headOfStore1 && $coordinator) {
            $coordinator->parent_id = $headOfStore1->id;
            $coordinator->save();
        }
    }
};
