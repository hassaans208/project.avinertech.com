# Database API - Comprehensive Test Cases

## Table of Contents
1. [Test Environment Setup](#test-environment-setup)
2. [Authentication & Security Tests](#authentication--security-tests)
3. [Metadata API Tests](#metadata-api-tests)
4. [Raw Query API Tests](#raw-query-api-tests)
5. [Fresh App DB Flow Tests](#fresh-app-db-flow-tests)
6. [Modify App DB Flow Tests](#modify-app-db-flow-tests)
7. [Data Operations Flow Tests](#data-operations-flow-tests)
8. [Admin Approval Workflow Tests](#admin-approval-workflow-tests)
9. [Soft Delete Management Tests](#soft-delete-management-tests)
10. [Error Handling Tests](#error-handling-tests)
11. [Performance & Security Tests](#performance--security-tests)
12. [Complete User Journey Tests](#complete-user-journey-tests)

---

## Test Environment Setup

### Prerequisites
- Laravel application running on `http://127.0.0.1:8000`
- MySQL database with `ui_api` user configured
- Valid signature from `signal.avinertech.com`
- Postman collection imported
- Environment variables configured

### Test Data Setup
```bash
# Run migrations
php artisan migrate

# Seed test data
php artisan db:seed --class=TestDataSeeder
```

---

## Authentication & Security Tests

### TC-AUTH-001: Valid Signature Verification
**Objective**: Test successful authentication with valid signature
**Steps**:
1. Set valid `X-APP-SIGNATURE` header
2. Make request to `/api/v1/database/capabilities`
3. Verify response status 200
4. Verify tenant_id is extracted and added to request

**Expected Result**: 
- Status: 200
- Response contains MySQL version and capabilities
- Request includes tenant_id

### TC-AUTH-002: Missing Signature Header
**Objective**: Test authentication failure without signature
**Steps**:
1. Remove `X-APP-SIGNATURE` header
2. Make request to `/api/v1/database/capabilities`
3. Verify response status 401

**Expected Result**: 
- Status: 401
- Error message: "Signature verification required"

### TC-AUTH-003: Invalid Signature
**Objective**: Test authentication failure with invalid signature
**Steps**:
1. Set invalid `X-APP-SIGNATURE` header
2. Make request to `/api/v1/database/capabilities`
3. Verify response status 401

**Expected Result**: 
- Status: 401
- Error message: "Signature verification failed"

### TC-AUTH-004: Blocked Tenant Signature
**Objective**: Test authentication failure for blocked tenant
**Steps**:
1. Set signature for blocked tenant
2. Make request to `/api/v1/database/capabilities`
3. Verify response status 401

**Expected Result**: 
- Status: 401
- Error message: "Invalid signature or tenant blocked"

---

## Metadata API Tests

### TC-META-001: Get Filter Operators
**Objective**: Test retrieval of all filter operators
**Steps**:
1. Set valid signature
2. GET `/api/v1/database/metadata/filters`
3. Verify response structure

**Expected Result**: 
- Status: 200
- Data contains all filter operators (equals, not_equals, greater_than, etc.)
- Response format matches FilterResource structure

### TC-META-002: Get Aggregation Functions
**Objective**: Test retrieval of all aggregation functions
**Steps**:
1. Set valid signature
2. GET `/api/v1/database/metadata/aggregations`
3. Verify response structure

**Expected Result**: 
- Status: 200
- Data contains all aggregation functions (count, sum, avg, min, max, etc.)
- Response format matches AggregationResource structure

### TC-META-003: Get Tenant Columns
**Objective**: Test retrieval of all tenant table columns
**Steps**:
1. Set valid signature
2. GET `/api/v1/database/metadata/columns`
3. Verify response structure

**Expected Result**: 
- Status: 200
- Data contains comprehensive column metadata
- Response format matches ColumnResource structure

### TC-META-004: Metadata Caching
**Objective**: Test metadata caching behavior
**Steps**:
1. First request to `/api/v1/database/metadata/filters`
2. Second request to same endpoint
3. Verify caching behavior

**Expected Result**: 
- Both requests return same data
- Second request should be faster (cached)

---

## Raw Query API Tests

### TC-RAW-001: Valid SELECT Query
**Objective**: Test execution of valid SELECT query
**Steps**:
1. Set valid signature
2. POST `/api/v1/database/raw-query`
3. Body: `{"query": "SELECT * FROM users WHERE id > 1 LIMIT 10"}`

**Expected Result**: 
- Status: 200
- Data contains query results
- Query was limited to 1000 records max

### TC-RAW-002: SELECT Query with Aggregation
**Objective**: Test SELECT query with aggregation functions
**Steps**:
1. Set valid signature
2. POST `/api/v1/database/raw-query`
3. Body: `{"query": "SELECT COUNT(*) as total, AVG(age) as avg_age FROM users"}`

**Expected Result**: 
- Status: 200
- Data contains aggregation results
- No limits applied to aggregation queries

### TC-RAW-003: Prohibited DROP Query
**Objective**: Test validation against DROP operations
**Steps**:
1. Set valid signature
2. POST `/api/v1/database/raw-query`
3. Body: `{"query": "DROP TABLE users"}`

**Expected Result**: 
- Status: 400
- Error: "Query contains prohibited patterns"

### TC-RAW-004: Prohibited INSERT Query
**Objective**: Test validation against INSERT operations
**Steps**:
1. Set valid signature
2. POST `/api/v1/database/raw-query`
3. Body: `{"query": "INSERT INTO users (name) VALUES ('John')"}`

**Expected Result**: 
- Status: 400
- Error: "Query contains prohibited patterns"

### TC-RAW-005: Value-to-Value Comparison
**Objective**: Test validation against value-to-value comparisons
**Steps**:
1. Set valid signature
2. POST `/api/v1/database/raw-query`
3. Body: `{"query": "SELECT * FROM users WHERE 1 = 1"}`

**Expected Result**: 
- Status: 400
- Error: "Value-to-value comparisons not allowed"

### TC-RAW-006: Query Limit Application
**Objective**: Test automatic query limit application
**Steps**:
1. Set valid signature
2. POST `/api/v1/database/raw-query`
3. Body: `{"query": "SELECT * FROM users"}` (no LIMIT)

**Expected Result**: 
- Status: 200
- Query automatically limited to 1000 records
- Response indicates query was limited

---

## Fresh App DB Flow Tests

### TC-FRESH-001: Create Users Table
**Objective**: Test creation of users table in Fresh App DB mode
**Steps**:
1. Set valid signature
2. POST `/api/v1/database/schemas/{schema}/tables`
3. Body: Create users table with columns (id, name, email, created_at)
4. Verify operation stored with status 'draft'

**Expected Result**: 
- Status: 201
- Operation name: "BATCH1_CREATE_USERS_TABLE"
- Status: 'draft'
- Case ID: 1 (Fresh App DB)

### TC-FRESH-002: Create Products Table
**Objective**: Test creation of products table
**Steps**:
1. Set valid signature
2. POST `/api/v1/database/schemas/{schema}/tables`
3. Body: Create products table with columns (id, name, price, description)
4. Verify operation stored

**Expected Result**: 
- Status: 201
- Operation name: "BATCH1_CREATE_PRODUCTS_TABLE"
- Status: 'draft'

### TC-FRESH-003: Create Orders Table
**Objective**: Test creation of orders table
**Steps**:
1. Set valid signature
2. POST `/api/v1/database/schemas/{schema}/tables`
3. Body: Create orders table with foreign keys to users and products
4. Verify operation stored

**Expected Result**: 
- Status: 201
- Operation name: "BATCH1_CREATE_ORDERS_TABLE"
- Status: 'draft'

### TC-FRESH-004: Request Batch Approval
**Objective**: Test requesting approval for Fresh App DB batch
**Steps**:
1. Set valid signature
2. POST `/api/v1/database/operation-groups/{groupId}/request-approval`
3. Body: `{"description": "Creating new e-commerce application database"}`
4. Verify group status updated to 'pending_approval'

**Expected Result**: 
- Status: 200
- Group status: 'pending_approval'
- Admin notification sent

---

## Modify App DB Flow Tests

### TC-MODIFY-001: Add Column to Existing Table
**Objective**: Test adding column to existing table
**Steps**:
1. Set valid signature
2. POST `/api/v1/database/schemas/{schema}/tables/{table}/columns`
3. Body: Add 'phone' column to users table
4. Verify operation stored

**Expected Result**: 
- Status: 201
- Operation name: "BATCH2_ALTER_USERS_TABLE"
- Status: 'draft'
- Case ID: 2 (Modify App DB)

### TC-MODIFY-002: Create New Table
**Objective**: Test creating new table in modify mode
**Steps**:
1. Set valid signature
2. POST `/api/v1/database/schemas/{schema}/tables`
3. Body: Create 'user_profiles' table
4. Verify operation stored

**Expected Result**: 
- Status: 201
- Operation name: "BATCH2_CREATE_USER_PROFILES_TABLE"
- Status: 'draft'

### TC-MODIFY-003: Add Foreign Key
**Objective**: Test adding foreign key constraint
**Steps**:
1. Set valid signature
2. POST `/api/v1/database/schemas/{schema}/tables/{table}/foreign-keys`
3. Body: Add FK from users to user_profiles
4. Verify operation stored

**Expected Result**: 
- Status: 201
- Operation name: "BATCH2_ADD_USERS_PROFILES_FK"
- Status: 'draft'

### TC-MODIFY-004: Request Batch Approval
**Objective**: Test requesting approval for Modify App DB batch
**Steps**:
1. Set valid signature
2. POST `/api/v1/database/operation-groups/{groupId}/request-approval`
3. Body: `{"description": "Enhancing existing application with new features"}`
4. Verify group status updated

**Expected Result**: 
- Status: 200
- Group status: 'pending_approval'

---

## Data Operations Flow Tests

### TC-DATA-001: Select Data with Limits
**Objective**: Test SELECT operation with automatic limits
**Steps**:
1. Set valid signature
2. GET `/api/v1/database/schemas/{schema}/tables/{table}/data?limit=10`
3. Verify results limited

**Expected Result**: 
- Status: 200
- Results limited to 10 records
- Operation name: "INSTANT_000000000000001_SELECT_USERS_WITHOUT_FILTERS"

### TC-DATA-002: Select Data with Filters
**Objective**: Test SELECT operation with filters
**Steps**:
1. Set valid signature
2. GET `/api/v1/database/schemas/{schema}/tables/{table}/data?filters[0][column]=name&filters[0][operator]=like&filters[0][value]=%John%`
3. Verify filtered results

**Expected Result**: 
- Status: 200
- Results filtered by name containing 'John'
- Operation name: "INSTANT_000000000000001_SELECT_USERS_WITH_FILTERS"

### TC-DATA-003: Insert Data
**Objective**: Test INSERT operation
**Steps**:
1. Set valid signature
2. POST `/api/v1/database/schemas/{schema}/tables/{table}/data`
3. Body: `{"data": {"name": "John Doe", "email": "john@example.com"}}`
4. Verify data inserted

**Expected Result**: 
- Status: 201
- Data inserted successfully
- Operation name: "INSTANT_000000000000001_INSERT_USERS_RECORD"

### TC-DATA-004: Update Data
**Objective**: Test UPDATE operation
**Steps**:
1. Set valid signature
2. PATCH `/api/v1/database/schemas/{schema}/tables/{table}/data/{id}`
3. Body: `{"data": {"name": "Jane Doe", "phone": "+1234567890"}}`
4. Verify data updated

**Expected Result**: 
- Status: 200
- Data updated successfully
- Operation name: "INSTANT_000000000000001_UPDATE_USERS_RECORD"

### TC-DATA-005: Soft Delete Data
**Objective**: Test soft delete operation
**Steps**:
1. Set valid signature
2. DELETE `/api/v1/database/schemas/{schema}/tables/{table}/data/{id}`
3. Verify soft delete performed

**Expected Result**: 
- Status: 204
- Record soft deleted (deleted_at set)
- Soft delete logged
- Operation name: "INSTANT_000000000000001_DELETE_USERS_RECORD"

---

## Admin Approval Workflow Tests

### TC-ADMIN-001: Get Pending Batches
**Objective**: Test admin retrieving pending batches
**Steps**:
1. Set valid signature (admin)
2. GET `/api/v1/database/operation-groups/pending`
3. Verify pending batches returned

**Expected Result**: 
- Status: 200
- Pending batches listed
- Admin can see operation details

### TC-ADMIN-002: Approve Batch
**Objective**: Test admin approving a batch
**Steps**:
1. Set valid signature (admin)
2. POST `/api/v1/database/operation-groups/{groupId}/approve`
3. Body: `{"admin_notes": "Approved for production deployment"}`
4. Verify batch approved and queued

**Expected Result**: 
- Status: 200
- Group status: 'approved'
- Batch queued for processing
- Operations execute in order

### TC-ADMIN-003: Reject Batch
**Objective**: Test admin rejecting a batch
**Steps**:
1. Set valid signature (admin)
2. POST `/api/v1/database/operation-groups/{groupId}/reject`
3. Body: `{"admin_notes": "Rejected due to potential data loss"}`
4. Verify batch rejected

**Expected Result**: 
- Status: 200
- Group status: 'rejected'
- User notified of rejection

### TC-ADMIN-004: Get Tenant Security Logs
**Objective**: Test admin retrieving security logs
**Steps**:
1. Set valid signature (admin)
2. GET `/api/v1/database/tenants/{tenantId}/security-logs`
3. Verify security logs returned

**Expected Result**: 
- Status: 200
- Security logs listed
- Admin can monitor tenant behavior

### TC-ADMIN-005: Block Tenant
**Objective**: Test admin blocking a tenant
**Steps**:
1. Set valid signature (admin)
2. POST `/api/v1/database/tenants/{tenantId}/block`
3. Body: `{"reason": "Multiple security violations"}`
4. Verify tenant blocked

**Expected Result**: 
- Status: 200
- Tenant blocked
- Future requests from tenant fail

### TC-ADMIN-006: Unblock Tenant
**Objective**: Test admin unblocking a tenant
**Steps**:
1. Set valid signature (admin)
2. POST `/api/v1/database/tenants/{tenantId}/unblock`
3. Body: `{"reason": "Security issue resolved"}`
4. Verify tenant unblocked

**Expected Result**: 
- Status: 200
- Tenant unblocked
- Tenant can make requests again

---

## Soft Delete Management Tests

### TC-SOFT-001: Get Soft Deleted Records
**Objective**: Test retrieving soft deleted records
**Steps**:
1. Set valid signature
2. GET `/api/v1/database/schemas/{schema}/tables/{table}/soft-deleted`
3. Verify soft deleted records returned

**Expected Result**: 
- Status: 200
- Soft deleted records listed
- Original data preserved

### TC-SOFT-002: Recover Soft Deleted Record
**Objective**: Test recovering soft deleted record
**Steps**:
1. Set valid signature
2. POST `/api/v1/database/schemas/{schema}/tables/{table}/soft-deleted/{id}/recover`
3. Verify record recovered

**Expected Result**: 
- Status: 200
- Record recovered (deleted_at set to NULL)
- Record available in normal queries

### TC-SOFT-003: Permanently Delete Record
**Objective**: Test permanent deletion of soft deleted record
**Steps**:
1. Set valid signature
2. DELETE `/api/v1/database/schemas/{schema}/tables/{table}/soft-deleted/{id}/permanent`
3. Verify record permanently deleted

**Expected Result**: 
- Status: 204
- Record permanently deleted
- Cannot be recovered

---

## Error Handling Tests

### TC-ERROR-001: Validation Error
**Objective**: Test validation error handling
**Steps**:
1. Set valid signature
2. POST `/api/v1/database/schemas/{schema}/tables` with invalid data
3. Verify validation error response

**Expected Result**: 
- Status: 422
- Error code: 'VALIDATION_ERROR'
- Detailed validation messages

### TC-ERROR-002: Database Connection Error
**Objective**: Test database connection error handling
**Steps**:
1. Set valid signature
2. Disconnect database
3. Make any database request
4. Verify error handling

**Expected Result**: 
- Status: 500
- Safe error message (no sensitive details)
- Error logged for debugging

### TC-ERROR-003: Tenant Blocked Error
**Objective**: Test tenant blocked error handling
**Steps**:
1. Set signature for blocked tenant
2. Make any request
3. Verify tenant blocked response

**Expected Result**: 
- Status: 403
- Error code: 'TENANT_BLOCKED'
- Clear blocking reason

### TC-ERROR-004: Operation Not Permitted
**Objective**: Test operation not permitted error
**Steps**:
1. Set valid signature
2. Attempt prohibited operation
3. Verify error response

**Expected Result**: 
- Status: 403
- Error code: 'OPERATION_NOT_PERMITTED'
- Clear denial reason

---

## Performance & Security Tests

### TC-PERF-001: Query Limit Enforcement
**Objective**: Test query limit enforcement
**Steps**:
1. Set valid signature
2. SELECT query without LIMIT
3. Verify automatic limit application

**Expected Result**: 
- Query limited to 1000 records
- Response indicates limitation
- Performance maintained

### TC-PERF-002: Large Batch Processing
**Objective**: Test processing large batch of operations
**Steps**:
1. Set valid signature
2. Create 50+ operations in batch
3. Request approval and process
4. Verify all operations execute

**Expected Result**: 
- All operations processed
- No timeout errors
- Proper status tracking

### TC-SEC-001: SQL Injection Prevention
**Objective**: Test SQL injection prevention
**Steps**:
1. Set valid signature
2. Attempt SQL injection in raw query
3. Verify injection blocked

**Expected Result**: 
- Injection attempt blocked
- Query rejected
- Security logged

### TC-SEC-002: Rate Limiting
**Objective**: Test rate limiting
**Steps**:
1. Set valid signature
2. Make rapid requests
3. Verify rate limiting applied

**Expected Result**: 
- Rate limiting applied
- Requests throttled
- System stability maintained

---

## Complete User Journey Tests

### TC-JOURNEY-001: E-commerce Application Build
**Objective**: Complete journey of building e-commerce application
**Steps**:
1. **Phase 1**: Create users, products, orders tables (Fresh App DB)
2. **Phase 2**: Request batch approval
3. **Phase 3**: Admin approves batch
4. **Phase 4**: Tables created successfully
5. **Phase 5**: Insert test data (instant operations)
6. **Phase 6**: Query data with filters
7. **Phase 7**: Test soft delete and recovery

**Expected Result**: 
- Complete application built successfully
- All operations tracked and logged
- Data operations work correctly

### TC-JOURNEY-002: Application Enhancement
**Objective**: Complete journey of enhancing existing application
**Steps**:
1. **Phase 1**: Add columns to existing tables (Modify App DB)
2. **Phase 2**: Create new tables
3. **Phase 3**: Add foreign keys
4. **Phase 4**: Request batch approval
5. **Phase 5**: Admin approves batch
6. **Phase 6**: Changes applied successfully
7. **Phase 7**: Continue using enhanced application

**Expected Result**: 
- Application enhanced successfully
- No data loss during modifications
- New features work correctly

### TC-JOURNEY-003: Data Management Operations
**Objective**: Complete journey of data management
**Steps**:
1. **Phase 1**: Query existing data with filters
2. **Phase 2**: Insert new records
3. **Phase 3**: Update existing records
4. **Phase 4**: Soft delete records
5. **Phase 5**: Recover soft deleted records
6. **Phase 6**: Permanently delete records
7. **Phase 7**: Use raw queries for complex operations

**Expected Result**: 
- All data operations work correctly
- Soft delete system functions properly
- Raw queries execute safely

---

## Test Execution Order

### Phase 1: Setup & Authentication
1. TC-AUTH-001 to TC-AUTH-004
2. TC-META-001 to TC-META-004

### Phase 2: Core API Testing
3. TC-RAW-001 to TC-RAW-006
4. TC-ERROR-001 to TC-ERROR-004

### Phase 3: Operation Flows
5. TC-FRESH-001 to TC-FRESH-004
6. TC-MODIFY-001 to TC-MODIFY-004
7. TC-DATA-001 to TC-DATA-005

### Phase 4: Admin Operations
8. TC-ADMIN-001 to TC-ADMIN-006
9. TC-SOFT-001 to TC-SOFT-003

### Phase 5: Performance & Security
10. TC-PERF-001 to TC-PERF-002
11. TC-SEC-001 to TC-SEC-002

### Phase 6: Complete Journeys
12. TC-JOURNEY-001 to TC-JOURNEY-003

---

## Test Data Requirements

### Test Tables
- `users` - Basic user information
- `products` - Product catalog
- `orders` - Order management
- `user_profiles` - Extended user data

### Test Data Sets
- 100+ users with various data
- 50+ products with different categories
- 200+ orders with different statuses
- Soft deleted records for recovery testing

### Test Signatures
- Valid tenant signature
- Invalid signature
- Blocked tenant signature
- Admin signature

---

## Success Criteria

### Functional Requirements
- All APIs return correct responses
- Batch operations require admin approval
- Instant operations execute immediately
- Soft delete system works correctly
- Raw queries are validated and limited

### Security Requirements
- Signature verification works correctly
- Tenant isolation maintained
- SQL injection prevented
- Rate limiting applied
- Security violations logged

### Performance Requirements
- Response times under 2 seconds
- Query limits enforced
- Large batches process successfully
- System remains stable under load

### User Experience Requirements
- Clear error messages
- Consistent response format
- Proper HTTP status codes
- Multilingual support
- Comprehensive logging

This comprehensive test suite covers all aspects of the Database API system, ensuring robust functionality, security, and performance across all operation modes and user journeys.
