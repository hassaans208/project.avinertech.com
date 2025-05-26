<?php

namespace Database\Seeders;

use App\Models\SqlStatement;
use Illuminate\Database\Seeder;

class SqlStatementSeeder extends Seeder
{
    public function run(): void
    {
        $statements = [
            [
                'sql_concept' => 'CREATE TABLE',
                'category' => 'Schema',
                'laravel_method' => 'Schema::create',
                'description' => 'Create a new table in the database',
                'example_sql' => 'CREATE TABLE users (id INT, name VARCHAR(255))',
                'example_laravel' => "Schema::create('users', function (Blueprint \$table) {\n    \$table->id();\n    \$table->string('name');\n});",
                'is_common' => true,
                'complexity_level' => 1,
            ],
            [
                'sql_concept' => 'ALTER TABLE',
                'category' => 'Schema', 
                'laravel_method' => 'Schema::table',
                'description' => 'Modify an existing table structure',
                'example_sql' => 'ALTER TABLE users ADD COLUMN email VARCHAR(255)',
                'example_laravel' => "Schema::table('users', function (Blueprint \$table) {\n    \$table->string('email');\n});",
                'is_common' => true,
                'complexity_level' => 2,
            ],
            [
                'sql_concept' => 'INSERT',
                'category' => 'DML',
                'laravel_method' => 'Model::create',
                'description' => 'Insert new records into a table',
                'example_sql' => "INSERT INTO users (name, email) VALUES ('John', 'john@example.com')",
                'example_laravel' => "User::create(['name' => 'John', 'email' => 'john@example.com']);",
                'is_common' => true,
                'complexity_level' => 1,
            ],
            [
                'sql_concept' => 'UPDATE',
                'category' => 'DML',
                'laravel_method' => 'Model::where()->update',
                'description' => 'Update existing records in a table',
                'example_sql' => "UPDATE users SET name = 'Jane' WHERE id = 1",
                'example_laravel' => "User::where('id', 1)->update(['name' => 'Jane']);",
                'is_common' => true,
                'complexity_level' => 2,
            ],
            [
                'sql_concept' => 'DELETE',
                'category' => 'DML',
                'laravel_method' => 'Model::where()->delete',
                'description' => 'Delete records from a table',
                'example_sql' => 'DELETE FROM users WHERE id = 1',
                'example_laravel' => "User::where('id', 1)->delete();",
                'is_common' => true,
                'complexity_level' => 1,
            ],
            [
                'sql_concept' => 'SELECT',
                'category' => 'DQL',
                'laravel_method' => 'Model::get',
                'description' => 'Retrieve records from a table',
                'example_sql' => 'SELECT * FROM users WHERE active = 1',
                'example_laravel' => "User::where('active', 1)->get();",
                'is_common' => true,
                'complexity_level' => 1,
            ],
            [
                'sql_concept' => 'WHERE',
                'category' => 'Clause',
                'laravel_method' => 'where',
                'description' => 'Filter records based on conditions',
                'example_sql' => 'SELECT * FROM users WHERE age > 18',
                'example_laravel' => "User::where('age', '>', 18)->get();",
                'is_common' => true,
                'complexity_level' => 1,
            ],
            [
                'sql_concept' => 'JOIN',
                'category' => 'Clause',
                'laravel_method' => 'join',
                'description' => 'Combine rows from multiple tables',
                'example_sql' => 'SELECT users.*, orders.* FROM users JOIN orders ON users.id = orders.user_id',
                'example_laravel' => "User::join('orders', 'users.id', '=', 'orders.user_id')->get();",
                'is_common' => true,
                'complexity_level' => 3,
            ],
            [
                'sql_concept' => 'GROUP BY',
                'category' => 'Clause',
                'laravel_method' => 'groupBy',
                'description' => 'Group rows by specified columns',
                'example_sql' => 'SELECT department, COUNT(*) FROM employees GROUP BY department',
                'example_laravel' => "Employee::groupBy('department')->selectRaw('department, COUNT(*) as count')->get();",
                'is_common' => true,
                'complexity_level' => 3,
            ],
            [
                'sql_concept' => 'HAVING',
                'category' => 'Clause',
                'laravel_method' => 'having',
                'description' => 'Filter groups after GROUP BY',
                'example_sql' => 'SELECT department, COUNT(*) FROM employees GROUP BY department HAVING COUNT(*) > 5',
                'example_laravel' => "Employee::groupBy('department')->havingRaw('COUNT(*) > ?', [5])->get();",
                'is_common' => false,
                'complexity_level' => 4,
            ],
            [
                'sql_concept' => 'TRANSACTION',
                'category' => 'Transaction',
                'laravel_method' => 'DB::transaction',
                'description' => 'Execute multiple queries in a transaction',
                'example_sql' => "BEGIN;\nINSERT INTO orders...\nUPDATE inventory...\nCOMMIT;",
                'example_laravel' => "DB::transaction(function () {\n    Order::create(...);\n    Inventory::update(...);\n});",
                'is_common' => false,
                'complexity_level' => 4,
            ],
        ];

        foreach ($statements as $statement) {
            SqlStatement::updateOrCreate(
                ['sql_concept' => $statement['sql_concept']],
                $statement
            );
        }
    }
}