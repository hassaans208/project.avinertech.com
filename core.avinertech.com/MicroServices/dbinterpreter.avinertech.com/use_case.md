# Database API Use Cases - Comprehensive Testing Guide

## Overview
This document provides comprehensive use cases for testing all Database API endpoints. Each API is covered with multiple scenarios including success cases, error cases, edge cases, and security tests.

---

## 1. Authentication & Setup APIs

### 1.1 Set Environment Variables
**Endpoint**: `POST /api/v1/database/setup/environment`

#### Use Cases:
1. **Valid Signature Setup**
   ```json
   {
     "X_APP_SIGNATURE": "valid_signature_from_signal_service",
     "BASE_URL": "http://127.0.0.1:8000",
     "TABLE_NAME": "users"
   }
   ```

2. **Invalid Signature**
   ```json
   {
     "X_APP_SIGNATURE": "invalid_signature",
     "BASE_URL": "http://127.0.0.1:8000"
   }
   ```

3. **Missing Required Headers**
   ```json
   {
     "BASE_URL": "http://127.0.0.1:8000"
   }
   ```

---

## 2. Security Test APIs

### 2.1 Test Security Violation - tenant_id Parameter
**Endpoint**: `POST /api/v1/database/tables`

#### Use Cases:
1. **Security Violation with tenant_id**
   ```json
   {
     "tenant_id": "malicious-tenant-id",
     "name": "test_table",
     "columns": [
       {
         "name": "id",
         "type": "INT",
         "primary_key": true,
         "auto_increment": true
       }
     ]
   }
   ```

2. **Security Violation with schema_name**
   ```json
   {
     "schema_name": "malicious-schema",
     "name": "test_table",
     "columns": [
       {
         "name": "id",
         "type": "INT",
         "primary_key": true,
         "auto_increment": true
       }
     ]
   }
   ```

3. **Security Violation with Both Parameters**
   ```json
   {
     "tenant_id": "malicious-tenant-id",
     "schema_name": "malicious-schema",
     "name": "test_table",
     "columns": [
       {
         "name": "id",
         "type": "INT",
         "primary_key": true,
         "auto_increment": true
       }
     ]
   }
   ```

---

## 3. Database Capabilities APIs

### 3.1 Get Database Capabilities
**Endpoint**: `GET /api/v1/database/capabilities`

#### Use Cases:
1. **Valid Request**
   - Headers: `X-APP-SIGNATURE: valid_signature`
   - Expected: Database capabilities information

2. **Missing Signature**
   - Headers: None
   - Expected: 401 Unauthorized

3. **Invalid Signature**
   - Headers: `X-APP-SIGNATURE: invalid_signature`
   - Expected: 401 Unauthorized

---

## 4. Schema Management APIs

### 4.1 Get Schema Details
**Endpoint**: `GET /api/v1/database/schema`

#### Use Cases:
1. **Valid Schema Request**
   - Headers: `X-APP-SIGNATURE: valid_signature`
   - Expected: Schema information with tables

2. **Non-existent Schema**
   - Headers: `X-APP-SIGNATURE: signature_for_nonexistent_schema`
   - Expected: 404 Not Found

3. **Schema with No Tables**
   - Headers: `X-APP-SIGNATURE: signature_for_empty_schema`
   - Expected: Empty tables array

4. **Schema with Many Tables**
   - Headers: `X-APP-SIGNATURE: signature_for_large_schema`
   - Expected: All tables with metadata

---

## 5. Table Management APIs

### 5.1 List Tables
**Endpoint**: `GET /api/v1/database/tables`

#### Use Cases:
1. **Basic Table Listing**
   - Headers: `X-APP-SIGNATURE: valid_signature`
   - Expected: Array of tables with metadata

2. **Empty Schema**
   - Headers: `X-APP-SIGNATURE: signature_for_empty_schema`
   - Expected: Empty array

3. **Large Schema**
   - Headers: `X-APP-SIGNATURE: signature_for_large_schema`
   - Expected: All tables with pagination

4. **Schema with Different Table Types**
   - Headers: `X-APP-SIGNATURE: signature_for_mixed_schema`
   - Expected: Tables, views, temporary tables

### 5.2 Get Table Details
**Endpoint**: `GET /api/v1/database/tables/{tableName}`

#### Use Cases:
1. **Valid Table**
   ```http
   GET /api/v1/database/tables/users
   ```

2. **Non-existent Table**
   ```http
   GET /api/v1/database/tables/nonexistent_table
   ```

3. **Table with Complex Structure**
   ```http
   GET /api/v1/database/tables/complex_table
   ```

4. **Table with Many Columns**
   ```http
   GET /api/v1/database/tables/large_table
   ```

5. **Table with Indexes**
   ```http
   GET /api/v1/database/tables/indexed_table
   ```

6. **Table with Foreign Keys**
   ```http
   GET /api/v1/database/tables/related_table
   ```

### 5.3 Create Table
**Endpoint**: `POST /api/v1/database/tables`

#### Use Cases:
1. **Simple Table Creation**
   ```json
   {
     "name": "test_users",
     "columns": [
       {
         "name": "id",
         "type": "INT",
         "primary_key": true,
         "auto_increment": true
       },
       {
         "name": "name",
         "type": "VARCHAR(255)",
         "nullable": false
       }
     ],
     "case_id": 1
   }
   ```

2. **Complex Table Creation**
   ```json
   {
     "name": "complex_table",
     "columns": [
       {
         "name": "id",
         "type": "BIGINT",
         "primary_key": true,
         "auto_increment": true
       },
       {
         "name": "email",
         "type": "VARCHAR(255)",
         "nullable": false,
         "unique": true
       },
       {
         "name": "created_at",
         "type": "TIMESTAMP",
         "default": "CURRENT_TIMESTAMP"
       },
       {
         "name": "metadata",
         "type": "JSON"
       }
     ],
     "engine": "InnoDB",
     "case_id": 1
   }
   ```

3. **Table with Constraints**
   ```json
   {
     "name": "constrained_table",
     "columns": [
       {
         "name": "id",
         "type": "INT",
         "primary_key": true
       },
       {
         "name": "age",
         "type": "INT",
         "check": "age >= 0 AND age <= 150"
       },
       {
         "name": "status",
         "type": "ENUM('active', 'inactive', 'pending')",
         "default": "pending"
       }
     ],
     "case_id": 1
   }
   ```

4. **Invalid Table Creation**
   ```json
   {
     "name": "",
     "columns": []
   }
   ```

5. **Table with Reserved Keywords**
   ```json
   {
     "name": "order",
     "columns": [
       {
         "name": "id",
         "type": "INT",
         "primary_key": true
       }
     ],
     "case_id": 1
   }
   ```

---

## 6. Data Management APIs

### 6.1 Get Table Data
**Endpoint**: `GET /api/v1/database/tables/{tableName}/data`

#### Use Cases:
1. **Basic Data Retrieval**
   ```http
   GET /api/v1/database/tables/users/data?limit=10&offset=0
   ```

2. **Data with Filters**
   ```http
   GET /api/v1/database/tables/users/data?filters[0][column]=name&filters[0][operator]=like&filters[0][value]=%John%&limit=10
   ```

3. **Data with Multiple Filters**
   ```http
   GET /api/v1/database/tables/users/data?filters[0][column]=age&filters[0][operator]=greater_than&filters[0][value]=18&filters[1][column]=status&filters[1][operator]=equals&filters[1][value]=active&limit=20
   ```

4. **Data with Sorting**
   ```http
   GET /api/v1/database/tables/users/data?order_by=created_at&order_direction=desc&limit=10
   ```

5. **Large Dataset**
   ```http
   GET /api/v1/database/tables/large_table/data?limit=1000&offset=0
   ```

6. **Empty Table**
   ```http
   GET /api/v1/database/tables/empty_table/data
   ```

7. **Data with Date Filters**
   ```http
   GET /api/v1/database/tables/users/data?filters[0][column]=created_at&filters[0][operator]=between&filters[0][value][]=2024-01-01&filters[0][value][]=2024-12-31
   ```

8. **Data with NULL Filters**
   ```http
   GET /api/v1/database/tables/users/data?filters[0][column]=deleted_at&filters[0][operator]=is_null
   ```

### 6.2 Insert Data
**Endpoint**: `POST /api/v1/database/tables/{tableName}/data`

#### Use Cases:
1. **Single Record Insert**
   ```json
   {
     "data": {
       "name": "John Doe",
       "email": "john@example.com",
       "age": 30
     }
   }
   ```

2. **Multiple Records Insert**
   ```json
   {
     "data": [
       {
         "name": "John Doe",
         "email": "john@example.com",
         "age": 30
       },
       {
         "name": "Jane Smith",
         "email": "jane@example.com",
         "age": 25
       }
     ]
   }
   ```

3. **Insert with Default Values**
   ```json
   {
     "data": {
       "name": "John Doe",
       "email": "john@example.com"
     }
   }
   ```

4. **Insert with JSON Data**
   ```json
   {
     "data": {
       "name": "John Doe",
       "metadata": {
         "preferences": {
           "theme": "dark",
           "notifications": true
         }
       }
     }
   }
   ```

5. **Insert with Invalid Data**
   ```json
   {
     "data": {
       "name": "",
       "email": "invalid-email",
       "age": -5
     }
   }
   ```

6. **Insert with Duplicate Key**
   ```json
   {
     "data": {
       "id": 1,
       "name": "John Doe",
       "email": "john@example.com"
     }
   }
   ```

### 6.3 Update Data
**Endpoint**: `PUT /api/v1/database/tables/{tableName}/data/{recordId}`

#### Use Cases:
1. **Update Single Field**
   ```json
   {
     "data": {
       "name": "John Updated"
     }
   }
   ```

2. **Update Multiple Fields**
   ```json
   {
     "data": {
       "name": "John Updated",
       "email": "john.updated@example.com",
       "age": 31
     }
   }
   ```

3. **Update with NULL Values**
   ```json
   {
     "data": {
       "phone": null,
       "address": null
     }
   }
   ```

4. **Update Non-existent Record**
   ```json
   {
     "data": {
       "name": "John Updated"
     }
   }
   ```

5. **Update with Invalid Data**
   ```json
   {
     "data": {
       "email": "invalid-email",
       "age": -5
     }
   }
   ```

### 6.4 Delete Data (Soft Delete)
**Endpoint**: `DELETE /api/v1/database/tables/{tableName}/data/{recordId}`

#### Use Cases:
1. **Soft Delete Valid Record**
   ```http
   DELETE /api/v1/database/tables/users/data/1
   ```

2. **Soft Delete Non-existent Record**
   ```http
   DELETE /api/v1/database/tables/users/data/99999
   ```

3. **Soft Delete Already Deleted Record**
   ```http
   DELETE /api/v1/database/tables/users/data/1
   ```

---

## 7. Soft Delete Management APIs

### 7.1 Get Soft Deleted Records
**Endpoint**: `GET /api/v1/database/tables/{tableName}/soft-deleted`

#### Use Cases:
1. **Get All Soft Deleted Records**
   ```http
   GET /api/v1/database/tables/users/soft-deleted
   ```

2. **Get Soft Deleted with Filters**
   ```http
   GET /api/v1/database/tables/users/soft-deleted?filters[0][column]=deleted_at&filters[0][operator]=greater_than&filters[0][value]=2024-01-01
   ```

3. **Get Soft Deleted with Pagination**
   ```http
   GET /api/v1/database/tables/users/soft-deleted?limit=10&offset=0
   ```

4. **Get Soft Deleted from Empty Table**
   ```http
   GET /api/v1/database/tables/empty_table/soft-deleted
   ```

### 7.2 Recover Soft Deleted Record
**Endpoint**: `POST /api/v1/database/tables/{tableName}/soft-deleted/{recordId}/recover`

#### Use Cases:
1. **Recover Valid Soft Deleted Record**
   ```http
   POST /api/v1/database/tables/users/soft-deleted/1/recover
   ```

2. **Recover Non-existent Record**
   ```http
   POST /api/v1/database/tables/users/soft-deleted/99999/recover
   ```

3. **Recover Already Recovered Record**
   ```http
   POST /api/v1/database/tables/users/soft-deleted/1/recover
   ```

### 7.3 Permanently Delete Record
**Endpoint**: `DELETE /api/v1/database/tables/{tableName}/soft-deleted/{recordId}/permanent`

#### Use Cases:
1. **Permanent Delete Valid Soft Deleted Record**
   ```http
   DELETE /api/v1/database/tables/users/soft-deleted/1/permanent
   ```

2. **Permanent Delete Non-existent Record**
   ```http
   DELETE /api/v1/database/tables/users/soft-deleted/99999/permanent
   ```

3. **Permanent Delete Active Record**
   ```http
   DELETE /api/v1/database/tables/users/soft-deleted/1/permanent
   ```

---

## 8. Batch Operations APIs

### 8.1 Add Column
**Endpoint**: `POST /api/v1/database/tables/{tableName}/columns`

#### Use Cases:
1. **Add Simple Column**
   ```json
   {
     "column": {
       "name": "phone",
       "type": "VARCHAR(20)",
       "nullable": true
     },
     "case_id": 2
   }
   ```

2. **Add Column with Default Value**
   ```json
   {
     "column": {
       "name": "status",
       "type": "ENUM('active', 'inactive')",
       "default": "active",
       "nullable": false
     },
     "case_id": 2
   }
   ```

3. **Add Column with Constraints**
   ```json
   {
     "column": {
       "name": "age",
       "type": "INT",
       "nullable": false,
       "check": "age >= 0 AND age <= 150"
     },
     "case_id": 2
   }
   ```

4. **Add Column with Invalid Data**
   ```json
   {
     "column": {
       "name": "",
       "type": "INVALID_TYPE"
     },
     "case_id": 2
   }
   ```

5. **Add Column to Non-existent Table**
   ```json
   {
     "column": {
       "name": "new_column",
       "type": "VARCHAR(255)"
     },
     "case_id": 2
   }
   ```

### 8.2 Update Column
**Endpoint**: `PUT /api/v1/database/tables/{tableName}/columns/{columnName}`

#### Use Cases:
1. **Update Column Type**
   ```json
   {
     "column": {
       "type": "TEXT",
       "nullable": true
     },
     "case_id": 2
   }
   ```

2. **Update Column Constraints**
   ```json
   {
     "column": {
       "type": "VARCHAR(500)",
       "nullable": false,
       "default": "default_value"
     },
     "case_id": 2
   }
   ```

3. **Update Non-existent Column**
   ```json
   {
     "column": {
       "type": "VARCHAR(255)"
     },
     "case_id": 2
   }
   ```

4. **Update Column with Invalid Type**
   ```json
   {
     "column": {
       "type": "INVALID_TYPE"
     },
     "case_id": 2
   }
   ```

### 8.3 Delete Column
**Endpoint**: `DELETE /api/v1/database/tables/{tableName}/columns/{columnName}`

#### Use Cases:
1. **Delete Valid Column**
   ```json
   {
     "case_id": 2
   }
   ```

2. **Delete Non-existent Column**
   ```json
   {
     "case_id": 2
   }
   ```

3. **Delete Primary Key Column**
   ```json
   {
     "case_id": 2
   }
   ```

4. **Delete Column with Foreign Key**
   ```json
   {
     "case_id": 2
   }
   ```

### 8.4 Add Index
**Endpoint**: `POST /api/v1/database/tables/{tableName}/indexes`

#### Use Cases:
1. **Add Simple Index**
   ```json
   {
     "index": {
       "name": "idx_email",
       "columns": ["email"],
       "type": "INDEX"
     },
     "case_id": 2
   }
   ```

2. **Add Unique Index**
   ```json
   {
     "index": {
       "name": "idx_unique_email",
       "columns": ["email"],
       "type": "UNIQUE"
     },
     "case_id": 2
   }
   ```

3. **Add Composite Index**
   ```json
   {
     "index": {
       "name": "idx_name_email",
       "columns": ["name", "email"],
       "type": "INDEX"
     },
     "case_id": 2
   }
   ```

4. **Add Fulltext Index**
   ```json
   {
     "index": {
       "name": "idx_content",
       "columns": ["content"],
       "type": "FULLTEXT"
     },
     "case_id": 2
   }
   ```

5. **Add Index with Invalid Columns**
   ```json
   {
     "index": {
       "name": "idx_invalid",
       "columns": ["non_existent_column"],
       "type": "INDEX"
     },
     "case_id": 2
   }
   ```

### 8.5 Delete Index
**Endpoint**: `DELETE /api/v1/database/tables/{tableName}/indexes/{indexName}`

#### Use Cases:
1. **Delete Valid Index**
   ```json
   {
     "case_id": 2
   }
   ```

2. **Delete Non-existent Index**
   ```json
   {
     "case_id": 2
   }
   ```

3. **Delete Primary Key Index**
   ```json
   {
     "case_id": 2
   }
   ```

---

## 9. Operation Management APIs

### 9.1 Create Operation
**Endpoint**: `POST /api/v1/database/operations`

#### Use Cases:
1. **Create Table Operation**
   ```json
   {
     "type": "CREATE_TABLE",
     "table_name": "test_table",
     "case_id": 1,
     "payload": {
       "name": "test_table",
       "columns": [
         {
           "name": "id",
           "type": "INT",
           "primary_key": true
         }
       ]
     }
   }
   ```

2. **Create Alter Table Operation**
   ```json
   {
     "type": "ALTER_TABLE",
     "table_name": "users",
     "case_id": 2,
     "payload": {
       "add_column": {
         "name": "phone",
         "type": "VARCHAR(20)"
       }
     }
   }
   ```

3. **Create Index Operation**
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

4. **Create Invalid Operation**
   ```json
   {
     "type": "INVALID_TYPE",
     "table_name": "users",
     "case_id": 1
   }
   ```

### 9.2 Get Operation Details
**Endpoint**: `GET /api/v1/database/operations/{operationId}`

#### Use Cases:
1. **Get Valid Operation**
   ```http
   GET /api/v1/database/operations/1
   ```

2. **Get Non-existent Operation**
   ```http
   GET /api/v1/database/operations/99999
   ```

3. **Get Operation from Different Tenant**
   ```http
   GET /api/v1/database/operations/1
   ```

### 9.3 List Operations
**Endpoint**: `GET /api/v1/database/operations`

#### Use Cases:
1. **List All Operations**
   ```http
   GET /api/v1/database/operations
   ```

2. **List Operations by Status**
   ```http
   GET /api/v1/database/operations?status=draft
   ```

3. **List Operations with Pagination**
   ```http
   GET /api/v1/database/operations?limit=10&offset=0
   ```

4. **List Operations by Type**
   ```http
   GET /api/v1/database/operations?type=CREATE_TABLE
   ```

### 9.4 Get Operation Groups
**Endpoint**: `GET /api/v1/database/operation-groups`

#### Use Cases:
1. **List All Groups**
   ```http
   GET /api/v1/database/operation-groups
   ```

2. **List Groups by Status**
   ```http
   GET /api/v1/database/operation-groups?status=pending_approval
   ```

3. **List Groups with Pagination**
   ```http
   GET /api/v1/database/operation-groups?limit=10&offset=0
   ```

### 9.5 Get Operation Group Details
**Endpoint**: `GET /api/v1/database/operation-groups/{groupId}`

#### Use Cases:
1. **Get Valid Group**
   ```http
   GET /api/v1/database/operation-groups/1
   ```

2. **Get Non-existent Group**
   ```http
   GET /api/v1/database/operation-groups/99999
   ```

3. **Get Group with Many Operations**
   ```http
   GET /api/v1/database/operation-groups/1
   ```

### 9.6 Request Batch Approval
**Endpoint**: `POST /api/v1/database/operation-groups/{groupId}/request-approval`

#### Use Cases:
1. **Request Approval for Draft Group**
   ```json
   {
     "description": "Adding new columns for user profile enhancement"
   }
   ```

2. **Request Approval for Non-existent Group**
   ```json
   {
     "description": "Test approval request"
   }
   ```

3. **Request Approval for Already Pending Group**
   ```json
   {
     "description": "Duplicate approval request"
   }
   ```

---

## 10. Admin Management APIs

### 10.1 Get Pending Batches
**Endpoint**: `GET /api/v1/admin/batches/pending`

#### Use Cases:
1. **Get All Pending Batches**
   ```http
   GET /api/v1/admin/batches/pending
   ```

2. **Get Pending Batches with Pagination**
   ```http
   GET /api/v1/admin/batches/pending?limit=10&offset=0
   ```

3. **Get Pending Batches When None Exist**
   ```http
   GET /api/v1/admin/batches/pending
   ```

### 10.2 Approve Batch
**Endpoint**: `POST /api/v1/admin/batches/{groupId}/approve`

#### Use Cases:
1. **Approve Valid Pending Batch**
   ```json
   {
     "admin_notes": "Approved after security review"
   }
   ```

2. **Approve Non-existent Batch**
   ```json
   {
     "admin_notes": "Test approval"
   }
   ```

3. **Approve Already Approved Batch**
   ```json
   {
     "admin_notes": "Duplicate approval"
   }
   ```

4. **Approve Batch Without Notes**
   ```json
   {}
   ```

### 10.3 Reject Batch
**Endpoint**: `POST /api/v1/admin/batches/{groupId}/reject`

#### Use Cases:
1. **Reject Valid Pending Batch**
   ```json
   {
     "admin_notes": "Rejected due to potential data loss"
   }
   ```

2. **Reject Non-existent Batch**
   ```json
   {
     "admin_notes": "Test rejection"
   }
   ```

3. **Reject Already Processed Batch**
   ```json
   {
     "admin_notes": "Late rejection"
   }
   ```

### 10.4 Execute Batch
**Endpoint**: `POST /api/v1/admin/batches/{groupId}/execute`

#### Use Cases:
1. **Execute Approved Batch**
   ```http
   POST /api/v1/admin/batches/1/execute
   ```

2. **Execute Non-existent Batch**
   ```http
   POST /api/v1/admin/batches/99999/execute
   ```

3. **Execute Non-approved Batch**
   ```http
   POST /api/v1/admin/batches/1/execute
   ```

4. **Execute Already Running Batch**
   ```http
   POST /api/v1/admin/batches/1/execute
   ```

### 10.5 Get Batch Status
**Endpoint**: `GET /api/v1/admin/batches/{groupId}/status`

#### Use Cases:
1. **Get Status of Valid Batch**
   ```http
   GET /api/v1/admin/batches/1/status
   ```

2. **Get Status of Non-existent Batch**
   ```http
   GET /api/v1/admin/batches/99999/status
   ```

3. **Get Status of Running Batch**
   ```http
   GET /api/v1/admin/batches/1/status
   ```

4. **Get Status of Completed Batch**
   ```http
   GET /api/v1/admin/batches/1/status
   ```

5. **Get Status of Failed Batch**
   ```http
   GET /api/v1/admin/batches/1/status
   ```

### 10.6 Cancel Batch
**Endpoint**: `POST /api/v1/admin/batches/{groupId}/cancel`

#### Use Cases:
1. **Cancel Draft Batch**
   ```json
   {
     "admin_notes": "Cancelled due to requirements change"
   }
   ```

2. **Cancel Pending Batch**
   ```json
   {
     "admin_notes": "Cancelled due to security concerns"
   }
   ```

3. **Cancel Approved Batch**
   ```json
   {
     "admin_notes": "Cancelled due to system maintenance"
   }
   ```

4. **Cancel Non-existent Batch**
   ```json
   {
     "admin_notes": "Test cancellation"
   }
   ```

5. **Cancel Running Batch**
   ```json
   {
     "admin_notes": "Emergency cancellation"
   }
   ```

---

## 11. Metadata APIs

### 11.1 Get All Columns
**Endpoint**: `GET /api/v1/database/metadata/columns`

#### Use Cases:
1. **Get All Columns**
   ```http
   GET /api/v1/database/metadata/columns
   ```

2. **Get Columns with Filters**
   ```http
   GET /api/v1/database/metadata/columns?table_name=users
   ```

3. **Get Columns with Pagination**
   ```http
   GET /api/v1/database/metadata/columns?limit=100&offset=0
   ```

---

## 12. Raw Query APIs

### 12.1 Execute Raw Query
**Endpoint**: `POST /api/v1/database/raw-query`

#### Use Cases:
1. **Execute Simple SELECT Query**
   ```json
   {
     "query": "SELECT COUNT(*) as total FROM users",
     "case_id": 3
   }
   ```

2. **Execute Complex Query**
   ```json
   {
     "query": "SELECT u.name, COUNT(o.id) as order_count FROM users u LEFT JOIN orders o ON u.id = o.user_id GROUP BY u.id",
     "case_id": 3
   }
   ```

3. **Execute Query with Parameters**
   ```json
   {
     "query": "SELECT * FROM users WHERE age > ? AND status = ?",
     "parameters": [18, "active"],
     "case_id": 3
   }
   ```

4. **Execute Invalid Query**
   ```json
   {
     "query": "INVALID SQL SYNTAX",
     "case_id": 3
   }
   ```

5. **Execute Dangerous Query**
   ```json
   {
     "query": "DROP TABLE users",
     "case_id": 3
   }
   ```

---

## 13. Error Handling Test Cases

### 13.1 Authentication Errors
1. **Missing Signature Header**
2. **Invalid Signature Format**
3. **Expired Signature**
4. **Signature for Wrong Tenant**

### 13.2 Validation Errors
1. **Missing Required Fields**
2. **Invalid Data Types**
3. **Invalid Field Values**
4. **Exceeding Field Limits**

### 13.3 Database Errors
1. **Connection Timeout**
2. **Table Not Found**
3. **Column Not Found**
4. **Constraint Violations**
5. **Deadlock Errors**

### 13.4 Business Logic Errors
1. **Operation Not Allowed**
2. **Batch Size Exceeded**
3. **Concurrent Modification**
4. **Resource Exhaustion**

---

## 14. Performance Test Cases

### 14.1 Large Dataset Tests
1. **Table with 1M+ Records**
2. **Table with 100+ Columns**
3. **Complex Queries with Joins**
4. **Batch Operations with 100+ Operations**

### 14.2 Concurrent Access Tests
1. **Multiple Users Accessing Same Table**
2. **Concurrent Batch Operations**
3. **Simultaneous Data Modifications**
4. **Admin Operations During User Operations**

### 14.3 Memory and Resource Tests
1. **Large JSON Payloads**
2. **Deep Nested Queries**
3. **Long-running Operations**
4. **Memory-intensive Queries**

---

## 15. Security Test Cases

### 15.1 SQL Injection Tests
1. **Basic SQL Injection Attempts**
2. **Blind SQL Injection**
3. **Time-based SQL Injection**
4. **Union-based SQL Injection**

### 15.2 Parameter Manipulation
1. **Schema Name Injection**
2. **Tenant ID Spoofing**
3. **Operation ID Manipulation**
4. **Batch ID Tampering**

### 15.3 Authorization Tests
1. **Cross-tenant Data Access**
2. **Unauthorized Admin Operations**
3. **Privilege Escalation Attempts**
4. **Session Hijacking**

---

## 16. Integration Test Scenarios

### 16.1 Complete Workflow Tests
1. **Create Table → Add Columns → Add Indexes → Insert Data**
2. **Create Batch → Add Operations → Request Approval → Approve → Execute**
3. **Insert Data → Update Data → Soft Delete → Recover → Permanent Delete**

### 16.2 Cross-API Tests
1. **Metadata API with Table Operations**
2. **Batch Operations with Data Operations**
3. **Admin Operations with User Operations**
4. **Raw Queries with Structured Operations**

---

## Testing Checklist

### ✅ Authentication & Security
- [ ] Valid signature acceptance
- [ ] Invalid signature rejection
- [ ] Missing signature handling
- [ ] Parameter security validation
- [ ] Cross-tenant access prevention

### ✅ CRUD Operations
- [ ] Create operations (tables, columns, indexes)
- [ ] Read operations (list, get details, query data)
- [ ] Update operations (modify data, alter structure)
- [ ] Delete operations (soft delete, permanent delete)

### ✅ Batch Operations
- [ ] Operation group creation
- [ ] Operation addition to groups
- [ ] Approval workflow
- [ ] Execution and monitoring
- [ ] Error handling and rollback

### ✅ Admin Functions
- [ ] Pending batch management
- [ ] Approval/rejection workflow
- [ ] Batch execution control
- [ ] Status monitoring
- [ ] Cancellation handling

### ✅ Error Scenarios
- [ ] Invalid input handling
- [ ] Database constraint violations
- [ ] Resource not found errors
- [ ] Permission denied errors
- [ ] System errors and timeouts

### ✅ Performance & Load
- [ ] Large dataset handling
- [ ] Concurrent access management
- [ ] Memory usage optimization
- [ ] Query performance
- [ ] Batch processing efficiency

This comprehensive use case guide covers all APIs with maximum test scenarios for thorough validation of the Database API system.
