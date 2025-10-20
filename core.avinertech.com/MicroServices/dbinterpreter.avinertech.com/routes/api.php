<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\V1\DatabaseController;
use App\Http\Controllers\Api\V1\SchemaController;
use App\Http\Controllers\Api\V1\TableController;
use App\Http\Controllers\Api\V1\OperationController;
use App\Http\Controllers\Api\V1\AdminController;
use App\Http\Controllers\Api\V1\MetadataController;
use App\Http\Controllers\Api\V1\RawQueryController;
use App\Http\Controllers\Api\V1\ModuleController;
use App\Http\Controllers\Api\V1\ModuleMetadataController;
use App\Http\Controllers\EnhancedModuleController;
use App\Http\Middleware\SignatureVerificationMiddleware;
use App\Http\Middleware\TenantSecurityMiddleware;
use App\Http\Middleware\IdempotencyMiddleware;
use App\Http\Middleware\AdminMiddleware;
use App\Http\Middleware\SecurityParameterMiddleware;

// API v1 Database routes with tenant support
Route::prefix('v1/database')->middleware([
    SecurityParameterMiddleware::class,
    SignatureVerificationMiddleware::class, 
    TenantSecurityMiddleware::class, 
    IdempotencyMiddleware::class
])->group(function () {
    
    // Capability probe
    Route::get('/capabilities', [DatabaseController::class, 'capabilities']);
    
    // Schema management
    Route::get('/schemas', [SchemaController::class, 'index']);
    Route::get('/schema', [SchemaController::class, 'show']);
    
    // Table management (Batch operations - require approval)
    Route::get('/tables', [TableController::class, 'index']);
    Route::get('/tables/{tableName}', [TableController::class, 'show']);
    Route::post('/tables', [TableController::class, 'store']); // Batch
    Route::patch('/tables/{tableName}', [TableController::class, 'update']); // Batch
    
    // Column management (Batch operations)
    Route::post('/tables/{tableName}/columns', [TableController::class, 'addColumn']); // Batch
    Route::patch('/tables/{tableName}/columns/{columnName}', [TableController::class, 'updateColumn']); // Batch
    Route::delete('/tables/{tableName}/columns/{columnName}', [TableController::class, 'deleteColumn']); // Batch
    
    // Index management (Batch operations)
    Route::post('/tables/{tableName}/indexes', [TableController::class, 'addIndex']); // Batch
    Route::patch('/tables/{tableName}/indexes/{indexName}', [TableController::class, 'updateIndex']); // Batch
    Route::delete('/tables/{tableName}/indexes/{indexName}', [TableController::class, 'deleteIndex']); // Batch
    
    // Foreign key management (Batch operations)
    Route::post('/tables/{tableName}/foreign-keys', [TableController::class, 'addForeignKey']); // Batch
    Route::patch('/tables/{tableName}/foreign-keys/{constraintName}', [TableController::class, 'updateForeignKey']); // Batch
    Route::delete('/tables/{tableName}/foreign-keys/{constraintName}', [TableController::class, 'deleteForeignKey']); // Batch
    
    // Check constraints (Batch operations)
    Route::post('/tables/{tableName}/checks', [TableController::class, 'addCheck']); // Batch
    Route::patch('/tables/{tableName}/checks/{constraintName}', [TableController::class, 'updateCheck']); // Batch
    Route::delete('/tables/{tableName}/checks/{constraintName}', [TableController::class, 'deleteCheck']); // Batch
    
    // Partitioning (Batch operations)
    Route::post('/tables/{tableName}/partitions', [TableController::class, 'enablePartitioning']); // Batch
    Route::post('/tables/{tableName}/partitions/add', [TableController::class, 'addPartition']); // Batch
    Route::post('/tables/{tableName}/partitions/reorganize', [TableController::class, 'reorganizePartitions']); // Batch
    
    // Data management (Instant operations)
    Route::get('/tables/{tableName}/data', [TableController::class, 'getData']); // Instant
    Route::post('/tables/{tableName}/data', [TableController::class, 'insertData']); // Instant
    Route::patch('/tables/{tableName}/data/{rowId}', [TableController::class, 'updateData']); // Instant
    Route::delete('/tables/{tableName}/data/{rowId}', [TableController::class, 'deleteData']); // Instant (soft delete)
    
    // Soft delete management
    Route::get('/tables/{tableName}/soft-deleted', [TableController::class, 'getSoftDeleted']); // Instant
    Route::post('/tables/{tableName}/soft-deleted/{recordId}/recover', [TableController::class, 'recoverRecord']); // Instant
    Route::delete('/tables/{tableName}/soft-deleted/{recordId}/permanent', [TableController::class, 'permanentlyDeleteRecord']); // Instant
    
    // Metadata APIs (cached on frontend)
    Route::get('/metadata/filters', [MetadataController::class, 'getFilters']); // Get all filter operators
    Route::get('/metadata/aggregations', [MetadataController::class, 'getAggregations']); // Get all aggregation functions
    Route::get('/metadata/columns', [MetadataController::class, 'getAllColumns']); // Get all tenant table columns
    
    // Raw Query API (strict validation)
    Route::post('/raw-query', [RawQueryController::class, 'execute']); // Execute validated raw SELECT queries
    
    
    // Module Metadata APIs
    Route::prefix('module-metadata')->group(function () {
        Route::get('/module-categories', [ModuleMetadataController::class, 'getModuleCategories']); // Get module categories
        Route::get('/data-types', [ModuleMetadataController::class, 'getDataTypes']); // Get all data types
        Route::get('/data-types/categories', [ModuleMetadataController::class, 'getDataTypesByCategory']); // Get data types by category
        Route::get('/form-controls', [ModuleMetadataController::class, 'getFormControls']); // Get form controls
        Route::get('/validation-rules', [ModuleMetadataController::class, 'getValidationRules']); // Get validation rules
        Route::get('/module-types', [ModuleMetadataController::class, 'getModuleTypes']); // Get module types
        Route::get('/layout-types', [ModuleMetadataController::class, 'getLayoutTypes']); // Get layout types
    });
    
    // Enhanced Module Management APIs
    Route::prefix('enhanced-modules')->group(function () {        
        Route::get('/', [EnhancedModuleController::class, 'getModuleDefinitions']); // Get module definitions
        Route::post('/', [EnhancedModuleController::class, 'createModuleDefinition']); // Create module definition
        Route::get('/groups', [EnhancedModuleController::class, 'getModuleGroups']); // Get module groups (requires tenant_id, optional schema_name)
        Route::get('/{id}', [EnhancedModuleController::class, 'getModuleDefinition']); // Get specific module definition
        Route::patch('/{id}', [EnhancedModuleController::class, 'updateModuleDefinition']); // Update module definition

    });
    
    // SQL Preview (never executes)
    Route::post('/preview-sql', [DatabaseController::class, 'previewSql']);
    
    // Operations (job-based DDL)
    Route::post('/operations', [OperationController::class, 'create']);
    Route::get('/operations/{operationId}', [OperationController::class, 'show']);
    Route::get('/operations', [OperationController::class, 'index']);

    // Admin approval operations
    Route::post('/operation-groups/{groupId}/approve', [AdminController::class, 'approveBatch']);
    Route::post('/operation-groups/{groupId}/reject', [AdminController::class, 'rejectBatch']);
    Route::get('/operation-groups/pending', [AdminController::class, 'getPendingBatches']);

    // Operation Groups (batch management)
    Route::get('/operation-groups/{groupId}', [OperationController::class, 'getGroup']);
    Route::get('/operation-groups', [OperationController::class, 'getGroups']);
    Route::post('/operation-groups/{groupId}/request-approval', [OperationController::class, 'requestApproval']);
    
    // Tenant management
    Route::get('/tenants/{tenantId}/security-logs', [AdminController::class, 'getTenantSecurityLogs']);
    Route::post('/tenants/{tenantId}/unblock', [AdminController::class, 'unblockTenant']);
    Route::get('/tenants/blocked', [AdminController::class, 'getBlockedTenants']);
    
    // System monitoring
    Route::get('/operations/stats', [AdminController::class, 'getOperationStats']);
    Route::get('/system/health', [AdminController::class, 'getSystemHealth']);
});

// Admin routes (separate middleware for admin operations)
Route::prefix('api/v1/admin')->middleware([
    \Laravel\Sanctum\Http\Middleware\EnsureFrontendRequestsAreStateful::class,
    AdminMiddleware::class
])->group(function () {
    
 
});