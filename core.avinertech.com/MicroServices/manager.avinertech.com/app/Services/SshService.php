<?php

namespace App\Services;

use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Artisan;
// use Illuminate\Support\Facades\Process;
use Symfony\Component\Process\Process;
use App\Models\Tenant;

class SshService
{
    private $tempDir;
    private $sshConfig;
    private $sshConfigFile;
    private $knownHostsFile;
    private $scriptPath;

    // Define available actions
    const ACTION_CREATE_MODULE = 'create-module';
    const ACTION_DELETE_MODULE = 'delete-module';
    const ACTION_DEPLOY_MODULE = 'deploy-module';
    const ACTION_SSL_CERT = 'ssl-cert';
    const ACTION_GET_LOG = 'get-log';
    const ACTION_CREATE_TENANT_DB = 'create-tenant-db';
    const ACTION_CREATE_TENANT = 'create-tenant';

    // Map actions to their script files
    private $actionScripts = [
        self::ACTION_CREATE_MODULE => 'create-module.sh',
        self::ACTION_DELETE_MODULE => 'delete-module.sh',
        self::ACTION_DEPLOY_MODULE => 'deploy-module.sh',
        self::ACTION_SSL_CERT => 'ssl-cert.sh',
        self::ACTION_GET_LOG => 'get-log.sh',
        self::ACTION_CREATE_TENANT_DB => 'create-tenant-db.sh',
        self::ACTION_CREATE_TENANT => 'create-tenant'
    ];

    // Map actions to their streaming requirements
    private $streamingActions = [
        self::ACTION_GET_LOG => true
    ];

    public function __construct()
    {
        $this->sshConfig = config('sshpass.connection.main');

        $this->tempDir = storage_path('app/temp/ssh');
        if (!File::exists($this->tempDir)) {
            File::makeDirectory($this->tempDir, 0755, true);
        }

        // Create known_hosts file if it doesn't exist
        $this->knownHostsFile = $this->tempDir . '/known_hosts';
        if (!File::exists($this->knownHostsFile)) {
            File::put($this->knownHostsFile, '');
            chmod($this->knownHostsFile, 0600);
        }

        // Create SSH config file
        $this->sshConfigFile = $this->tempDir . '/ssh_config';
        File::put($this->sshConfigFile, "Host {$this->sshConfig['host']}\n" .
            "    UserKnownHostsFile {$this->knownHostsFile}\n" .
            "    StrictHostKeyChecking no\n" .
            "    User {$this->sshConfig['username']}\n" .
            "    Port {$this->sshConfig['port']}\n"
        );
        chmod($this->sshConfigFile, 0600);
    }

    /**
     * Build SSH command with proper credentials and file handling
     *
     * @param string $scriptContent The content of the script to execute
     * @param array $parameters Parameters to pass to the script
     * @param bool $useHereDoc Whether to use heredoc for script content
     * @return array The command array ready for Process::run
     */
    private function buildSshCommand(string $scriptPath, array $parameters = [], bool $useHereDoc = false): array
    {
        // Create temporary script file
        // $tempScriptPath = $this->tempDir . '/temp_script_' . uniqid() . '.sh';
        // // if ($useHereDoc) {
        //     // Use heredoc for script content
        //     $scriptContent = "cat << 'EOF' > {$scriptPath}\nEOF\n" .
        //             "chmod +x {$tempScriptPath}\n" .
        //             "bash {$tempScriptPath} " . implode(' ', array_map('escapeshellarg', $parameters));
        //     dd($scriptContent);
        // // } else {
        //     // Direct script execution
        //     // File::put($tempScriptPath, $scriptContent);
        //     // chmod($tempScriptPath, 0777);
        //     // chown($tempScriptPath, 'www-data');
        // // }

        // // Build the base SSH command
        // $command = [
        //     'sshpass',
        //     '-p', base64_decode($this->sshConfig['password']),
        //     'ssh',
        //     '-F', escapeshellarg($this->sshConfigFile),
        //     "{$this->sshConfig['username']}@{$this->sshConfig['host']}"
        // ];

        // if ($useHereDoc) {
        //     $command[] = $scriptContent;
        // } else {
        //     $command[] = 'bash -s < ' . escapeshellarg($tempScriptPath);
        // }

        $command = [
            'sshpass',
            '-p', base64_decode($this->sshConfig['password']),
            'ssh',
            '-F', $this->sshConfigFile,
            "{$this->sshConfig['username']}@{$this->sshConfig['host']}",
            'bash', '-s', '--', ...$parameters
        ];
        $process = new Process($command);
        $process->setInput($scriptPath);
        $process->run();
        // dd($command);

        $output = $process->getOutput();
        $error = $process->getErrorOutput();

        return [
            // 'command' => $command,
            'output' => $output,
            'error' => $error
            // 'tempScriptPath' => $tempScriptPath
        ];
    }

    /**
     * Execute SSH command and handle cleanup
     *
     * @param array $commandResult Result from buildSshCommand
     * @param bool $isStreaming Whether this is a streaming command
     * @return mixed Process result or streaming output
     * @throws \Exception
     */
    private function executeSshCommand(array $commandResult, bool $isStreaming = false)
    {
        $command = $commandResult['command'];
        // $tempScriptPath = $commandResult['tempScriptPath'];
        // dd($command);
        try {
            if ($isStreaming) {
        $result = Process::start($command);
        $output = [];

        while (!$result->done()) {
            $line = $result->latestOutput();
            if (!empty($line)) {
                $output[] = $line;
                if (strpos($line, 'SUCCESS') !== false) {
                    $result->stop();
                    break;
                }
            }
                }
            } else {
                $result = Process::run(implode(' ', $command));
                dd($result->errorOutput());
        }

        if (!$result->successful()) {
            Log::error('SSH command failed', [
                'command' => $command,
                'exitCode' => $result->exitCode(),
                    'output' => $isStreaming ? $output : $result->output(),
                'errorOutput' => $result->errorOutput()
            ]);
                throw new \Exception("SSH command failed: " . $result->errorOutput());
            }

            return $isStreaming ? $output : $result->output();
        } finally {
            // Clean up temporary script
            // if (File::exists($tempScriptPath)) {
            //     File::delete($tempScriptPath);
            // }
        }
    }

    /**
     * Execute an SSH action with parameters
     *
     * @param string $action The action to execute (use class constants)
     * @param array $parameters Parameters for the action
     * @param array $options Additional options for the action
     * @return mixed The result of the action
     * @throws \Exception
     */
    public function executeAction(string $action, array $parameters = [], array $options = [], $request = null)
    {
        if (!isset($this->actionScripts[$action])) {
            throw new \InvalidArgumentException("Invalid action: {$action}");
        }

        $scriptFile = $this->actionScripts[$action];

        if($scriptFile && File::exists(base_path("app/Console/Commands/{$scriptFile}"))) {
            $scriptContent = File::get(base_path("app/Console/Commands/{$scriptFile}"));
            // $commandResult = $this->buildSshCommand(
            //     $scriptContent, 
            //     $parameters,
            //     $options['use_heredoc'] ?? false
            // );
            
            $isStreaming = $this->streamingActions[$action] ?? false;
            // $result = $this->executeSshCommand($commandResult, $isStreaming);

        }


        // Handle special post-processing for certain actions
        switch ($action) {
            case self::ACTION_CREATE_MODULE:
                // dd(Tenant::get()->toArray());

                $tenant = Tenant::find($request[2]);
                $paths = $this->getSourceAndTargetPaths($request[0], $request[1], $tenant->host);
                $parameters = array_values($paths);
                $commandResult = $this->buildSshCommand(
                    $scriptContent, 
                    $parameters,
                    $options['use_heredoc'] ?? false
                );

                return $commandResult;

            case self::ACTION_DEPLOY_MODULE:
                // dd($request);
                $tenant = Tenant::findOrFail($request[2]);
                $paths = $this->getSourceAndTargetPaths($request[0], $request[1], $tenant->host);
                // dd($paths);
                $serverConfig = File::get(base_path('app/Console/Commands/server.conf'));
                $serverConfig = str_replace('{{SERVER_NAME}}', "$request[2].avinertech.com", $serverConfig);
                $serverConfig = str_replace('{{PATH}}', $paths['target_path'], $serverConfig);
                $serverConfig = str_replace("\n", '', $serverConfig);
                // dd($serverConfig);
                $parameters = [
                    $tenant->host,
                    $paths['target_path']
                ];

                return $this->buildSshCommand(
                    $scriptContent,
                    $parameters,
                    $options['use_heredoc'] ?? false
                );
            case self::ACTION_SSL_CERT:
                $tenant = Tenant::findOrFail($request[0]);
                $parameters = [
                    $tenant->host
                ];
                return $this->buildSshCommand(
                    $scriptContent,
                    $parameters,
                    $options['use_heredoc'] ?? false
                );
            case self::ACTION_CREATE_TENANT_DB:
                $tenant = Tenant::findOrFail($request[0]);
                $parameters = [
                    $tenant->database_name,
                    $tenant->database_user,
                    $tenant->database_password,
                    $tenant->database_host ?? 'localhost'
                ];
                $commandResult = $this->buildSshCommand(
                    $scriptContent,
                    $parameters,
                    $options['use_heredoc'] ?? false
                );
                return $commandResult;

                case self::ACTION_CREATE_TENANT:
                    $tenant = Tenant::find($request['id'] ?? null);

                    if(Tenant::where('host', $request['host'])->exists() && !$tenant) {
                        throw new \Exception("Tenant Already Exists, Try a new Host");
                    }

                    if(!$tenant) {
                        $request['application_path'] = $this->getSourceAndTargetPaths('tenant', 'custom-app', $request['host'])['target_path'];
                        $tenant = Tenant::create($request);
                    }

                    $tenant->update($request);
                    return [
                        'status' => true,
                        'message' => 'Tenant created successfully',
                        'data' => $tenant
                    ];
            default:
                return $result;
        }
    }

    // Convenience methods that use executeAction
    public function createModule($module, $submodule, $host)
    {
        return $this->executeAction(self::ACTION_CREATE_MODULE, [], [], [$module, $submodule, $host]);
    }

    public function deleteModule($module, $submodule, $appName)
    {
        return $this->executeAction(self::ACTION_DELETE_MODULE, [$module, $submodule, $appName]);
    }

    public function deployModule($module, $submodule, $tenantId)
    {
        return $this->executeAction(self::ACTION_DEPLOY_MODULE, [], [], [$module, $submodule, $tenantId]);
    }

    public function sslCert($tenantId)
    {
        return $this->executeAction(self::ACTION_SSL_CERT,[],  [], [$tenantId]);
    }

    public function getLog($appDomain)
    {
        return $this->executeAction(self::ACTION_GET_LOG, [$appDomain]);
    }

    public function createDatabase($tenantId)
    {
        return $this->executeAction(
            self::ACTION_CREATE_TENANT_DB, [], [],
            [$tenantId]
        );
    }

    public function createTenant($tenantData)
    {
        return $this->executeAction(self::ACTION_CREATE_TENANT, [], [], $tenantData);
    }

    public function getSourceAndTargetPaths($module, $submodule, $host)
    {
        $basePath = '/var/www/sites/Project';
        $result = [
            'source_path' => '',
            'target_path' => ''
        ];

        if ($module === 'core') {
            // Core module paths
            $result['source_path'] = "{$basePath}/core.avinertech.com/Basic/base.avinertech.com";
            if ($submodule === 'basic') {
            } elseif ($submodule === 'micro') {
                $result['target_path'] = "{$basePath}/core.avinertech.com/MicroServices/{$host}";
            } elseif ($submodule === 'service') {
                $result['target_path'] = "{$basePath}/core.avinertech.com/Services/{$host}";
            }
        } elseif ($module === 'tenant') {
            // Tenant module paths
            $result['source_path'] = "{$basePath}/tenant.avinertech.com/Stubs/demo.avinertech.com";
            
            if ($submodule === 'custom-app') {
                $result['target_path'] = "{$basePath}/tenant.avinertech.com/CustomApplications/{$host}";
            } elseif ($submodule === 'app') {
                $result['target_path'] = "{$basePath}/tenant.avinertech.com/Applications/{$host}";
            }
        }

        return $result;
    }

    public function createTenantOrCore($module, $submodule, $appName)
    {
        $paths = $this->getSourceAndTargetPaths($module, $submodule, $appName);
        
        if (empty($paths['source_path']) || empty($paths['target_path'])) {
            throw new \InvalidArgumentException('Invalid module/submodule combination');
        }

        return $this->executeAction(
            self::ACTION_CREATE_MODULE,
            [$paths['source_path'], $paths['target_path']]
        );
    }
}