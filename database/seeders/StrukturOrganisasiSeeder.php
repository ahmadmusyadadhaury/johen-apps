<?php

namespace Database\Seeders;

use App\Models\Division;
use App\Models\Employee;
use App\Models\Position;
use Illuminate\Database\Seeder;

class StrukturOrganisasiSeeder extends Seeder
{
    public function run(): void
    {
        $ceo = Position::firstOrCreate(
            ['nama' => 'CEO'],
            ['parent_id' => null, 'is_active' => true]
        );

        Employee::firstOrCreate(
            ['position' => 'CEO'],
            [
                'nik' => 'CEO001',
                'nama' => 'Josia Hendrico Simanungkalit',
                'email' => 'ceo@johen.com',
                'tipe' => 'karyawan_aktif',
            ]
        );

        Position::where('nama', 'HR')->update(['nama' => 'Human Resource Generalist']);
        Position::where('nama', 'UI UX')->update(['nama' => 'UI/UX Designer']);
        Position::where('nama', 'Fullstack')->update(['nama' => 'Fullstack Developer']);

        $shifts = ['Pagi', 'Siang', 'Malam', 'Subuh'];
        $oldHosts = Position::where('nama', 'Host Live')->get();
        foreach ($oldHosts as $old) {
            $old->delete();
            foreach ($shifts as $shift) {
                Position::create(['nama' => "Host Live ($shift)", 'parent_id' => $old->parent_id, 'is_active' => true]);
            }
        }

        if (Employee::where('position', '!=', 'CEO')->exists()) {
            if ($this->command) {
                $this->command->warn('Data karyawan lain masih ada, skip pembuatan jabatan.');
            }
            return;
        }

        Position::where('id', '!=', $ceo->id)->delete();

        $gm  = Position::create(['nama' => 'General Manager', 'parent_id' => $ceo->id, 'is_active' => true]);
        $hos1 = Position::create(['nama' => 'Head of Store 1', 'parent_id' => $gm->id, 'is_active' => true]);
        $hos2 = Position::create(['nama' => 'Head of Store 2', 'parent_id' => $gm->id, 'is_active' => true]);
        $hr   = Position::create(['nama' => 'Human Resource Generalist', 'parent_id' => $hos1->id, 'is_active' => true]);

        $koorCreative = Position::create(['nama' => 'Koordinator Creative', 'parent_id' => $hos1->id, 'is_active' => true]);
        $koorAdmin    = Position::create(['nama' => 'Koordinator Admin', 'parent_id' => $hos1->id, 'is_active' => true]);
        $koorPubg     = Position::create(['nama' => 'Koordinator Johen PUBG', 'parent_id' => $hos1->id, 'is_active' => true]);
        $koorFf       = Position::create(['nama' => 'Koordinator Free Fire', 'parent_id' => $hos1->id, 'is_active' => true]);
        $koorRoblox   = Position::create(['nama' => 'Koordinator Roblox', 'parent_id' => $hos1->id, 'is_active' => true]);
        $koorEfoot    = Position::create(['nama' => 'Koordinator E-football', 'parent_id' => $hos1->id, 'is_active' => true]);

        $koorIt       = Position::create(['nama' => 'Koordinator IT', 'parent_id' => $hos2->id, 'is_active' => true]);
        $koorMlbb     = Position::create(['nama' => 'Koordinator MLBB', 'parent_id' => $hos2->id, 'is_active' => true]);
        $koorValo     = Position::create(['nama' => 'Koordinator Valorant', 'parent_id' => $hos2->id, 'is_active' => true]);
        $koorMonkeyPubg = Position::create(['nama' => 'Koordinator Monkey PUBG', 'parent_id' => $hos2->id, 'is_active' => true]);

        Position::create(['nama' => 'UI/UX Designer', 'parent_id' => $koorIt->id, 'is_active' => true]);
        Position::create(['nama' => 'Fullstack Developer', 'parent_id' => $koorIt->id, 'is_active' => true]);
        Position::create(['nama' => 'IT Support', 'parent_id' => $koorIt->id, 'is_active' => true]);

        Position::create(['nama' => 'Desain Grafis', 'parent_id' => $koorCreative->id, 'is_active' => true]);
        Position::create(['nama' => 'Video Animator', 'parent_id' => $koorCreative->id, 'is_active' => true]);
        Position::create(['nama' => 'Video Editor', 'parent_id' => $koorCreative->id, 'is_active' => true]);
        Position::create(['nama' => 'Social Media Specialist', 'parent_id' => $koorCreative->id, 'is_active' => true]);
        Position::create(['nama' => 'Content Creator', 'parent_id' => $koorCreative->id, 'is_active' => true]);
        Position::create(['nama' => 'Admin KOL', 'parent_id' => $koorCreative->id, 'is_active' => true]);

        Position::create(['nama' => 'Admin Johen PUBG (Pagi)', 'parent_id' => $koorAdmin->id, 'is_active' => true]);
        Position::create(['nama' => 'Admin Johen PUBG (Malam)', 'parent_id' => $koorAdmin->id, 'is_active' => true]);
        Position::create(['nama' => 'Admin MLBB (Pagi)', 'parent_id' => $koorAdmin->id, 'is_active' => true]);
        Position::create(['nama' => 'Admin MLBB (Malam)', 'parent_id' => $koorAdmin->id, 'is_active' => true]);
        Position::create(['nama' => 'Admin Monkey & Dex', 'parent_id' => $koorAdmin->id, 'is_active' => true]);
        Position::create(['nama' => 'Admin Transaksi Roblox', 'parent_id' => $koorAdmin->id, 'is_active' => true]);

        foreach ([$koorPubg, $koorFf, $koorRoblox, $koorEfoot, $koorMlbb, $koorValo, $koorMonkeyPubg] as $koor) {
            $gameName = str_replace('Koordinator ', '', $koor->nama);
            $shifts = match ($koor->nama) {
                'Koordinator Roblox' => ['Pagi', 'Siang'],
                'Koordinator Valorant' => ['Pagi'],
                default => ['Pagi', 'Siang', 'Malam', 'Subuh'],
            };
            foreach ($shifts as $shift) {
                Position::create(['nama' => "Host {$gameName} ({$shift})", 'parent_id' => $koor->id, 'is_active' => true]);
            }
        }

        $koorStock = Position::create(['nama' => 'Koordinator Stock', 'parent_id' => $hos1->id, 'is_active' => true]);
        Position::create(['nama' => 'Admin Record Johen & Monkey PUBG', 'parent_id' => $koorStock->id, 'is_active' => true]);
        Position::create(['nama' => 'Admin Record FF, ML, E-Football', 'parent_id' => $koorStock->id, 'is_active' => true]);

        Position::create(['nama' => 'Admin HR', 'parent_id' => $hr->id, 'is_active' => true]);
        Position::create(['nama' => 'Admin GA', 'parent_id' => $hr->id, 'is_active' => true]);
        Position::create(['nama' => 'Office Boy', 'parent_id' => $hr->id, 'is_active' => true]);
        Position::create(['nama' => 'Resepsionis 1', 'parent_id' => $hr->id, 'is_active' => true]);
        Position::create(['nama' => 'Resepsionis 2', 'parent_id' => $hr->id, 'is_active' => true]);
    }
}
