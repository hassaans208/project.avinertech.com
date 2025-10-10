# Database Interpreter API - Payload Structures Documentation

This document provides a comprehensive overview of all payload structures used throughout the Database Interpreter system for various operations.

## Table of Contents

1. [Operation Payload Structure](#operation-payload-structure)
2. [Batch Operations (DDL)](#batch-operations-ddl)
3. [Instant Operations (DML)](#instant-operations-dml)
4. [Raw Query Operations](#raw-query-operations)
5. [Metadata Operations](#metadata-operations)
6. [Admin Operations](#admin-operations)
7. [System Operations](#system-operations)

---

## Operation Payload Structure

### Base Operation Structure
All operations follow this base structure when stored in the database:

```json
{
  "tenant_id": "string (UUID)",
  "case_id": "integer",
  "type": "string (operation type)",
  "name": "string (auto-generated)",
  "schema_name": "string (from signature)",
  "table_name": "string (nullable)",
  "payload": "object (operation-specific data)",
  "sql_preview": "string",
  "status": "enum (draft|pending_approval|queued|running|success|failed|cancelled)",
  "execution_order": "integer (default: 0)",
  "result": "object (nullable)",
  "error_message": "string (nullable)",
  "started_at": "timestamp (nullable)",
  "completed_at": "timestamp (nullable)"
}
```

---

## Batch Operations (DDL)

These operations require admin approval and are processed in batches.

### 1. CREATE_TABLE

**Operation Type**: `CREATE_TABLE`

**Payload Structure**:
```json
{
  "name": "string (table name)",
  "columns": [
    {
      "name": "string",
      "type": "string (e.g., VARCHAR(255), INT, BIGINT)",
      "nullable": "boolean|string (true/false or 'YES'/'NO')",
      "default": "string|number|null",
      "auto_increment": "boolean",
      "primary_key": "boolean"
    }
  ],
  "engine": "string (optional, e.g., 'InnoDB')",
  "charset": "string (optional, e.g., 'utf8mb4')"
}
```

**Example**:
```json
{
  "type": "CREATE_TABLE",
  "table_name": "products",
  "case_id": 1,
  "payload": {
    "name": "products",
    "columns": [
      {
        "name": "id",
        "type": "BIGINT",
        "auto_increment": true,
        "primary_key": true
      },
      {
        "name": "name",
        "type": "VARCHAR(255)",
        "nullable": false
      },
      {
        "name": "price",
        "type": "DECIMAL(10,2)",
        "nullable": false,
        "default": 0.00
      }
    ],
    "engine": "InnoDB",
    "charset": "utf8mb4"
  }
}
```

### 2. ALTER_TABLE

**Operation Type**: `ALTER_TABLE`

#### 2.1 Add Column
**Payload Structure**:
```json
{
  "add_column": {
    "name": "string",
    "type": "string",
    "nullable": "boolean|string",
    "default": "string|number|null",
    "auto_increment": "boolean"
  }
}
```

**Example**:
```json
{
  "type": "ALTER_TABLE",
  "table_name": "users",
  "case_id": 2,
  "payload": {
    "add_column": {
      "name": "phone",
      "type": "VARCHAR(20)",
      "nullable": true,
      "default": null
    }
  }
}
```

#### 2.2 Modify Column
**Payload Structure**:
```json
{
  "modify_column": {
    "name": "string",
    "type": "string",
    "nullable": "boolean|string",
    "default": "string|number|null",
    "auto_increment": "boolean"
  }
}
```

**Example**:
```json
{
  "type": "ALTER_TABLE",
  "table_name": "users",
  "case_id": 2,
  "payload": {
    "modify_column": {
      "name": "email",
      "type": "VARCHAR(320)",
      "nullable": false
    }
  }
}
```

#### 2.3 Drop Column
**Payload Structure**:
```json
{
  "drop_column": {
    "name": "string"
  }
}
```

**Example**:
```json
{
  "type": "ALTER_TABLE",
  "table_name": "users",
  "case_id": 2,
  "payload": {
    "drop_column": {
      "name": "old_field"
    }
  }
}
```

### 3. DROP_TABLE

**Operation Type**: `DROP_TABLE`

**Payload Structure**:
```json
{
  "table_name": "string"
}
```

**Example**:
```json
{
  "type": "DROP_TABLE",
  "table_name": "old_table",
  "case_id": 1,
  "payload": {
    "table_name": "old_table"
  }
}
```

### 4. CREATE_INDEX

**Operation Type**: `CREATE_INDEX`

**Payload Structure**:
```json
{
  "name": "string",
  "columns": ["string", "string", ...],
  "type": "string (INDEX|UNIQUE|FULLTEXT|SPATIAL)"
}
```

**Example**:
```json
{
  "type": "CREATE_INDEX",
  "table_name": "users",
  "case_id": 2,
  "payload": {
    "name": "idx_email",
    "columns": ["email"],
    "type": "UNIQUE"
  }
}
```

**Composite Index Example**:
```json
{
  "type": "CREATE_INDEX",
  "table_name": "users",
  "case_id": 2,
  "payload": {
    "name": "idx_name_email",
    "columns": ["name", "email"],
    "type": "INDEX"
  }
}
```

### 5. DROP_INDEX

**Operation Type**: `DROP_INDEX`

**Payload Structure**:
```json
{
  "index_name": "string"
}
```

**Example**:
```json
{
  "type": "DROP_INDEX",
  "table_name": "users",
  "case_id": 2,
  "payload": {
    "index_name": "idx_email"
  }
}
```

### 6. ADD_FOREIGN_KEY

**Operation Type**: `ADD_FOREIGN_KEY`

**Payload Structure**:
```json
{
  "name": "string (constraint name)",
  "column": "string (local column)",
  "referenced_table": "string",
  "referenced_column": "string",
  "on_delete": "string (optional: CASCADE|SET NULL|RESTRICT)",
  "on_update": "string (optional: CASCADE|SET NULL|RESTRICT)"
}
```

**Example**:
```json
{
  "type": "ADD_FOREIGN_KEY",
  "table_name": "orders",
  "case_id": 2,
  "payload": {
    "name": "fk_orders_user_id",
    "column": "user_id",
    "referenced_table": "users",
    "referenced_column": "id",
    "on_delete": "CASCADE",
    "on_update": "RESTRICT"
  }
}
```

### 7. DROP_FOREIGN_KEY

**Operation Type**: `DROP_FOREIGN_KEY`

**Payload Structure**:
```json
{
  "constraint_name": "string"
}
```

**Example**:
```json
{
  "type": "DROP_FOREIGN_KEY",
  "table_name": "orders",
  "case_id": 2,
  "payload": {
    "constraint_name": "fk_orders_user_id"
  }
}
```

### 8. Placeholder Operations (Not Yet Implemented)

The following operations are defined in the API but return placeholder responses:

#### 8.1 ADD_CHECK
**Operation Type**: `ADD_CHECK`
**Status**: Placeholder
**Expected Payload**:
```json
{
  "name": "string (constraint name)",
  "condition": "string (SQL condition)"
}
```

#### 8.2 UPDATE_CHECK
**Operation Type**: `UPDATE_CHECK`
**Status**: Placeholder
**Expected Payload**:
```json
{
  "name": "string (constraint name)",
  "condition": "string (new SQL condition)"
}
```

#### 8.3 DELETE_CHECK
**Operation Type**: `DELETE_CHECK`
**Status**: Placeholder
**Expected Payload**:
```json
{
  "constraint_name": "string"
}
```

#### 8.4 ENABLE_PARTITIONING
**Operation Type**: `ENABLE_PARTITIONING`
**Status**: Placeholder
**Expected Payload**:
```json
{
  "partition_type": "string (RANGE|LIST|HASH|KEY)",
  "partition_expression": "string"
}
```

#### 8.5 ADD_PARTITION
**Operation Type**: `ADD_PARTITION`
**Status**: Placeholder
**Expected Payload**:
```json
{
  "partition_name": "string",
  "partition_definition": "string"
}
```

#### 8.6 REORGANIZE_PARTITIONS
**Operation Type**: `REORGANIZE_PARTITIONS`
**Status**: Placeholder
**Expected Payload**:
```json
{
  "partitions": ["string", "string", ...],
  "new_partitions": [
    {
      "name": "string",
      "definition": "string"
    }
  ]
}
```

#### 8.7 UPDATE_INDEX
**Operation Type**: `UPDATE_INDEX`
**Status**: Placeholder
**Expected Payload**:
```json
{
  "name": "string",
  "columns": ["string", "string", ...],
  "type": "string"
}
```

#### 8.8 UPDATE_FOREIGN_KEY
**Operation Type**: `UPDATE_FOREIGN_KEY`
**Status**: Placeholder
**Expected Payload**:
```json
{
  "name": "string",
  "column": "string",
  "referenced_table": "string",
  "referenced_column": "string",
  "on_delete": "string",
  "on_update": "string"
}
```

#### 8.9 UPDATE_TABLE
**Operation Type**: `UPDATE_TABLE`
**Status**: Placeholder
**Expected Payload**:
```json
{
  "changes": {
    "engine": "string (optional)",
    "charset": "string (optional)",
    "collation": "string (optional)"
  }
}
```

---

## Instant Operations (DML)

These operations execute immediately without requiring approval.

### 1. GET_DATA

**Request Structure**:
```json
{
  "filters": [
    {
      "column": "string",
      "operator": "string (equals|not_equals|greater_than|less_than|like|in|between)",
      "value": "string|number|array",
      "logical": "string (AND|OR, optional)"
    }
  ],
  "limit": "integer (optional, default: 100)",
  "offset": "integer (optional, default: 0)",
  "order_by": [
    {
      "column": "string",
      "direction": "string (ASC|DESC)"
    }
  ],
  "aggregations": [
    {
      "function": "string (COUNT|SUM|AVG|MIN|MAX)",
      "column": "string",
      "alias": "string"
    }
  ]
}
```

**Example**:
```json
{
  "filters": [
    {
      "column": "status",
      "operator": "equals",
      "value": "active"
    }
  ],
  "limit": 50,
  "offset": 0,
  "order_by": [
    {
      "column": "created_at",
      "direction": "DESC"
    }
  ]
}
```

### 2. INSERT_DATA

**Request Structure**:
```json
{
  "data": {
    "column1": "value1",
    "column2": "value2",
    ...
  }
}
```

**Example**:
```json
{
  "data": {
    "name": "John Doe",
    "email": "john@example.com",
    "status": "active"
  }
}
```

### 3. UPDATE_DATA

**Request Structure**:
```json
{
  "data": {
    "column1": "new_value1",
    "column2": "new_value2",
    ...
  }
}
```

**Example**:
```json
{
  "data": {
    "name": "Jane Doe",
    "status": "inactive"
  }
}
```

### 4. DELETE_DATA (Soft Delete)

**Request Structure**:
```json
{
  "reason": "string (optional)"
}
```

**Example**:
```json
{
  "reason": "User requested account deletion"
}
```

### 5. GET_SOFT_DELETED

**Request Structure**:
```json
{
  "filters": [
    {
      "column": "string",
      "operator": "string",
      "value": "string|number|array"
    }
  ],
  "limit": "integer (optional)",
  "offset": "integer (optional)"
}
```

### 6. RECOVER_RECORD

**Request Structure**:
```json
{
  "reason": "string (optional)"
}
```

**Example**:
```json
{
  "reason": "Recovery requested by user"
}
```

### 7. PERMANENTLY_DELETE_RECORD

**Request Structure**:
```json
{
  "confirmation": "string (required: 'PERMANENTLY_DELETE')"
}
```

**Example**:
```json
{
  "confirmation": "PERMANENTLY_DELETE"
}
```

---

## Raw Query Operations

### Execute Raw Query

**Request Structure**:
```json
{
  "query": "string (SELECT query only)"
}
```

**Example**:
```json
{
  "query": "SELECT id, name, email FROM users WHERE status = 'active' AND created_at > '2024-01-01' LIMIT 10"
}
```

**Validation Rules**:
- Only `SELECT` queries are allowed
- No `DROP`, `DELETE`, `UPDATE`, `INSERT`, `CREATE`, `ALTER` statements
- No system database access (`information_schema`, `mysql`, `performance_schema`)
- No file operations (`LOAD_FILE`, `INTO OUTFILE`)
- No procedure calls or functions

---

## Metadata Operations

### 1. Get Filter Operators

**Request Structure**: No payload required

**Response Structure**:
```json
{
  "status": "success",
  "message": "Filter operators retrieved successfully",
  "data": [
    {
      "name": "string",
      "label": "string",
      "operator": "string",
      "description": "string"
    }
  ]
}
```

### 2. Get Aggregation Functions

**Request Structure**: No payload required

**Response Structure**:
```json
{
  "status": "success",
  "message": "Aggregation functions retrieved successfully",
  "data": [
    {
      "name": "string",
      "label": "string",
      "description": "string",
      "return_type": "string"
    }
  ]
}
```

### 3. Get All Columns

**Request Structure**: No payload required

**Response Structure**:
```json
{
  "status": "success",
  "message": "Columns retrieved successfully",
  "data": [
    {
      "table_name": "string",
      "column_name": "string",
      "data_type": "string",
      "nullable": "string (YES|NO)",
      "default_value": "string|null",
      "column_type": "string"
    }
  ]
}
```

---

## Admin Operations

### 1. Approve Batch

**Request Structure**:
```json
{
  "admin_notes": "string (optional)"
}
```

**Example**:
```json
{
  "admin_notes": "Approved for production deployment after testing"
}
```

### 2. Reject Batch

**Request Structure**:
```json
{
  "reason": "string (required)",
  "admin_notes": "string (optional)"
}
```

**Example**:
```json
{
  "reason": "Schema changes conflict with existing data",
  "admin_notes": "Please review the table structure before resubmitting"
}
```

### 3. Unblock Tenant

**Request Structure**:
```json
{
  "reason": "string (required)",
  "admin_notes": "string (optional)"
}
```

**Example**:
```json
{
  "reason": "Security issue resolved",
  "admin_notes": "Tenant has updated their security policies"
}
```

---

## System Operations

### 1. Preview SQL

**Request Structure**:
```json
{
  "sql": "string"
}
```

**Example**:
```json
{
  "sql": "CREATE TABLE test_table (id INT PRIMARY KEY, name VARCHAR(255))"
}
```

### 2. Get System Health

**Request Structure**: No payload required

**Response Structure**:
```json
{
  "status": "success",
  "message": "System health retrieved successfully",
  "data": {
    "database": {
      "status": "healthy|degraded|unhealthy",
      "response_time": "number (ms)",
      "connections": "number"
    },
    "queue": {
      "status": "healthy|degraded|unhealthy",
      "pending_jobs": "number",
      "failed_jobs": "number"
    },
    "cache": {
      "status": "healthy|degraded|unhealthy",
      "hit_rate": "number (percentage)"
    },
    "disk": {
      "status": "healthy|degraded|unhealthy",
      "usage_percentage": "number"
    },
    "memory": {
      "status": "healthy|degraded|unhealthy",
      "usage_percentage": "number"
    },
    "uptime": "string (e.g., '2 days, 5 hours')"
  }
}
```

---

## Common Request Headers

All API requests require these headers:

```json
{
  "X-APP-SIGNATURE": "string (HMAC signature)",
  "Content-Type": "application/json",
  "Accept": "application/json",
  "Idempotency-Key": "string (for POST/PATCH/DELETE operations)"
}
```

---

## Error Response Structure

All error responses follow this structure:

```json
{
  "status": "error",
  "message": "string (human-readable error message)",
  "error": {
    "code": "string (error code)",
    "details": "string (detailed error information)",
    "field": "string (optional, for validation errors)"
  }
}
```

---

## Notes

1. **Payload Nesting**: The system uses nested payload structures where the actual operation data is stored in `payload.payload` for batch operations.

2. **Schema Security**: Schema names are extracted from verified signatures and cannot be specified in request bodies.

3. **Tenant Isolation**: All operations are scoped to the tenant identified in the signature.

4. **Batch Processing**: DDL operations are queued for batch processing and require admin approval.

5. **Instant Execution**: DML operations execute immediately without approval.

6. **Soft Deletion**: DELETE operations perform soft deletion by setting `deleted_at` and `deleted_by` fields.

7. **Validation**: All operations undergo strict validation before execution or queuing.

8. **SQL Injection Protection**: Raw queries are validated to prevent SQL injection attacks.

9. **Idempotency**: POST, PATCH, and DELETE operations support idempotency keys to prevent duplicate operations.

10. **Audit Trail**: All operations are logged with timestamps, user information, and execution results.
