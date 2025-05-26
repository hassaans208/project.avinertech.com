<?php

namespace App\Services;

use App\Models\ColumnType;
use Illuminate\Support\Str;
use Illuminate\Support\Collection;
use InvalidArgumentException;
use App\Services\EncryptionService;

class ColumnInterpreterService
{
    /**
     * Map of database column types to Laravel migration methods
     */
    protected array $columnTypeMap = [];
    protected EncryptionService $encryptionService;

    public function __construct(EncryptionService $encryptionService)
    {
        $this->encryptionService = $encryptionService;
        // Load column types from database and map them
        $this->loadColumnTypeMap();
    }

    /**
     * Load column types from database into map
     */
    protected function loadColumnTypeMap(): void
    {
        $columnTypes = ColumnType::all();
        foreach ($columnTypes as $columnType) {
            $this->columnTypeMap[Str::lower($columnType->mysql_type)] = $columnType->laravel_method;
        }
    }

    /**
     * Interpret an associative array of column definitions and convert them to Laravel column types
     *
     * @param array $columns Array of column definitions
     * @return Collection Collection of interpreted column definitions
     * @throws InvalidArgumentException
     */
    public function interpret(array $columns): Collection
    {
        $interpretedColumns = collect();

        foreach ($columns as $name => $definition) {
            if (!is_array($definition)) {
                throw new InvalidArgumentException("Column definition for '{$name}' must be an array");
            }
            // dd($columns,$definition['type']);
            $columnType = $this->findColumnType($definition['type'] ?? null);
            if (!$columnType) {
                throw new InvalidArgumentException("Unknown column type: {$definition['type']}");
            }

            $interpretedColumns->push([
                'name' => $name,
                'type' => $this->columnTypeMap[Str::lower($definition['type'])],
                'parameters' => $this->buildParameters($columnType, $definition),
                'attributes' => $this->buildAttributes($definition),
                'definition' => $definition,
                'column_type' => $columnType,
            ]);
        }

        return $interpretedColumns;
    }

    /**
     * Find the appropriate column type model for a given database type
     *
     * @param string|null $type
     * @return ColumnType|null
     */
    protected function findColumnType(?string $type): ?ColumnType
    {
        if (!$type) {
            return null;
        }

        return ColumnType::where('mysql_type', Str::upper($type))->first();
    }

    /**
     * Build the parameters array for a column type based on its requirements
     *
     * @param ColumnType $columnType
     * @param array $definition
     * @return array
     */
    protected function buildParameters(ColumnType $columnType, array $definition): array
    {
        $parameters = [];

        if ($columnType->requires_length && isset($definition['length'])) {
            $parameters[] = $definition['length'];
        }

        if ($columnType->requires_precision && isset($definition['precision'])) {
            $parameters[] = $definition['precision'];
        }

        if ($columnType->requires_scale && isset($definition['scale'])) {
            $parameters[] = $definition['scale'];
        }

        if ($columnType->requires_values && isset($definition['values'])) {
            $parameters[] = $definition['values'];
        }

        return $parameters;
    }

    /**
     * Build the attributes array for a column definition
     *
     * @param array $definition
     * @return array
     */
    protected function buildAttributes(array $definition): array
    {
        $attributes = [];

        // Handle nullable
        if (isset($definition['nullable'])) {
            $attributes[] = 'nullable()';
        }

        // Handle default value
        if (array_key_exists('default', $definition)) {
            $attributes[] = "default('{$definition['default']}')";
        }

        // Handle unique
        if (isset($definition['unique'])) {
            $attributes[] = 'unique()';
        }

        // Handle index
        if (isset($definition['index'])) {
            $attributes[] = 'index()';
        }

        // Handle unsigned
        if (isset($definition['unsigned'])) {
            $attributes[] = 'unsigned()';
        }

        // Handle comment
        if (isset($definition['comment'])) {
            $comment = addslashes($definition['comment']);
            $attributes[] = "comment('{$comment}')";
        }

        return $attributes;
    }

    /**
     * Generate the Laravel migration code for a column
     *
     * @param array $column
     * @return string
     */
    public function generateMigrationCode(array $column): string
    {
        $code = "\$table->{$column['column_type']['laravel_method']}('{$column['name']}'";

        // Add parameters
        if (!empty($column['parameters'])) {
            $paramString = collect($column['parameters'])->map(function ($param) {
                if (is_array($param)) {
                    // Handle array parameters (like enum values)
                    return "['" . implode("', '", $param) . "']";
                }
                return is_string($param) ? "'$param'" : $param;
            })->join(', ');
            
            $code .= ', ' . $paramString;
        }

        $code .= ')';

        // Add attributes
        if (!empty($column['attributes'])) {
            $code .= '->' . implode('->', $column['attributes']);
        }

        return $code . ';';
    }

    /**
     * Generate Laravel migration code for multiple columns
     *
     * @param array $columns
     * @return string
     */
    public function generateMigrationCodeForColumns(array $columns): string
    {
        try{
            $interpretedColumns = $this->interpret($columns);
            
            $interpretedColumns = $interpretedColumns->map(function ($column) {
                return $this->generateMigrationCode($column);
            })->implode("\n");

            $interpretedColumns = "<?php\n\n" . $interpretedColumns;

            file_put_contents(storage_path('query/test.php'), $interpretedColumns);

            return $interpretedColumns;
        } catch (\Exception $e) {
            return "Error: " . $e;
        }
    }

    /**
     * Generate complete migration file content for a model
     *
     * @param array $modelData
     * @return string
     */
    public function generateModelMigration(array $modelData): string
    {
        $tableName = Str::snake(Str::pluralStudly($modelData['name']));
        $className = 'Create' . Str::studly($modelData['name']) . 'Table';
        $timestamp = now()->format('Y_m_d_His');

        // If drop_table is true, only generate drop migration
        if ($modelData['drop_table']) {
            return $this->generateDropTableMigration($tableName);
        }

        // Check for overlapping columns if model exists
        $overlappingColumns = [];
        if ($modelData['exists'] && !empty($modelData['existing_columns'])) {
            $overlappingColumns = array_intersect(
                array_keys($modelData['new_columns'] ?? []),
                $modelData['existing_columns']
            );
        }

        // If model exists and has overlapping columns, generate alter table migration
        if ($modelData['exists'] && !empty($overlappingColumns)) {
            return $this->generateAlterTableMigration($tableName, $modelData, $overlappingColumns);
        }

        // Otherwise generate create table migration
        return $this->generateCreateTableMigration($tableName, $modelData);
    }

    /**
     * Generate drop table migration
     *
     * @param string $tableName
     * @return string
     */
    protected function generateDropTableMigration(string $tableName): string
    {
        return <<<PHP
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::dropIfExists('{$tableName}');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Table recreation would go here if needed
    }
};
PHP;
    }

    /**
     * Generate alter table migration
     *
     * @param string $tableName
     * @param array $modelData
     * @param array $overlappingColumns
     * @return string
     */
    protected function generateAlterTableMigration(string $tableName, array $modelData, array $overlappingColumns): string
    {
        $migrationContent = <<<PHP
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('{$tableName}', function (Blueprint \$table) {
PHP;

        // Handle overlapping columns modifications
        foreach ($overlappingColumns as $columnName) {
            $columnDefinition = $modelData['new_columns'][$columnName];
            $migrationContent .= "\n            // Modify column: {$columnName}\n";
            
            // Get the column type and parameters
            $columnType = $this->findColumnType($columnDefinition['type']);
            $method = $this->columnTypeMap[Str::lower($columnDefinition['type'])];
            $parameters = $this->buildParameters($columnType, $columnDefinition);
            
            // Handle different column types
            switch ($method) {
                case 'string':
                    if (isset($columnDefinition['length'])) {
                        $migrationContent .= "            \$table->string('{$columnName}', {$columnDefinition['length']})";
                    } else {
                        $migrationContent .= "            \$table->string('{$columnName}')";
                    }
                    break;
                    
                case 'integer':
                case 'bigInteger':
                case 'smallInteger':
                case 'tinyInteger':
                    $migrationContent .= "            \$table->{$method}('{$columnName}')";
                    break;
                    
                case 'decimal':
                case 'float':
                case 'double':
                    $precision = $columnDefinition['precision'] ?? 8;
                    $scale = $columnDefinition['scale'] ?? 2;
                    $migrationContent .= "            \$table->{$method}('{$columnName}', {$precision}, {$scale})";
                    break;
                    
                case 'enum':
                    $values = $columnDefinition['values'];
                    $valuesStr = "['" . implode("', '", $values) . "']";
                    $migrationContent .= "            \$table->enum('{$columnName}', {$valuesStr})";
                    break;
                    
                case 'text':
                case 'mediumText':
                case 'longText':
                case 'json':
                case 'boolean':
                case 'date':
                case 'dateTime':
                case 'timestamp':
                case 'time':
                    $migrationContent .= "            \$table->{$method}('{$columnName}')";
                    break;
                    
                default:
                    $migrationContent .= "            \$table->{$method}('{$columnName}')";
            }

            // Add column modifiers
            if (isset($columnDefinition['nullable'])) {
                $migrationContent .= $columnDefinition['nullable'] ? "->nullable()" : "->nullable(false)";
            }

            if (isset($columnDefinition['default'])) {
                $default = is_string($columnDefinition['default']) 
                    ? "'" . addslashes($columnDefinition['default']) . "'"
                    : $columnDefinition['default'];
                $migrationContent .= "->default({$default})";
            }

            if (isset($columnDefinition['unique']) && $columnDefinition['unique']) {
                $migrationContent .= "->unique()";
            }

            if (isset($columnDefinition['unsigned']) && $columnDefinition['unsigned']) {
                $migrationContent .= "->unsigned()";
            }

            if (isset($columnDefinition['comment'])) {
                $comment = addslashes($columnDefinition['comment']);
                $migrationContent .= "->comment('{$comment}')";
            }

            $migrationContent .= ";\n";
        }

        // Add new non-overlapping columns
        $newColumns = array_diff(array_keys($modelData['new_columns']), $overlappingColumns);
        if (!empty($newColumns)) {
            $migrationContent .= "\n            // Add new columns\n";
            foreach ($newColumns as $columnName) {
                $columnDefinition = $modelData['new_columns'][$columnName];
                $migrationContent .= "            " . $this->generateMigrationCode([
                    'name' => $columnName,
                    'column_type' => ['laravel_method' => $this->columnTypeMap[Str::lower($columnDefinition['type'])]],
                    'parameters' => $this->buildParameters(
                        $this->findColumnType($columnDefinition['type']),
                        $columnDefinition
                    ),
                    'attributes' => $this->buildAttributes($columnDefinition)
                ]) . "\n";
            }
        }

        $migrationContent .= <<<PHP
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('{$tableName}', function (Blueprint \$table) {
PHP;

        // Reverse the changes in down method
        foreach ($overlappingColumns as $columnName) {
            $migrationContent .= "            // Revert column: {$columnName}\n";
            $migrationContent .= "            \$table->string('{$columnName}')->change();\n";
        }

        foreach (array_diff(array_keys($modelData['new_columns']), $overlappingColumns) as $columnName) {
            $migrationContent .= "            \$table->dropColumn('{$columnName}');\n";
        }

        $migrationContent .= <<<PHP
        });
    }
};
PHP;

        return $migrationContent;
    }

    /**
     * Generate create table migration
     *
     * @param string $tableName
     * @param array $modelData
     * @return string
     */
    protected function generateCreateTableMigration(string $tableName, array $modelData): string
    {
        $migrationContent = <<<PHP
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('{$tableName}', function (Blueprint \$table) {
            \$table->id();
PHP;

        // Add existing columns if model exists
        if ($modelData['exists'] && !empty($modelData['existing_columns'])) {
            $migrationContent .= "\n            // Existing columns\n";
            foreach ($modelData['existing_columns'] as $column) {
                if (!isset($modelData['new_columns'][$column])) {
                    $migrationContent .= "            \$table->string('{$column}');\n";
                }
            }
        }

        // Add new columns
        if (!empty($modelData['new_columns'])) {
            $migrationContent .= "\n            // New columns\n";
            $interpretedColumns = $this->interpret($modelData['new_columns']);
            foreach ($interpretedColumns as $column) {
                $migrationContent .= "            " . $this->generateMigrationCode($column) . "\n";
            }
        }

        $migrationContent .= <<<PHP
            \$table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('{$tableName}');
    }
};
PHP;

        return $migrationContent;
    }

    /**
     * Generate and encrypt migration data
     *
     * @param array $modelData
     * @param string $encryptionKey
     * @return array
     */
    public function generateAndSaveMigration(array $modelData, string $encryptionKey): array
    {
        try {
            $migrationContent = $this->generateModelMigration($modelData);
            $timestamp = now()->format('Y_m_d_His');
            $fileName = "{$timestamp}_create_" . Str::snake($modelData['name']) . "_table.php";

            // Prepare migration data
            $migrationData = [
                'file_name' => $fileName,
                'content' => $migrationContent,
                'timestamp' => $timestamp,
                'model_name' => $modelData['name']
            ];

            // Handle data dump if requested
            if ($modelData['exists'] && $modelData['dump_data']) {
                $migrationData['data_dump'] = $this->dumpTableData($modelData['name']);
            }

            // Handle data download if requested
            if ($modelData['exists'] && $modelData['download_data']) {
                $migrationData['download_data'] = $this->prepareDataDownload($modelData);
            }

            // Encrypt the migration data
            $encryptedData = $this->encryptionService->encrypt($migrationData, $encryptionKey);

            return [
                'success' => true,
                'encrypted_data' => $migrationData
            ];
        } catch (\Exception $e) {
            return [
                'success' => false,
                'error' => $e->getMessage()
            ];
        }
    }

    /**
     * Dump table data to SQL
     *
     * @param string $tableName
     * @return string|null
     */
    protected function dumpTableData(string $tableName): ?string
    {
        try {
            $tableName = Str::snake(Str::pluralStudly($tableName));
            $data = \DB::table($tableName)->get();
            
            if ($data->isEmpty()) {
                return null;
            }

            $dump = "-- Dump data for table `{$tableName}`\n\n";
            foreach ($data as $row) {
                $values = array_map(function ($value) {
                    return is_null($value) ? 'NULL' : "'" . addslashes($value) . "'";
                }, (array) $row);
                
                $dump .= "INSERT INTO `{$tableName}` VALUES (" . implode(', ', $values) . ");\n";
            }

            return $dump;
        } catch (\Exception $e) {
            return null;
        }
    }

    /**
     * Prepare data for download
     *
     * @param array $modelData
     * @return string|null
     */
    protected function prepareDataDownload(array $modelData): ?string
    {
        try {
            $tableName = Str::snake(Str::pluralStudly($modelData['name']));
            $data = \DB::table($tableName)->get();
            
            if ($data->isEmpty()) {
                return null;
            }

            $format = $modelData['download_format'] ?? 'csv';
            $fileName = "{$tableName}_" . now()->format('Y_m_d_His') . ".{$format}";
            $filePath = storage_path("app/public/downloads/{$fileName}");

            // Ensure directory exists
            if (!file_exists(dirname($filePath))) {
                mkdir(dirname($filePath), 0755, true);
            }

            if ($format === 'csv') {
                $this->generateCsvFile($data, $filePath);
            }

            return "downloads/{$fileName}";
        } catch (\Exception $e) {
            return null;
        }
    }

    /**
     * Generate CSV file from data
     *
     * @param \Illuminate\Support\Collection $data
     * @param string $filePath
     * @return void
     */
    protected function generateCsvFile(\Illuminate\Support\Collection $data, string $filePath): void
    {
        $handle = fopen($filePath, 'w');
        
        // Write headers
        if ($data->isNotEmpty()) {
            fputcsv($handle, array_keys((array) $data->first()));
        }

        // Write data
        foreach ($data as $row) {
            fputcsv($handle, (array) $row);
        }

        fclose($handle);
    }

    /**
     * Generate and save migrations for multiple models
     *
     * @param array $modelsData
     * @param string $encryptionKey
     * @return array
     */
    public function generateAndSaveMigrations(array $modelsData, string $encryptionKey): array
    {
        try {
            // Sort models by order if specified
            usort($modelsData, function ($a, $b) {
                $orderA = $a['order'] ?? PHP_INT_MAX;
                $orderB = $b['order'] ?? PHP_INT_MAX;
                return $orderA - $orderB;
            });

            $allMigrationsData = [];

            foreach ($modelsData as $modelData) {
                $result = $this->generateAndSaveMigration($modelData, $encryptionKey);
                
                if (!$result['success']) {
                    return [
                        'success' => false,
                        'error' => "Error processing model {$modelData['name']}: " . $result['error']
                    ];
                }

                $allMigrationsData[] = $result['encrypted_data'];
            }

            // Encrypt the entire collection of migrations
            $encryptedData = $this->encryptionService->encrypt([
                'migrations' => $allMigrationsData,
                'timestamp' => now()->format('Y-m-d H:i:s'),
                'total_models' => count($modelsData)
            ], $encryptionKey);

            return [
                'success' => true,
                'encrypted_data' => $encryptedData
            ];
        } catch (\Exception $e) {
            return [
                'success' => false,
                'error' => $e->getMessage()
            ];
        }
    }

    /**
     * Generate preview of migrations for multiple models
     *
     * @param array $modelsData
     * @param string $encryptionKey
     * @return string
     */
    public function generateModelMigrationsPreview(array $modelsData, string $encryptionKey): string
    {
        // Sort models by order if specified
        usort($modelsData, function ($a, $b) {
            $orderA = $a['order'] ?? PHP_INT_MAX;
            $orderB = $b['order'] ?? PHP_INT_MAX;
            return $orderA - $orderB;
        });

        $previews = [];
        foreach ($modelsData as $modelData) {
            $previews[$modelData['name']] = [
                'content' => $this->generateModelMigration($modelData),
                'timestamp' => now()->format('Y-m-d H:i:s')
            ];
        }

        // Encrypt the previews
        return $this->encryptionService->encrypt($previews, $encryptionKey);
    }
}