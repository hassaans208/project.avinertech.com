<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Process;
use Symfony\Component\Console\Input\ArgvInput;

class OptimizeCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'optimize:tenant {tenantId}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $tenantId = $this->argument('tenantId');


        $dir = "Applications";

        if($tenantId == 'demo.avinertech.com') {
            $dir = "Stubs";
        }

        $command = "cd $dir/$tenantId && php artisan optimize";
        $process = Process::run($command);

        $this->info(
          $process->command(), $process->output(), $process->errorOutput()
        );
    }
}
