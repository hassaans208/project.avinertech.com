<?php

namespace App\Services;

use App\Models\ColumnType;
use App\Services\Abstracts\BaseService;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Str;

class QueryInterpreterService extends BaseService
{
    /**
     * @var array
     */
    protected array $columnDefinition = [];

    /**
     * @var ColumnType|null
     */
    protected ?ColumnType $columnType = null;

    /**
     * Set the column definition
     *
     * @param array $definition
     * @return self
     */
    public function setColumnDefinition(array $definition): self
    {
        $this->columnDefinition = $definition;
        return $this;
    }

    /**
     * Validate the service data
     *
     * @return void
     */
    protected function validate(): void
    {
        if (empty($this->columnDefinition)) {
            throw new \InvalidArgumentException('Column definition is required');
        }

        if (!isset($this->columnDefinition['type'])) {
            throw new \InvalidArgumentException('Column type is required');
        }
        dd($this->columnDefinition['type']);
        $this->columnType = ColumnType::where('mysql_type', strtoupper($this->columnDefinition['type']))
            ->first();

        if (!$this->columnType) {
            throw new \InvalidArgumentException(
                sprintf('Unsupported column type: %s', $this->columnDefinition['type'])
            );
        }
    }

    /**
     * Process the query interpretation
     *
     * @return void
     */
    protected function process(): void
    {
        $method = $this->columnType->getLaravelMethod();
        $parameters = $this->buildParameters();

        $this->result = [
            'method' => $method,
            'parameters' => $parameters,
            'column_type' => $this->columnType,
        ];
    }

    /**
     * Build the parameters for the Laravel migration method
     *
     * @return array
     */
    protected function buildParameters(): array
    {
        $parameters = [];

        // Add column name
        $parameters[] = $this->columnDefinition['name'] ?? '';

        // Add length if required
        if ($this->columnType->requiresLength() && isset($this->columnDefinition['length'])) {
            $parameters[] = $this->columnDefinition['length'];
        }

        // Add precision and scale if required
        if ($this->columnType->requiresPrecision() && isset($this->columnDefinition['precision'])) {
            $parameters[] = $this->columnDefinition['precision'];
            
            if ($this->columnType->requiresScale() && isset($this->columnDefinition['scale'])) {
                $parameters[] = $this->columnDefinition['scale'];
            }
        }

        // Add enum values if required
        if ($this->columnType->requiresValues() && isset($this->columnDefinition['values'])) {
            $parameters[] = $this->columnDefinition['values'];
        }

        return $parameters;
    }

    /**
     * Generate Laravel migration code for a column
     *
     * @return string
     */
    public function generateMigrationCode(): string
    {
        $result = $this->execute($this->columnDefinition);
        $method = $result['method'];
        $parameters = $result['parameters'];

        // Convert parameters to string representation
        $paramString = collect($parameters)->map(function ($param) {
            if (is_array($param)) {
                return "['" . implode("', '", $param) . "']";
            }
            return is_string($param) ? "'$param'" : $param;
        })->join(', ');

        return "\$table->$method($paramString)";
    }

    /**
     * Parse MySQL column definition
     *
     * @param string $definition
     * @return array
     */
    public static function parseMySQLDefinition(string $definition): array
    {
        $parts = explode(' ', trim($definition));
        $name = array_shift($parts);
        $type = array_shift($parts);
        
        $result = [
            'name' => trim($name, '`'),
            'type' => strtoupper($type),
        ];

        // Parse type parameters
        if (preg_match('/\(([^)]+)\)/', $type, $matches)) {
            $typeParams = $matches[1];
            $result['type'] = strtoupper(preg_replace('/\([^)]+\)/', '', $type));

            // Handle different parameter types
            if (str_contains($typeParams, ',')) {
                if (in_array($result['type'], ['FLOAT', 'DOUBLE', 'DECIMAL'])) {
                    list($result['precision'], $result['scale']) = array_map('trim', explode(',', $typeParams));
                } else {
                    $result['values'] = array_map('trim', explode(',', $typeParams));
                }
            } else {
                $result['length'] = (int) $typeParams;
            }
        }

        // Parse additional attributes
        foreach ($parts as $part) {
            if (strtoupper($part) === 'UNSIGNED') {
                $result['unsigned'] = true;
            } elseif (strtoupper($part) === 'NULL') {
                $result['nullable'] = true;
            } elseif (strtoupper($part) === 'NOT NULL') {
                $result['nullable'] = false;
            } elseif (str_starts_with(strtoupper($part), 'DEFAULT')) {
                $result['default'] = trim($part, "DEFAULT '");
            }
        }

        return $result;
    }
} 