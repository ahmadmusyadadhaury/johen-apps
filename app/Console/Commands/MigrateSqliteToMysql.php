<?php

namespace App\Console\Commands;

use Illuminate\Console\Attributes\Description;
use Illuminate\Console\Attributes\Signature;
use Illuminate\Console\Command;

#[Signature('app:migrate-sqlite-to-mysql')]
#[Description('Command description')]
class MigrateSqliteToMysql extends Command
{
    /**
     * Execute the console command.
     */
    public function handle()
    {
        //
    }
}
