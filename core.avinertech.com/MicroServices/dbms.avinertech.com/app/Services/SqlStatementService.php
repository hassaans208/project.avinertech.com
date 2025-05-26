<?php

namespace App\Services;

use App\Models\SqlStatement;
use App\Services\Abstracts\BaseService;

class SqlStatementService extends BaseService
{
    protected function validate(): void
    {
        // Add validation logic if needed
    }

    protected function process(): void
    {
        // Example: fetch all statements
        $this->result = SqlStatement::all();
    }

    public function find($id)
    {
        return SqlStatement::findOrFail($id);
    }

    public function create(array $data)
    {
        return SqlStatement::create($data);
    }

    public function update($id, array $data)
    {
        $stmt = SqlStatement::findOrFail($id);
        $stmt->update($data);
        return $stmt;
    }

    public function delete($id)
    {
        $stmt = SqlStatement::findOrFail($id);
        $stmt->delete();
        return true;
    }
} 