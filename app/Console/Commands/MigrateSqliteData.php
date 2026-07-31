<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class MigrateSqliteData extends Command
{
    protected $signature = 'db:migrate-from-sqlite';
    protected $description = 'Migrate all data from SQLite (database/database.sqlite) to MySQL';

    protected array $tableOrder = [
        // No dependencies
        'asset_categories',
        'payment_categories',
        'announcements',
        'manual_books',
        'freelances',
        'bonus_host_lives',
        'bonus_admin_transactions',
        'bonus_creatives',
        'personal_access_tokens',
        'content_plans',
        'bonus_pubgs',
        'divisions',
        'influencers',

        // Depends on: divisions
        'positions',

        // Depends on: none at this point (user_id removed from employees)
        'employees',

        // Depends on: employees (via employee_id FK)
        'users',

        // Depends on: users
        'sessions',
        'payroll_imports',
        'meetings',
        'electricity_settings',
        'electricity_topups',
        'electricity_token_checks',
        'internet_payments',
        'internet_usage_checks',
        'digital_assets',
        'ipl_ruko_payments',
        'kalender_events',
        'payment_submissions',
        'influencer_pengajuans',

        // Depends on: employees
        'employee_documents',
        'position_histories',
        'employee_contracts',
        'attendances',
        'weekly_plan_reports',
        'activity_competitors',

        // Depends on: employees, positions
        'leave_requests',
        'employee_position',

        // Depends on: employees, users
        'meeting_requests',
        'promotions',

        // Depends on: asset_categories, users
        'assets',

        // Depends on: assets, users, employees
        'asset_loans',
        'asset_maintenances',
        'payments',

        // Depends on: payroll_imports, employees
        'payroll_details',

        // Depends on: positions, users
        'position_notes',

        // Depends on: payroll_details
        'email_logs',

        // Depends on: content_plans
        'content_plan_reports',

        // Depends on: influencers
        'influencer_pembayarans',

        // Depends on: position_notes
        'position_note_comments',

        // System tables
        'cache',
        'cache_locks',
        'jobs',
        'job_batches',
        'failed_jobs',
        'password_reset_tokens',
    ];

    public function handle(): int
    {
        $sqlitePath = base_path('JSA_Office');

        if (!file_exists($sqlitePath)) {
            $sqlitePath = database_path('database.sqlite');
        }

        if (!file_exists($sqlitePath)) {
            $this->error("SQLite database not found at: $sqlitePath");
            return 1;
        }

        config(['database.connections.sqlite_import' => [
            'driver' => 'sqlite',
            'database' => $sqlitePath,
            'prefix' => '',
        ]]);

        $sqlite = DB::connection('sqlite_import');

        $this->info('Checking SQLite database...');
        $sqliteTables = collect($sqlite->select("SELECT name FROM sqlite_master WHERE type='table' AND name NOT LIKE 'sqlite_%' AND name != 'migrations' ORDER BY name"))
            ->map(fn($t) => $t->name)
            ->toArray();

        $mysqlTables = collect(DB::select('SHOW TABLES'))
            ->map(fn($t) => array_values((array)$t)[0])
            ->toArray();

        $this->info('Found ' . count($sqliteTables) . ' tables in SQLite, ' . count($mysqlTables) . ' tables in MySQL');

        DB::statement('SET FOREIGN_KEY_CHECKS = 0');

        $tablesToTruncate = array_intersect($this->tableOrder, $mysqlTables);
        foreach (array_reverse($tablesToTruncate) as $table) {
            DB::statement("TRUNCATE TABLE `$table`");
        }
        $this->line('Truncated ' . count($tablesToTruncate) . ' MySQL tables');

        // Ensure foto column can hold base64 data
        DB::statement("ALTER TABLE employees MODIFY foto LONGTEXT");
        $this->line('  Modified employees.foto to LONGTEXT');

        $totalRows = 0;
        $results = [];

        foreach ($this->tableOrder as $table) {
            if (!in_array($table, $sqliteTables)) {
                continue;
            }

            $rows = $sqlite->table($table)->get()->map(fn($r) => (array)$r);
            $count = $rows->count();

            if ($count === 0) {
                $results[$table] = 0;
                continue;
            }

            $allCols = array_keys((array)$rows->first());
            $mysqlCols = Schema::connection('mysql')->getColumnListing($table);
            $commonCols = array_intersect($allCols, $mysqlCols);

            if (empty($commonCols)) {
                $this->warn("  {$table}: no common columns, skipping");
                $results[$table] = 0;
                continue;
            }

            $filtered = $rows->map(fn($r) => array_intersect_key($r, array_flip($commonCols)));

            $isSelfRef = in_array($table, ['positions', 'position_note_comments']);
            $refCol = 'parent_id';

            if ($isSelfRef) {
                $pass1 = $filtered->filter(fn($r) => $r[$refCol] === null);
                foreach ($pass1->chunk(100) as $chunk) {
                    DB::table($table)->insert($chunk->toArray());
                }
                $pass2 = $filtered->filter(fn($r) => $r[$refCol] !== null);
                foreach ($pass2->chunk(100) as $chunk) {
                    DB::table($table)->insert($chunk->toArray());
                }
            } else {
                foreach ($filtered->chunk(100) as $chunk) {
                    DB::table($table)->insert($chunk->toArray());
                }
            }

            $totalRows += $count;
            $results[$table] = $count;
        }

        foreach ($results as $table => $count) {
            if ($count > 0) {
                $this->line("  {$table}: {$count} rows");
            }
        }

        // Convert old photo file paths to base64
        $this->newLine();
        $this->line('Checking employee photos...');
        $photoCount = 0;
        $employees = DB::table('employees')->whereNotNull('foto')->get(['id', 'foto']);
        foreach ($employees as $emp) {
            if (!str_starts_with($emp->foto, 'base64:')) {
                $photoPath = storage_path('app/public/employees/' . $emp->foto);
                if (file_exists($photoPath)) {
                    $contents = base64_encode(file_get_contents($photoPath));
                    DB::table('employees')->where('id', $emp->id)->update(['foto' => 'base64:' . $contents]);
                    $photoCount++;
                }
            }
        }
        $this->line("  Converted {$photoCount} employee photos to base64");

        DB::statement('SET FOREIGN_KEY_CHECKS = 1');

        $this->newLine();
        $this->info("\u{2713} Migration complete! Total: {$totalRows} rows transferred from " . count($sqliteTables) . " tables.");
        $this->line('   Photos converted: ' . $photoCount);

        return 0;
    }
}
