<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;   // For File::exists(), File::copyDirectory(), etc.

class MicroServiceCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:manage-call {module} {submodule} {appName}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Creates a new micro-service or tenant app structure.';

    /**
     * Define our modules and submodules in a single structure.
     */
    const MODULES = [
        'core' => [
            'mod' => 'core.avinertech.com',
            'submodule' => [
                'micro'   => 'MicroServices',
                'service' => 'Services',
            ],
        ],
        'tenant' => [
            'mod' => 'tenant.avinertech.com',
            'submodule' => [
                'apps' => 'Applications',
            ],
        ],
    ];

    // Paths to our stubs or base folders
    const STUBS_BASIC    = 'Stubs/demo.avinertech.com/*';
    const SERVICES_BASIC = 'Basic/base.avinertech.com/';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        // Grab the arguments
        $module    = $this->argument('module');
        $submodule = $this->argument('submodule');
        $appName   = $this->argument('appName');

        // Where is the root project folder (defined in config/app.php perhaps)
        $rootPath  = rtrim(config('app.root_path', base_path()), DIRECTORY_SEPARATOR);

        // Validate the requested module
        if (! array_key_exists($module, self::MODULES)) {
            $this->error("Module [{$module}] is invalid!");
            return 1;
        }

        $moduleData = self::MODULES[$module];

        // Validate the requested submodule
        if (! array_key_exists($submodule, $moduleData['submodule'])) {
            $this->error("Submodule [{$submodule}] is not valid for module [{$module}]!");
            return 1;
        }

        $submoduleFolder = $moduleData['submodule'][$submodule];

        // Build the final folder path
        $filePath = $rootPath
            . DIRECTORY_SEPARATOR
            . $moduleData['mod']
            . DIRECTORY_SEPARATOR
            . $submoduleFolder
            . DIRECTORY_SEPARATOR
            . "{$appName}.avinertech.com";

        // Check if directory already exists
        if (is_dir($filePath)) {
            $this->error("App [{$appName}] in module [{$module}] submodule [{$submodule}] already exists!");
            return 1;
        }

        $basicBuild = ($module === 'core') ? self::SERVICES_BASIC : self::STUBS_BASIC;

        $basicPath = $rootPath
            . DIRECTORY_SEPARATOR
            . $moduleData['mod']
            . DIRECTORY_SEPARATOR
            . trim($basicBuild, DIRECTORY_SEPARATOR);

        try {
            File::makeDirectory($filePath, 0755, true);

            if (! File::exists($basicPath)) {
                throw new \Exception("Source path [{$basicPath}] does not exist!");
            }

            File::copyDirectory($basicPath, $filePath);

            $this->info("New project [{$appName}] has been built successfully!");
            return 0;

        } catch (\Exception $e) {
            // Cleanup (remove the newly created directory)
            if (is_dir($filePath)) {
                File::deleteDirectory($filePath);
            }

            $this->error("Error: " . $e->getMessage());
            $this->info("Rolled back creation of [{$appName}] safely.");
            return 1;
        }
    }
}
