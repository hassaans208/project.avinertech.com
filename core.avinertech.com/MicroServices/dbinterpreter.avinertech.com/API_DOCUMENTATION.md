# Database Interpreter API Documentation

## Overview
This API provides comprehensive database management capabilities with tenant isolation, security verification, and batch operation support. All endpoints require proper authentication and signature verification.

## Base URL
```
/api/v1/database
```

## Authentication & Middleware
All endpoints use the following middleware stack:
- `SecurityParameterMiddleware` - Validates security parameters
- `SignatureVerificationMiddleware` - Verifies request signatures
- `TenantSecurityMiddleware` - Ensures tenant isolation
- `IdempotencyMiddleware` - Prevents duplicate requests

---

## Database Management APIs

### 1. Database Capabilities
**GET** `/capabilities`

Retrieves database capabilities and version information.

**Response:**
```json
{
  "status": "success",
  "message": "Capabilities retrieved successfully",
  "data": {
    "version": "8.0.35",
    "version_number": 80035,
    "capabilities": {
      "functional_indexes": true,
      "check_constraints_enforced": true,
      "invisible_indexes": true,
      "generated_columns": true,
      "json_type": true,
      "spatial_indexes": true,
      "partitioning": true,
      "deprecated_display_widths": false,
      "zerofill_attribute": false
    },
    "limits": {
      "max_index_length": 3072,
      "max_varchar_length": 65535,
      "max_decimal_precision": 65,
      "max_decimal_scale": 30,
      "max_enum_values": 65535
    },
    "engines": ["InnoDB", "MyISAM", "MEMORY"]
  }
}
```

---

## Schema Management APIs

### 2. List Schemas
**GET** `/schemas`

Retrieves all accessible schemas for the tenant.

**Response:**
```json
{
  "status": "success",
  "message": "Schemas retrieved successfully",
  "data": [
    {
      "name": "tenant_schema",
      "charset": "utf8mb4",
      "collation": "utf8mb4_unicode_ci",
      "sql_path": null
    }
  ]
}
```

### 3. Get Schema Details
**GET** `/schema`

Retrieves detailed information about a specific schema including its tables.

**Response:**
```json
{
  "status": "success",
  "message": "Schema retrieved successfully",
  "data": {
    "schema": {
      "name": "tenant_schema",
      "charset": "utf8mb4",
      "collation": "utf8mb4_unicode_ci"
    },
    "tables": [
      {
        "name": "users",
        "type": "BASE TABLE",
        "engine": "InnoDB",
        "rows": 1000,
        "avg_row_length": 1024,
        "data_length": 1048576,
        "index_length": 262144,
        "collation": "utf8mb4_unicode_ci",
        "created_at": "2024-01-01 00:00:00",
        "updated_at": "2024-01-01 12:00:00",
        "comment": "User accounts table"
      }
    ],
    "table_count": 1
  }
}
```

---

## Table Management APIs

### 4. List Tables
**GET** `/tables`

Retrieves all tables in the tenant's schema.

**Response:**
```json
{
  "status": "success",
  "message": "Tables retrieved successfully",
  "data": [
    {
      "name": "users",
      "type": "BASE TABLE",
      "engine": "InnoDB",
      "rows": 1000,
      "avg_row_length": 1024,
      "data_length": 1048576,
      "index_length": 262144,
      "collation": "utf8mb4_unicode_ci",
      "created_at": "2024-01-01 00:00:00",
      "updated_at": "2024-01-01 12:00:00",
      "comment": "User accounts table"
    }
  ]
}
```

### 5. Get Table Details
**GET** `/tables/{tableName}`

Retrieves detailed information about a specific table including columns and indexes.

**Response:**
```json
{
  "status": "success",
  "message": "Table retrieved successfully",
  "data": {
    "table": {
      "name": "users",
      "type": "BASE TABLE",
      "engine": "InnoDB",
      "rows": 1000,
      "avg_row_length": 1024,
      "data_length": 1048576,
      "index_length": 262144,
      "collation": "utf8mb4_unicode_ci",
      "created_at": "2024-01-01 00:00:00",
      "updated_at": "2024-01-01 12:00:00",
      "comment": "User accounts table"
    },
    "columns": [
      {
        "name": "id",
        "position": 1,
        "default_value": null,
        "nullable": "NO",
        "data_type": "int",
        "max_length": null,
        "precision": 10,
        "scale": 0,
        "datetime_precision": null,
        "charset": null,
        "collation": null,
        "column_type": "int(11)",
        "key": "PRI",
        "extra": "auto_increment",
        "privileges": "select,insert,update,references",
        "comment": "Primary key"
      }
    ],
    "indexes": [
      {
        "name": "PRIMARY",
        "non_unique": 0,
        "type": "BTREE",
        "column_name": "id",
        "sequence": 1
      }
    ],
    "column_count": 1,
    "index_count": 1
  }
}
```

### 6. Create Table (Batch Operation)
**POST** `/tables`

Creates a new table operation (requires admin approval).

**Request Body:**
```json
{
  "name": "new_table",
  "case_id": 1,
  "columns": [
    {
      "name": "id",
      "type": "INT(11)",
      "nullable": "NO",
      "auto_increment": true,
      "primary_key": true
    },
    {
      "name": "name",
      "type": "VARCHAR(255)",
      "nullable": "NO",
      "default": ""
    }
  ],
  "engine": "InnoDB",
  "charset": "utf8mb4"
}
```

**Response:**
```json
{
  "status": "success",
  "message": "Table creation requested",
  "data": {
    "operation_name": "CREATE_TABLE_new_table_001",
    "sql_preview": "CREATE TABLE `tenant_schema`.`new_table` (\n  `id` INT(11) NOT NULL AUTO_INCREMENT PRIMARY KEY,\n  `name` VARCHAR(255) NOT NULL DEFAULT ''\n) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4",
    "status": "draft"
  }
}
```

---

## Data Management APIs (Instant Operations)

### 7. Get Table Data
**GET** `/tables/{tableName}/data`

Retrieves data from a table with optional filtering.

**Query Parameters:**
- `limit` (optional): Maximum number of records (default: 100, max: 1000)
- `offset` (optional): Number of records to skip (default: 0)
- `filters` (optional): Array of filter objects

**Filter Object:**
```json
{
  "column": "status",
  "operator": "equals",
  "value": "active"
}
```

**Available Operators:**
- `equals`, `not_equals`, `greater_than`, `less_than`
- `like`, `in`, `between`, `is_null`, `is_not_null`

**Response:**
```json
{
  "status": "success",
  "message": "Data retrieved successfully",
  "data": {
    "results": [
      {
        "id": 1,
        "name": "John Doe",
        "email": "john@example.com",
        "created_at": "2024-01-01 00:00:00"
      }
    ],
    "count": 1,
    "limit": 100,
    "offset": 0,
    "limited": false
  }
}
```

### 8. Insert Data
**POST** `/tables/{tableName}/data`

Inserts new data into a table.

**Request Body:**
```json
{
  "data": {
    "name": "Jane Doe",
    "email": "jane@example.com",
    "status": "active"
  }
}
```

**Response:**
```json
{
  "status": "success",
  "message": "Data inserted successfully",
  "data": {
    "id": 2,
    "inserted_data": {
      "name": "Jane Doe",
      "email": "jane@example.com",
      "status": "active"
    }
  }
}
```

### 9. Update Data
**PATCH** `/tables/{tableName}/data/{rowId}`

Updates existing data in a table.

**Request Body:**
```json
{
  "data": {
    "name": "Jane Smith",
    "status": "inactive"
  }
}
```

**Response:**
```json
{
  "status": "success",
  "message": "Data updated successfully",
  "data": {
    "id": "2",
    "updated_data": {
      "name": "Jane Smith",
      "status": "inactive"
    }
  }
}
```

### 10. Delete Data (Soft Delete)
**DELETE** `/tables/{tableName}/data/{rowId}`

Performs a soft delete on a record.

**Response:**
```json
{
  "status": "success",
  "message": "Data deleted successfully",
  "data": {
    "id": "2",
    "soft_deleted": true
  }
}
```

---

## Soft Delete Management APIs

### 11. Get Soft Deleted Records
**GET** `/tables/{tableName}/soft-deleted`

Retrieves all soft-deleted records from a table.

**Response:**
```json
{
  "status": "success",
  "message": "Soft deleted records retrieved successfully",
  "data": [
    {
      "id": 2,
      "name": "Jane Smith",
      "email": "jane@example.com",
      "deleted_at": "2024-01-01 12:00:00",
      "deleted_by": "tenant_123"
    }
  ]
}
```

### 12. Recover Record
**POST** `/tables/{tableName}/soft-deleted/{recordId}/recover`

Recovers a soft-deleted record.

**Response:**
```json
{
  "status": "success",
  "message": "Record recovered successfully",
  "data": {
    "id": "2",
    "recovered": true
  }
}
```

### 13. Permanently Delete Record
**DELETE** `/tables/{tableName}/soft-deleted/{recordId}/permanent`

Permanently deletes a soft-deleted record.

**Response:**
```json
{
  "status": "success",
  "message": "Record permanently deleted successfully",
  "data": {
    "id": "2",
    "permanently_deleted": true
  }
}
```

---

## Column Management APIs (Batch Operations)

### 14. Add Column
**POST** `/tables/{tableName}/columns`

Adds a new column to a table (requires admin approval).

**Request Body:**
```json
{
  "column": {
    "name": "phone",
    "type": "VARCHAR(20)",
    "nullable": "YES",
    "default": null
  },
  "case_id": 1
}
```

**Response:**
```json
{
  "status": "success",
  "message": "Operation added to batch",
  "data": {
    "operation_id": 123,
    "group_id": 456,
    "operation_name": "ALTER_TABLE_users_001",
    "status": "draft",
    "sql_preview": "ALTER TABLE `users` ADD COLUMN `phone` VARCHAR(20)"
  }
}
```

### 15. Update Column
**PATCH** `/tables/{tableName}/columns/{columnName}`

Modifies an existing column (requires admin approval).

**Request Body:**
```json
{
  "column": {
    "type": "VARCHAR(50)",
    "nullable": "NO",
    "default": ""
  },
  "case_id": 1
}
```

**Response:**
```json
{
  "status": "success",
  "message": "Operation added to batch",
  "data": {
    "operation_id": 124,
    "group_id": 456,
    "operation_name": "ALTER_TABLE_users_002",
    "status": "draft",
    "sql_preview": "ALTER TABLE `users` MODIFY COLUMN `phone` VARCHAR(50)"
  }
}
```

### 16. Delete Column
**DELETE** `/tables/{tableName}/columns/{columnName}`

Removes a column from a table (requires admin approval).

**Request Body:**
```json
{
  "case_id": 1
}
```

**Response:**
```json
{
  "status": "success",
  "message": "Operation added to batch",
  "data": {
    "operation_id": 125,
    "group_id": 456,
    "operation_name": "ALTER_TABLE_users_003",
    "status": "draft",
    "sql_preview": "ALTER TABLE `users` DROP COLUMN `phone`"
  }
}
```

---

## Index Management APIs (Batch Operations)

### 17. Add Index
**POST** `/tables/{tableName}/indexes`

Creates a new index on a table (requires admin approval).

**Request Body:**
```json
{
  "index": {
    "name": "idx_email",
    "columns": ["email"],
    "type": "UNIQUE"
  },
  "case_id": 1
}
```

**Response:**
```json
{
  "status": "success",
  "message": "Operation added to batch",
  "data": {
    "operation_id": 126,
    "group_id": 457,
    "operation_name": "CREATE_INDEX_users_001",
    "status": "draft",
    "sql_preview": "CREATE UNIQUE `idx_email` ON `users` (`email`)"
  }
}
```

### 18. Delete Index
**DELETE** `/tables/{tableName}/indexes/{indexName}`

Removes an index from a table (requires admin approval).

**Request Body:**
```json
{
  "case_id": 1
}
```

**Response:**
```json
{
  "status": "success",
  "message": "Operation added to batch",
  "data": {
    "operation_id": 127,
    "group_id": 457,
    "operation_name": "DROP_INDEX_users_001",
    "status": "draft",
    "sql_preview": "DROP INDEX `idx_email` ON `users`"
  }
}
```

---

## Metadata APIs

### 19. Get Filter Operators
**GET** `/metadata/filters`

Retrieves all available filter operators for query building.

**Response:**
```json
{
  "status": "success",
  "message": "Filters retrieved successfully",
  "data": [
    {
      "name": "equals",
      "label": "Equals",
      "description": "Exact match",
      "data_types": ["string", "number", "date", "boolean"]
    },
    {
      "name": "not_equals",
      "label": "Not Equals",
      "description": "Not exact match",
      "data_types": ["string", "number", "date", "boolean"]
    }
  ]
}
```

### 20. Get Aggregation Functions
**GET** `/metadata/aggregations`

Retrieves all available aggregation functions.

**Response:**
```json
{
  "status": "success",
  "message": "Aggregations retrieved successfully",
  "data": [
    {
      "name": "COUNT",
      "label": "Count",
      "description": "Count number of records",
      "return_type": "number"
    },
    {
      "name": "SUM",
      "label": "Sum",
      "description": "Sum of numeric values",
      "return_type": "number"
    }
  ]
}
```

### 21. Get All Columns
**GET** `/metadata/columns`

Retrieves all columns across all tables for the tenant.

**Response:**
```json
{
  "status": "success",
  "message": "Columns retrieved successfully",
  "data": [
    {
      "table_name": "users",
      "column_name": "id",
      "data_type": "int",
      "nullable": "NO",
      "default_value": null,
      "column_type": "int(11)"
    }
  ]
}
```

---

## Raw Query API

### 22. Execute Raw Query
**POST** `/raw-query`

Executes validated raw SELECT queries with strict security validation.

**Request Body:**
```json
{
  "query": "SELECT id, name FROM users WHERE status = 'active' LIMIT 10"
}
```

**Response:**
```json
{
  "status": "success",
  "message": "Query executed successfully",
  "data": {
    "results": [
      {
        "id": 1,
        "name": "John Doe"
      }
    ],
    "execution_time": "0.05s",
    "rows_affected": 1
  }
}
```

---

## SQL Preview API

### 23. Preview SQL
**POST** `/preview-sql`

Validates and previews SQL without execution.

**Request Body:**
```json
{
  "sql": "SELECT * FROM users WHERE id = 1"
}
```

**Response:**
```json
{
  "status": "success",
  "message": "SQL preview generated",
  "data": {
    "sql": "SELECT * FROM users WHERE id = 1",
    "valid": true,
    "preview_only": true
  }
}
```

---

## Operation Management APIs

### 24. Create Operation
**POST** `/operations`

Creates a new database operation.

**Request Body:**
```json
{
  "type": "CREATE_TABLE",
  "table_name": "products",
  "case_id": 1,
  "payload": {
    "columns": [
      {
        "name": "id",
        "type": "INT(11)",
        "nullable": "NO",
        "auto_increment": true,
        "primary_key": true
      }
    ]
  }
}
```

**Response:**
```json
{
  "status": "success",
  "message": "Operation created successfully",
  "data": {
    "operation_id": 128,
    "operation_name": "CREATE_TABLE_products_001",
    "status": "draft"
  }
}
```

### 25. Get Operation Details
**GET** `/operations/{operationId}`

Retrieves details of a specific operation.

**Response:**
```json
{
  "status": "success",
  "message": "Operation retrieved successfully",
  "data": {
    "id": 128,
    "name": "CREATE_TABLE_products_001",
    "type": "CREATE_TABLE",
    "table_name": "products",
    "status": "draft",
    "sql_preview": "CREATE TABLE `products` (\n  `id` INT(11) NOT NULL AUTO_INCREMENT PRIMARY KEY\n)",
    "result_message": null,
    "result_data": null,
    "executed_at": null,
    "created_at": "2024-01-01 12:00:00",
    "updated_at": "2024-01-01 12:00:00"
  }
}
```

### 26. List Operations
**GET** `/operations`

Retrieves a list of operations with optional filtering.

**Query Parameters:**
- `status` (optional): Filter by status (draft, pending_approval, approved, etc.)
- `limit` (optional): Maximum number of records (default: 50, max: 100)
- `offset` (optional): Number of records to skip (default: 0)

**Response:**
```json
{
  "status": "success",
  "message": "Operations retrieved successfully",
  "data": {
    "operations": [
      {
        "id": 128,
        "name": "CREATE_TABLE_products_001",
        "type": "CREATE_TABLE",
        "table_name": "products",
        "status": "draft",
        "created_at": "2024-01-01 12:00:00"
      }
    ],
    "total": 1,
    "limit": 50,
    "offset": 0
  }
}
```

---

## Operation Group Management APIs

### 27. Get Operation Groups
**GET** `/operation-groups`

Retrieves a list of operation groups.

**Query Parameters:**
- `status` (optional): Filter by status
- `limit` (optional): Maximum number of records (default: 50, max: 100)
- `offset` (optional): Number of records to skip (default: 0)

**Response:**
```json
{
  "status": "success",
  "message": "Operation groups retrieved successfully",
  "data": {
    "groups": [
      {
        "id": 456,
        "name": "BATCH_ALTER_TABLE_USERS_20240101120000",
        "status": "draft",
        "description": "Batch operations for ALTER_TABLE on table users",
        "created_at": "2024-01-01 12:00:00"
      }
    ],
    "total": 1,
    "limit": 50,
    "offset": 0
  }
}
```

### 28. Get Operation Group Details
**GET** `/operation-groups/{groupId}`

Retrieves details of a specific operation group including its operations.

**Response:**
```json
{
  "status": "success",
  "message": "Operation group retrieved successfully",
  "data": {
    "group": {
      "id": 456,
      "name": "BATCH_ALTER_TABLE_USERS_20240101120000",
      "status": "draft",
      "description": "Batch operations for ALTER_TABLE on table users",
      "created_at": "2024-01-01 12:00:00"
    },
    "operations": [
      {
        "id": 123,
        "name": "ALTER_TABLE_users_001",
        "type": "ALTER_TABLE",
        "status": "draft",
        "sql_preview": "ALTER TABLE `users` ADD COLUMN `phone` VARCHAR(20)"
      }
    ],
    "operation_count": 1
  }
}
```

### 29. Request Batch Approval
**POST** `/operation-groups/{groupId}/request-approval`

Requests admin approval for a batch of operations.

**Request Body:**
```json
{
  "description": "Adding phone column to users table for contact information"
}
```

**Response:**
```json
{
  "status": "success",
  "message": "Batch approval requested",
  "data": {
    "group_id": 456,
    "status": "pending_approval",
    "approval_requested_at": "2024-01-01 12:30:00"
  }
}
```

---

## Admin Management APIs

### 30. Approve Batch
**POST** `/operation-groups/{groupId}/approve`

Approves a batch of operations for execution.

**Request Body:**
```json
{
  "admin_notes": "Approved for production deployment"
}
```

**Response:**
```json
{
  "status": "success",
  "message": "Batch approved successfully",
  "data": {
    "group_id": 456,
    "status": "approved",
    "approved_at": "2024-01-01 13:00:00",
    "queued_for_execution": true
  }
}
```

### 31. Reject Batch
**POST** `/operation-groups/{groupId}/reject`

Rejects a batch of operations.

**Request Body:**
```json
{
  "admin_notes": "Column type incompatible with existing data"
}
```

**Response:**
```json
{
  "status": "success",
  "message": "Batch rejected successfully",
  "data": {
    "group_id": 456,
    "status": "rejected",
    "rejected_at": "2024-01-01 13:00:00"
  }
}
```

### 32. Get Pending Batches
**GET** `/operation-groups/pending`

Retrieves all batches pending admin approval.

**Query Parameters:**
- `limit` (optional): Maximum number of records (default: 50, max: 100)
- `offset` (optional): Number of records to skip (default: 0)

**Response:**
```json
{
  "status": "success",
  "message": "Pending batches retrieved successfully",
  "data": {
    "groups": [
      {
        "id": 456,
        "name": "BATCH_ALTER_TABLE_USERS_20240101120000",
        "status": "pending_approval",
        "description": "Adding phone column to users table",
        "approval_requested_at": "2024-01-01 12:30:00",
        "operation_count": 1
      }
    ],
    "total": 1,
    "limit": 50,
    "offset": 0
  }
}
```

---

## Tenant Management APIs

### 33. Get Tenant Security Logs
**GET** `/tenants/{tenantId}/security-logs`

Retrieves security logs for a specific tenant.

**Query Parameters:**
- `limit` (optional): Maximum number of records (default: 50, max: 100)
- `offset` (optional): Number of records to skip (default: 0)

**Response:**
```json
{
  "status": "success",
  "message": "Tenant security logs retrieved successfully",
  "data": {
    "logs": [
      {
        "id": 1,
        "tenant_id": "tenant_123",
        "action": "SIGNATURE_VERIFICATION_FAILED",
        "ip_address": "192.168.1.100",
        "user_agent": "Mozilla/5.0...",
        "created_at": "2024-01-01 12:00:00"
      }
    ],
    "total": 1,
    "limit": 50,
    "offset": 0
  }
}
```

### 34. Unblock Tenant
**POST** `/tenants/{tenantId}/unblock`

Unblocks a previously blocked tenant.

**Request Body:**
```json
{
  "reason": "Security issue resolved"
}
```

**Response:**
```json
{
  "status": "success",
  "message": "Tenant unblocked successfully",
  "data": {
    "tenant_id": "tenant_123",
    "unblocked_at": "2024-01-01 13:00:00",
    "reason": "Security issue resolved"
  }
}
```

### 35. Get Blocked Tenants
**GET** `/tenants/blocked`

Retrieves all currently blocked tenants.

**Query Parameters:**
- `limit` (optional): Maximum number of records (default: 50, max: 100)
- `offset` (optional): Number of records to skip (default: 0)

**Response:**
```json
{
  "status": "success",
  "message": "Blocked tenants retrieved successfully",
  "data": {
    "tenants": [
      {
        "id": "tenant_456",
        "name": "Blocked Tenant",
        "blocked_at": "2024-01-01 10:00:00",
        "block_reason": "Multiple failed signature verifications"
      }
    ],
    "total": 1,
    "limit": 50,
    "offset": 0
  }
}
```

---

## System Monitoring APIs

### 36. Get Operation Statistics
**GET** `/operations/stats`

Retrieves system-wide operation statistics.

**Response:**
```json
{
  "status": "success",
  "message": "Operation stats retrieved successfully",
  "data": {
    "total_operations": 1000,
    "pending_operations": 50,
    "queued_operations": 25,
    "running_operations": 5,
    "completed_operations": 900,
    "failed_operations": 20,
    "total_batches": 200,
    "pending_batches": 10,
    "approved_batches": 15,
    "rejected_batches": 5,
    "completed_batches": 160,
    "failed_batches": 10
  }
}
```

### 37. Get System Health
**GET** `/system/health`

Retrieves overall system health status.

**Response:**
```json
{
  "status": "success",
  "message": "System health retrieved successfully",
  "data": {
    "overall_status": "healthy",
    "health_checks": {
      "database_connection": true,
      "queue_status": true,
      "cache_status": true,
      "disk_space": true,
      "memory_usage": true,
      "uptime": "2d 5h 30m",
      "timestamp": "2024-01-01 13:00:00"
    }
  }
}
```

---

## Error Responses

All endpoints return standardized error responses:

```json
{
  "status": "error",
  "message": "Error description",
  "error": {
    "code": "ERROR_CODE",
    "details": "Detailed error information"
  },
  "timestamp": "2024-01-01T13:00:00Z",
  "request_id": "req_123456789"
}
```

## Common Error Codes

- `CAPABILITY_PROBE_FAILED` - Database capability check failed
- `SCHEMA_NOT_FOUND` - Requested schema does not exist
- `TABLE_NOT_FOUND` - Requested table does not exist
- `OPERATION_CREATION_FAILED` - Failed to create operation
- `BATCH_APPROVAL_FAILED` - Batch approval process failed
- `QUERY_EXECUTION_FAILED` - Raw query execution failed
- `RECORD_NOT_FOUND` - Requested record does not exist
- `TENANT_SECURITY_LOGS_RETRIEVAL_FAILED` - Security logs retrieval failed

## Rate Limiting

All endpoints are subject to rate limiting based on tenant and operation type. Rate limits are enforced per tenant to ensure fair usage across the system.

## Security Notes

1. All requests must include proper signature verification
2. Tenant isolation is enforced at the database level
3. DDL operations require admin approval before execution
4. Raw queries are restricted to SELECT operations only
5. All data modifications are logged for audit purposes
