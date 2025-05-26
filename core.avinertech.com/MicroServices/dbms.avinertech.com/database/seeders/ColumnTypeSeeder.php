<?php

namespace Database\Seeders;

use App\Models\ColumnType;
use Illuminate\Database\Seeder;

class ColumnTypeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run(): void
    {
        $types = [
            [
                'mysql_type' => 'CHAR',
                'laravel_method' => 'char',
                'description' => 'Fixed length string',
                'requires_length' => true,
                'requires_precision' => false,
                'requires_scale' => false,
                'requires_values' => false,
            ],
            [
                'mysql_type' => 'VARCHAR',
                'laravel_method' => 'string',
                'description' => 'Variable length string',
                'requires_length' => true,
                'requires_precision' => false,
                'requires_scale' => false,
                'requires_values' => false,
            ],
            [
                'mysql_type' => 'TEXT',
                'laravel_method' => 'text',
                'description' => 'Text up to 65,535 bytes',
                'requires_length' => false,
                'requires_precision' => false,
                'requires_scale' => false,
                'requires_values' => false,
            ],
            [
                'mysql_type' => 'MEDIUMTEXT',
                'laravel_method' => 'mediumText',
                'description' => 'Medium text',
                'requires_length' => false,
                'requires_precision' => false,
                'requires_scale' => false,
                'requires_values' => false,
            ],
            [
                'mysql_type' => 'LONGTEXT',
                'laravel_method' => 'longText',
                'description' => 'Long text',
                'requires_length' => false,
                'requires_precision' => false,
                'requires_scale' => false,
                'requires_values' => false,
            ],
            [
                'mysql_type' => 'BLOB',
                'laravel_method' => 'binary',
                'description' => 'Binary data',
                'requires_length' => false,
                'requires_precision' => false,
                'requires_scale' => false,
                'requires_values' => false,
            ],
            [
                'mysql_type' => 'ENUM',
                'laravel_method' => 'enum',
                'description' => 'Enumerated values',
                'requires_length' => false,
                'requires_precision' => false,
                'requires_scale' => false,
                'requires_values' => true,
            ],
            [
                'mysql_type' => 'TINYINT',
                'laravel_method' => 'tinyInteger',
                'description' => 'Small integer',
                'requires_length' => false,
                'requires_precision' => false,
                'requires_scale' => false,
                'requires_values' => false,
            ],
            [
                'mysql_type' => 'SMALLINT',
                'laravel_method' => 'smallInteger',
                'description' => 'Small integer',
                'requires_length' => false,
                'requires_precision' => false,
                'requires_scale' => false,
                'requires_values' => false,
            ],
            [
                'mysql_type' => 'MEDIUMINT',
                'laravel_method' => 'mediumInteger',
                'description' => 'Medium integer',
                'requires_length' => false,
                'requires_precision' => false,
                'requires_scale' => false,
                'requires_values' => false,
            ],
            [
                'mysql_type' => 'INT',
                'laravel_method' => 'integer',
                'description' => 'Standard integer',
                'requires_length' => false,
                'requires_precision' => false,
                'requires_scale' => false,
                'requires_values' => false,
            ],
            [
                'mysql_type' => 'BIGINT',
                'laravel_method' => 'bigInteger',
                'description' => 'Large integer',
                'requires_length' => false,
                'requires_precision' => false,
                'requires_scale' => false,
                'requires_values' => false,
            ],
            [
                'mysql_type' => 'FLOAT',
                'laravel_method' => 'float',
                'description' => 'Floating point',
                'requires_length' => false,
                'requires_precision' => true,
                'requires_scale' => true,
                'requires_values' => false,
            ],
            [
                'mysql_type' => 'DOUBLE',
                'laravel_method' => 'double',
                'description' => 'Double precision float',
                'requires_length' => false,
                'requires_precision' => true,
                'requires_scale' => true,
                'requires_values' => false,
            ],
            [
                'mysql_type' => 'DECIMAL',
                'laravel_method' => 'decimal',
                'description' => 'Fixed-point decimal',
                'requires_length' => false,
                'requires_precision' => true,
                'requires_scale' => true,
                'requires_values' => false,
            ],
            [
                'mysql_type' => 'DATE',
                'laravel_method' => 'date',
                'description' => 'Date only',
                'requires_length' => false,
                'requires_precision' => false,
                'requires_scale' => false,
                'requires_values' => false,
            ],
            [
                'mysql_type' => 'DATETIME',
                'laravel_method' => 'dateTime',
                'description' => 'Date and time',
                'requires_length' => false,
                'requires_precision' => false,
                'requires_scale' => false,
                'requires_values' => false,
            ],
            [
                'mysql_type' => 'TIMESTAMP',
                'laravel_method' => 'timestamp',
                'description' => 'Timestamp',
                'requires_length' => false,
                'requires_precision' => false,
                'requires_scale' => false,
                'requires_values' => false,
            ],
            [
                'mysql_type' => 'TIME',
                'laravel_method' => 'time',
                'description' => 'Time only',
                'requires_length' => false,
                'requires_precision' => false,
                'requires_scale' => false,
                'requires_values' => false,
            ],
            [
                'mysql_type' => 'YEAR',
                'laravel_method' => 'year',
                'description' => 'Year only',
                'requires_length' => false,
                'requires_precision' => false,
                'requires_scale' => false,
                'requires_values' => false,
            ],
            [
                'mysql_type' => 'JSON',
                'laravel_method' => 'json',
                'description' => 'JSON data',
                'requires_length' => false,
                'requires_precision' => false,
                'requires_scale' => false,
                'requires_values' => false,
            ],
            [
                'mysql_type' => 'BOOLEAN',
                'laravel_method' => 'boolean',
                'description' => 'Boolean',
                'requires_length' => false,
                'requires_precision' => false,
                'requires_scale' => false,
                'requires_values' => false,
            ],
        ];

        foreach ($types as $type) {
            ColumnType::updateOrCreate(
                ['mysql_type' => $type['mysql_type']],
                $type
            );
        }
    }
} 