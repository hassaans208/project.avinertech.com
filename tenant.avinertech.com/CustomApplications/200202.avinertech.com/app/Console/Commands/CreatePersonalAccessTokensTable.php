<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class CreatePersonalAccessTokensTable extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'sanctum:create-table';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create the personal_access_tokens table manually';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Creating personal_access_tokens table...');

        $sql = "CREATE TABLE IF NOT EXISTS personal_access_tokens (
            id INTEGER PRIMARY KEY AUTOINCREMENT,
            tokenable_type VARCHAR(255) NOT NULL,
            tokenable_id INTEGER NOT NULL,
            name VARCHAR(255) NOT NULL,
            token VARCHAR(64) NOT NULL UNIQUE,
            abilities TEXT NULL,
            last_used_at TIMESTAMP NULL,
            expires_at TIMESTAMP NULL,
            created_at TIMESTAMP NULL,
            updated_at TIMESTAMP NULL
        )";

        DB::statement($sql);

        $this->info('Table created successfully!');
    }
} 