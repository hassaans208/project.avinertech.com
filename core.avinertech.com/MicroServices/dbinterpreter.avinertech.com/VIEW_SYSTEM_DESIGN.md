# View Rendering System Design

This document outlines the comprehensive design for a view rendering system that supports column management, password encryption, metadata APIs, and view caching.

## Table of Contents

1. [Database Schema Design](#database-schema-design)
2. [Models and Relationships](#models-and-relationships)
3. [API Endpoints](#api-endpoints)
4. [View Types and Caching](#view-types-and-caching)
5. [Password Encryption System](#password-encryption-system)
6. [Metadata API Extensions](#metadata-api-extensions)
7. [Implementation Examples](#implementation-examples)

---

## Database Schema Design

### 1. View Definitions Table

```sql
CREATE TABLE view_definitions (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    tenant_id VARCHAR(36) NOT NULL,
    schema_name VARCHAR(64) NOT NULL,
    table_name VARCHAR(64) NOT NULL,
    view_name VARCHAR(100) NOT NULL,
    view_type ENUM('create', 'update', 'list', 'analytics') NOT NULL,
    title VARCHAR(255) NOT NULL,
    description TEXT NULL,
    is_active BOOLEAN DEFAULT TRUE,
    cache_key VARCHAR(255) UNIQUE NULL,
    cached_content LONGTEXT NULL,
    cache_expires_at TIMESTAMP NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    
    INDEX idx_tenant_table (tenant_id, schema_name, table_name),
    INDEX idx_view_type (view_type),
    INDEX idx_cache_key (cache_key),
    UNIQUE KEY unique_view_name (tenant_id, schema_name, table_name, view_name, view_type)
);
```

### 2. View Column Configurations Table

```sql
CREATE TABLE view_column_configurations (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    view_definition_id BIGINT UNSIGNED NOT NULL,
    column_name VARCHAR(64) NOT NULL,
    display_name VARCHAR(255) NULL,
    is_visible BOOLEAN DEFAULT TRUE,
    is_editable BOOLEAN DEFAULT TRUE,
    is_required BOOLEAN DEFAULT FALSE,
    is_searchable BOOLEAN DEFAULT FALSE,
    is_sortable BOOLEAN DEFAULT TRUE,
    display_order INT DEFAULT 0,
    column_width INT NULL,
    data_type VARCHAR(50) NOT NULL,
    is_password_field BOOLEAN DEFAULT FALSE,
    validation_rules JSON NULL,
    display_format VARCHAR(100) NULL,
    placeholder_text VARCHAR(255) NULL,
    help_text TEXT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    
    FOREIGN KEY (view_definition_id) REFERENCES view_definitions(id) ON DELETE CASCADE,
    INDEX idx_view_definition (view_definition_id),
    INDEX idx_display_order (display_order),
    UNIQUE KEY unique_view_column (view_definition_id, column_name)
);
```

### 3. View Layout Configurations Table

```sql
CREATE TABLE view_layout_configurations (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    view_definition_id BIGINT UNSIGNED NOT NULL,
    layout_type ENUM('form', 'table', 'grid', 'card') NOT NULL,
    layout_config JSON NOT NULL,
    responsive_config JSON NULL,
    theme_config JSON NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    
    FOREIGN KEY (view_definition_id) REFERENCES view_definitions(id) ON DELETE CASCADE,
    INDEX idx_view_definition (view_definition_id),
    INDEX idx_layout_type (layout_type)
);
```

### 4. View Permissions Table

```sql
CREATE TABLE view_permissions (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    view_definition_id BIGINT UNSIGNED NOT NULL,
    user_id BIGINT UNSIGNED NULL,
    role_id BIGINT UNSIGNED NULL,
    permission_type ENUM('read', 'write', 'admin') NOT NULL,
    granted_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    granted_by BIGINT UNSIGNED NOT NULL,
    
    FOREIGN KEY (view_definition_id) REFERENCES view_definitions(id) ON DELETE CASCADE,
    INDEX idx_view_definition (view_definition_id),
    INDEX idx_user (user_id),
    INDEX idx_role (role_id),
    UNIQUE KEY unique_user_permission (view_definition_id, user_id, permission_type),
    UNIQUE KEY unique_role_permission (view_definition_id, role_id, permission_type)
);
```

### 5. View Cache Management Table

```sql
CREATE TABLE view_cache_management (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    tenant_id VARCHAR(36) NOT NULL,
    cache_key VARCHAR(255) UNIQUE NOT NULL,
    view_type ENUM('create', 'update', 'list', 'analytics') NOT NULL,
    table_name VARCHAR(64) NOT NULL,
    cache_size BIGINT UNSIGNED NOT NULL,
    hit_count INT DEFAULT 0,
    last_accessed_at TIMESTAMP NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    
    INDEX idx_tenant (tenant_id),
    INDEX idx_cache_key (cache_key),
    INDEX idx_last_accessed (last_accessed_at)
);
```

---

## Models and Relationships

### 1. ViewDefinition Model

```php
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ViewDefinition extends Model
{
    protected $fillable = [
        'tenant_id',
        'schema_name',
        'table_name',
        'view_name',
        'view_type',
        'title',
        'description',
        'is_active',
        'cache_key',
        'cached_content',
        'cache_expires_at'
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'cache_expires_at' => 'datetime'
    ];

    public function columnConfigurations(): HasMany
    {
        return $this->hasMany(ViewColumnConfiguration::class);
    }

    public function layoutConfigurations(): HasMany
    {
        return $this->hasMany(ViewLayoutConfiguration::class);
    }

    public function permissions(): HasMany
    {
        return $this->hasMany(ViewPermission::class);
    }

    public function scopeForTenant($query, string $tenantId)
    {
        return $query->where('tenant_id', $tenantId);
    }

    public function scopeForTable($query, string $schemaName, string $tableName)
    {
        return $query->where('schema_name', $schemaName)
                    ->where('table_name', $tableName);
    }

    public function scopeByType($query, string $viewType)
    {
        return $query->where('view_type', $viewType);
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function isCacheValid(): bool
    {
        return $this->cached_content && 
               $this->cache_expires_at && 
               $this->cache_expires_at->isFuture();
    }

    public function getCacheKey(): string
    {
        return $this->cache_key ?? $this->generateCacheKey();
    }

    private function generateCacheKey(): string
    {
        return "view_{$this->tenant_id}_{$this->schema_name}_{$this->table_name}_{$this->view_name}_{$this->view_type}";
    }
}
```

### 2. ViewColumnConfiguration Model

```php
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ViewColumnConfiguration extends Model
{
    protected $fillable = [
        'view_definition_id',
        'column_name',
        'display_name',
        'is_visible',
        'is_editable',
        'is_required',
        'is_searchable',
        'is_sortable',
        'display_order',
        'column_width',
        'data_type',
        'is_password_field',
        'validation_rules',
        'display_format',
        'placeholder_text',
        'help_text'
    ];

    protected $casts = [
        'is_visible' => 'boolean',
        'is_editable' => 'boolean',
        'is_required' => 'boolean',
        'is_searchable' => 'boolean',
        'is_sortable' => 'boolean',
        'is_password_field' => 'boolean',
        'validation_rules' => 'array'
    ];

    public function viewDefinition(): BelongsTo
    {
        return $this->belongsTo(ViewDefinition::class);
    }

    public function scopeVisible($query)
    {
        return $query->where('is_visible', true);
    }

    public function scopeEditable($query)
    {
        return $query->where('is_editable', true);
    }

    public function scopeSearchable($query)
    {
        return $query->where('is_searchable', true);
    }

    public function scopeSortable($query)
    {
        return $query->where('is_sortable', true);
    }

    public function scopeOrdered($query)
    {
        return $query->orderBy('display_order');
    }

    public function getValidationRulesAttribute($value)
    {
        return $value ? json_decode($value, true) : [];
    }

    public function setValidationRulesAttribute($value)
    {
        $this->attributes['validation_rules'] = json_encode($value);
    }
}
```

### 3. ViewLayoutConfiguration Model

```php
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ViewLayoutConfiguration extends Model
{
    protected $fillable = [
        'view_definition_id',
        'layout_type',
        'layout_config',
        'responsive_config',
        'theme_config'
    ];

    protected $casts = [
        'layout_config' => 'array',
        'responsive_config' => 'array',
        'theme_config' => 'array'
    ];

    public function viewDefinition(): BelongsTo
    {
        return $this->belongsTo(ViewDefinition::class);
    }

    public function scopeByType($query, string $layoutType)
    {
        return $query->where('layout_type', $layoutType);
    }
}
```

---

## API Endpoints

### 1. View Management APIs

#### 1.1 Create View Definition

**POST** `/api/v1/database/views`

```json
{
  "table_name": "users",
  "view_name": "user_management",
  "view_type": "list",
  "title": "User Management",
  "description": "Manage system users",
  "column_configurations": [
    {
      "column_name": "id",
      "display_name": "ID",
      "is_visible": true,
      "is_editable": false,
      "is_required": false,
      "is_searchable": true,
      "is_sortable": true,
      "display_order": 1,
      "data_type": "BIGINT"
    },
    {
      "column_name": "email",
      "display_name": "Email Address",
      "is_visible": true,
      "is_editable": true,
      "is_required": true,
      "is_searchable": true,
      "is_sortable": true,
      "display_order": 2,
      "data_type": "VARCHAR",
      "validation_rules": {
        "required": true,
        "email": true,
        "max": 255
      },
      "placeholder_text": "Enter email address"
    },
    {
      "column_name": "password",
      "display_name": "Password",
      "is_visible": true,
      "is_editable": true,
      "is_required": true,
      "is_searchable": false,
      "is_sortable": false,
      "display_order": 3,
      "data_type": "PASSWORD",
      "is_password_field": true,
      "validation_rules": {
        "required": true,
        "min": 8
      },
      "placeholder_text": "Enter password"
    }
  ],
  "layout_configuration": {
    "layout_type": "table",
    "layout_config": {
      "columns_per_row": 3,
      "show_actions": true,
      "pagination": {
        "per_page": 25,
        "show_page_info": true
      }
    }
  }
}
```

#### 1.2 Get View Definition

**GET** `/api/v1/database/views/{viewId}`

**Response**:
```json
{
  "status": "success",
  "message": "View definition retrieved successfully",
  "data": {
    "id": 1,
    "table_name": "users",
    "view_name": "user_management",
    "view_type": "list",
    "title": "User Management",
    "description": "Manage system users",
    "is_active": true,
    "column_configurations": [
      {
        "id": 1,
        "column_name": "id",
        "display_name": "ID",
        "is_visible": true,
        "is_editable": false,
        "is_required": false,
        "is_searchable": true,
        "is_sortable": true,
        "display_order": 1,
        "data_type": "BIGINT",
        "validation_rules": {},
        "placeholder_text": null,
        "help_text": null
      }
    ],
    "layout_configuration": {
      "id": 1,
      "layout_type": "table",
      "layout_config": {
        "columns_per_row": 3,
        "show_actions": true,
        "pagination": {
          "per_page": 25,
          "show_page_info": true
        }
      }
    },
    "cached_content": null,
    "cache_expires_at": null,
    "created_at": "2024-01-01T00:00:00Z",
    "updated_at": "2024-01-01T00:00:00Z"
  }
}
```

#### 1.3 Update View Definition

**PATCH** `/api/v1/database/views/{viewId}`

#### 1.4 Delete View Definition

**DELETE** `/api/v1/database/views/{viewId}`

#### 1.5 List View Definitions

**GET** `/api/v1/database/views`

**Query Parameters**:
- `table_name` (optional): Filter by table name
- `view_type` (optional): Filter by view type
- `is_active` (optional): Filter by active status

### 2. View Rendering APIs

#### 2.1 Build and Cache View

**POST** `/api/v1/database/views/{viewId}/build`

```json
{
  "force_rebuild": false,
  "cache_duration": 3600
}
```

**Response**:
```json
{
  "status": "success",
  "message": "View built and cached successfully",
  "data": {
    "view_id": 1,
    "cache_key": "view_tenant123_schema_users_user_management_list",
    "cache_expires_at": "2024-01-01T01:00:00Z",
    "build_time": "0.5s",
    "cache_size": "2.5KB"
  }
}
```

#### 2.2 Get Cached View

**GET** `/api/v1/database/views/{viewId}/render`

**Response**:
```json
{
  "status": "success",
  "message": "View rendered successfully",
  "data": {
    "view_id": 1,
    "view_type": "list",
    "title": "User Management",
    "html_content": "<div class=\"view-container\">...</div>",
    "css_content": ".view-container { ... }",
    "js_content": "function initializeView() { ... }",
    "metadata": {
      "columns": [...],
      "layout": {...},
      "permissions": {...}
    },
    "cache_info": {
      "cache_key": "view_tenant123_schema_users_user_management_list",
      "cached_at": "2024-01-01T00:00:00Z",
      "expires_at": "2024-01-01T01:00:00Z",
      "hit_count": 15
    }
  }
}
```

#### 2.3 Build All Views for Table

**POST** `/api/v1/database/tables/{tableName}/views/build`

```json
{
  "view_types": ["create", "update", "list", "analytics"],
  "force_rebuild": false,
  "cache_duration": 3600
}
```

### 3. Column Management APIs

#### 3.1 Update Column Configuration

**PATCH** `/api/v1/database/views/{viewId}/columns/{columnName}`

```json
{
  "display_name": "User ID",
  "is_visible": true,
  "is_editable": false,
  "is_required": false,
  "is_searchable": true,
  "is_sortable": true,
  "display_order": 1,
  "validation_rules": {
    "required": false
  },
  "placeholder_text": "Enter user ID",
  "help_text": "Unique identifier for the user"
}
```

#### 3.2 Reorder Columns

**POST** `/api/v1/database/views/{viewId}/columns/reorder`

```json
{
  "column_orders": [
    {
      "column_name": "id",
      "display_order": 1
    },
    {
      "column_name": "email",
      "display_order": 2
    },
    {
      "column_name": "name",
      "display_order": 3
    }
  ]
}
```

---

## View Types and Caching

### 1. View Types

#### 1.1 Create View
- **Purpose**: Form for creating new records
- **Features**: Required field validation, password encryption, field grouping
- **Layout**: Form layout with validation

#### 1.2 Update View
- **Purpose**: Form for editing existing records
- **Features**: Pre-populated fields, conditional editing, audit trail
- **Layout**: Form layout with edit controls

#### 1.3 List View
- **Purpose**: Table/grid for displaying multiple records
- **Features**: Pagination, sorting, filtering, search, bulk actions
- **Layout**: Table/grid layout with controls

#### 1.4 Analytics View
- **Purpose**: Dashboard with charts and metrics
- **Features**: Aggregations, charts, filters, date ranges
- **Layout**: Dashboard layout with widgets

### 2. Caching Strategy

#### 2.1 Cache Keys
```php
// Format: view_{tenant_id}_{schema}_{table}_{view_name}_{view_type}
$cacheKey = "view_{$tenantId}_{$schemaName}_{$tableName}_{$viewName}_{$viewType}";
```

#### 2.2 Cache Content
```php
$cacheContent = [
    'html_content' => $renderedHtml,
    'css_content' => $compiledCss,
    'js_content' => $compiledJs,
    'metadata' => $viewMetadata,
    'dependencies' => $assetDependencies,
    'version' => $viewVersion
];
```

#### 2.3 Cache Invalidation
- **Automatic**: When view definition changes
- **Manual**: Via API endpoint
- **Scheduled**: Based on TTL
- **Event-based**: When table schema changes

---

## Password Encryption System

### 1. Password Field Configuration

```php
// Column configuration for password fields
$passwordColumn = [
    'column_name' => 'password',
    'data_type' => 'PASSWORD',
    'is_password_field' => true,
    'validation_rules' => [
        'required' => true,
        'min' => 8,
        'max' => 128,
        'pattern' => '^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[@$!%*?&])[A-Za-z\d@$!%*?&]'
    ],
    'display_format' => 'password',
    'placeholder_text' => 'Enter secure password'
];
```

### 2. Password Encryption Service

```php
<?php

namespace App\Services;

use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Crypt;

class PasswordEncryptionService
{
    public function encryptPassword(string $password): string
    {
        return Hash::make($password);
    }

    public function verifyPassword(string $password, string $hash): bool
    {
        return Hash::check($password, $hash);
    }

    public function maskPassword(string $password): string
    {
        return str_repeat('*', strlen($password));
    }

    public function generatePassword(int $length = 12): string
    {
        $chars = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789!@#$%^&*';
        return substr(str_shuffle($chars), 0, $length);
    }

    public function validatePasswordStrength(string $password): array
    {
        $errors = [];
        
        if (strlen($password) < 8) {
            $errors[] = 'Password must be at least 8 characters long';
        }
        
        if (!preg_match('/[a-z]/', $password)) {
            $errors[] = 'Password must contain at least one lowercase letter';
        }
        
        if (!preg_match('/[A-Z]/', $password)) {
            $errors[] = 'Password must contain at least one uppercase letter';
        }
        
        if (!preg_match('/\d/', $password)) {
            $errors[] = 'Password must contain at least one number';
        }
        
        if (!preg_match('/[@$!%*?&]/', $password)) {
            $errors[] = 'Password must contain at least one special character';
        }
        
        return $errors;
    }
}
```

### 3. Password Field Rendering

```php
// In view rendering
if ($column->is_password_field) {
    $fieldHtml = '<input type="password" 
                        name="' . $column->column_name . '" 
                        class="form-control password-field" 
                        placeholder="' . $column->placeholder_text . '"
                        data-validation="' . json_encode($column->validation_rules) . '">';
    
    if ($viewType === 'update') {
        $fieldHtml .= '<small class="form-text text-muted">Leave blank to keep current password</small>';
    }
}
```

---

## Metadata API Extensions

### 1. Enhanced Data Types API

**GET** `/api/v1/database/metadata/data-types`

**Response**:
```json
{
  "status": "success",
  "message": "Data types retrieved successfully",
  "data": [
    {
      "name": "VARCHAR",
      "label": "Text",
      "description": "Variable-length character string",
      "category": "text",
      "max_length": 65535,
      "supports_encryption": false,
      "form_controls": ["text", "textarea", "email", "url"],
      "validation_rules": ["max", "min", "pattern", "required"]
    },
    {
      "name": "PASSWORD",
      "label": "Password",
      "description": "Encrypted password field",
      "category": "security",
      "max_length": 255,
      "supports_encryption": true,
      "form_controls": ["password"],
      "validation_rules": ["required", "min", "pattern"],
      "encryption_method": "bcrypt",
      "mask_display": true
    },
    {
      "name": "INT",
      "label": "Integer",
      "description": "Whole number",
      "category": "numeric",
      "min_value": -2147483648,
      "max_value": 2147483647,
      "supports_encryption": false,
      "form_controls": ["number", "range"],
      "validation_rules": ["min", "max", "required"]
    },
    {
      "name": "DECIMAL",
      "label": "Decimal",
      "description": "Decimal number with precision",
      "category": "numeric",
      "precision": 10,
      "scale": 2,
      "supports_encryption": false,
      "form_controls": ["number"],
      "validation_rules": ["min", "max", "required", "decimal"]
    },
    {
      "name": "DATE",
      "label": "Date",
      "description": "Date value",
      "category": "datetime",
      "supports_encryption": false,
      "form_controls": ["date", "datepicker"],
      "validation_rules": ["required", "date"]
    },
    {
      "name": "DATETIME",
      "label": "Date and Time",
      "description": "Date and time value",
      "category": "datetime",
      "supports_encryption": false,
      "form_controls": ["datetime", "datetimepicker"],
      "validation_rules": ["required", "datetime"]
    },
    {
      "name": "BOOLEAN",
      "label": "Boolean",
      "description": "True/false value",
      "category": "boolean",
      "supports_encryption": false,
      "form_controls": ["checkbox", "switch", "radio"],
      "validation_rules": ["required"]
    },
    {
      "name": "JSON",
      "label": "JSON",
      "description": "JSON object or array",
      "category": "complex",
      "supports_encryption": false,
      "form_controls": ["textarea", "json-editor"],
      "validation_rules": ["required", "json"]
    }
  ]
}
```

### 2. Enhanced Columns API

**GET** `/api/v1/database/metadata/columns`

**Response**:
```json
{
  "status": "success",
  "message": "Columns retrieved successfully",
  "data": [
    {
      "table_name": "users",
      "column_name": "id",
      "data_type": "BIGINT",
      "column_type": "bigint(20)",
      "nullable": "NO",
      "default_value": null,
      "is_primary_key": true,
      "is_auto_increment": true,
      "is_password_field": false,
      "supports_encryption": false,
      "form_controls": ["number"],
      "validation_rules": ["required", "min", "max"]
    },
    {
      "table_name": "users",
      "column_name": "email",
      "data_type": "VARCHAR",
      "column_type": "varchar(255)",
      "nullable": "NO",
      "default_value": null,
      "is_primary_key": false,
      "is_auto_increment": false,
      "is_password_field": false,
      "supports_encryption": false,
      "form_controls": ["text", "email"],
      "validation_rules": ["required", "email", "max"]
    },
    {
      "table_name": "users",
      "column_name": "password",
      "data_type": "PASSWORD",
      "column_type": "varchar(255)",
      "nullable": "NO",
      "default_value": null,
      "is_primary_key": false,
      "is_auto_increment": false,
      "is_password_field": true,
      "supports_encryption": true,
      "form_controls": ["password"],
      "validation_rules": ["required", "min", "pattern"],
      "encryption_method": "bcrypt"
    }
  ]
}
```

---

## Implementation Examples

### 1. View Controller

```php
<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\ViewDefinition;
use App\Services\ViewBuilderService;
use App\Services\ViewCacheService;
use Illuminate\Http\Request;

class ViewController extends Controller
{
    public function __construct(
        private ViewBuilderService $viewBuilder,
        private ViewCacheService $viewCache
    ) {}

    public function store(Request $request)
    {
        $request->validate([
            'table_name' => 'required|string',
            'view_name' => 'required|string',
            'view_type' => 'required|in:create,update,list,analytics',
            'title' => 'required|string',
            'description' => 'nullable|string',
            'column_configurations' => 'required|array',
            'layout_configuration' => 'required|array'
        ]);

        $tenantId = $request->get('tenant_id');
        $schemaName = $request->get('schema_name');

        $viewDefinition = ViewDefinition::create([
            'tenant_id' => $tenantId,
            'schema_name' => $schemaName,
            'table_name' => $request->table_name,
            'view_name' => $request->view_name,
            'view_type' => $request->view_type,
            'title' => $request->title,
            'description' => $request->description,
        ]);

        // Create column configurations
        foreach ($request->column_configurations as $config) {
            $viewDefinition->columnConfigurations()->create($config);
        }

        // Create layout configuration
        $viewDefinition->layoutConfigurations()->create($request->layout_configuration);

        return response()->json([
            'status' => 'success',
            'message' => 'View definition created successfully',
            'data' => $viewDefinition->load(['columnConfigurations', 'layoutConfigurations'])
        ], 201);
    }

    public function build(Request $request, string $viewId)
    {
        $viewDefinition = ViewDefinition::findOrFail($viewId);
        
        $forceRebuild = $request->get('force_rebuild', false);
        $cacheDuration = $request->get('cache_duration', 3600);

        if (!$forceRebuild && $viewDefinition->isCacheValid()) {
            return response()->json([
                'status' => 'success',
                'message' => 'View already cached',
                'data' => [
                    'view_id' => $viewId,
                    'cache_key' => $viewDefinition->getCacheKey(),
                    'cache_expires_at' => $viewDefinition->cache_expires_at
                ]
            ]);
        }

        $result = $this->viewBuilder->buildView($viewDefinition, $cacheDuration);

        return response()->json([
            'status' => 'success',
            'message' => 'View built and cached successfully',
            'data' => $result
        ]);
    }

    public function render(string $viewId)
    {
        $viewDefinition = ViewDefinition::findOrFail($viewId);

        if (!$viewDefinition->isCacheValid()) {
            // Auto-rebuild if cache is invalid
            $this->viewBuilder->buildView($viewDefinition);
            $viewDefinition->refresh();
        }

        $cachedContent = json_decode($viewDefinition->cached_content, true);

        return response()->json([
            'status' => 'success',
            'message' => 'View rendered successfully',
            'data' => [
                'view_id' => $viewId,
                'view_type' => $viewDefinition->view_type,
                'title' => $viewDefinition->title,
                'html_content' => $cachedContent['html_content'],
                'css_content' => $cachedContent['css_content'],
                'js_content' => $cachedContent['js_content'],
                'metadata' => $cachedContent['metadata'],
                'cache_info' => [
                    'cache_key' => $viewDefinition->getCacheKey(),
                    'cached_at' => $viewDefinition->updated_at,
                    'expires_at' => $viewDefinition->cache_expires_at
                ]
            ]
        ]);
    }
}
```

### 2. View Builder Service

```php
<?php

namespace App\Services;

use App\Models\ViewDefinition;
use Illuminate\Support\Facades\Storage;

class ViewBuilderService
{
    public function buildView(ViewDefinition $viewDefinition, int $cacheDuration = 3600): array
    {
        $startTime = microtime(true);

        // Generate HTML content
        $htmlContent = $this->generateHtmlContent($viewDefinition);
        
        // Generate CSS content
        $cssContent = $this->generateCssContent($viewDefinition);
        
        // Generate JS content
        $jsContent = $this->generateJsContent($viewDefinition);
        
        // Prepare metadata
        $metadata = $this->prepareMetadata($viewDefinition);

        $cacheContent = [
            'html_content' => $htmlContent,
            'css_content' => $cssContent,
            'js_content' => $jsContent,
            'metadata' => $metadata,
            'version' => time()
        ];

        // Update view definition with cached content
        $viewDefinition->update([
            'cached_content' => json_encode($cacheContent),
            'cache_expires_at' => now()->addSeconds($cacheDuration),
            'cache_key' => $viewDefinition->getCacheKey()
        ]);

        $buildTime = round((microtime(true) - $startTime) * 1000, 2);

        return [
            'view_id' => $viewDefinition->id,
            'cache_key' => $viewDefinition->getCacheKey(),
            'cache_expires_at' => $viewDefinition->cache_expires_at,
            'build_time' => $buildTime . 'ms',
            'cache_size' => $this->formatBytes(strlen($viewDefinition->cached_content))
        ];
    }

    private function generateHtmlContent(ViewDefinition $viewDefinition): string
    {
        $columns = $viewDefinition->columnConfigurations()->ordered()->get();
        $layout = $viewDefinition->layoutConfigurations()->first();

        switch ($viewDefinition->view_type) {
            case 'create':
                return $this->generateCreateFormHtml($columns, $layout);
            case 'update':
                return $this->generateUpdateFormHtml($columns, $layout);
            case 'list':
                return $this->generateListTableHtml($columns, $layout);
            case 'analytics':
                return $this->generateAnalyticsDashboardHtml($columns, $layout);
            default:
                throw new \InvalidArgumentException('Invalid view type');
        }
    }

    private function generateCreateFormHtml($columns, $layout): string
    {
        $html = '<form class="view-form create-form" data-view-type="create">';
        
        foreach ($columns as $column) {
            if (!$column->is_visible) continue;
            
            $html .= '<div class="form-group">';
            $html .= '<label for="' . $column->column_name . '">' . $column->display_name . '</label>';
            
            if ($column->is_password_field) {
                $html .= '<input type="password" 
                                id="' . $column->column_name . '" 
                                name="' . $column->column_name . '" 
                                class="form-control password-field" 
                                placeholder="' . $column->placeholder_text . '">';
            } else {
                $html .= '<input type="text" 
                                id="' . $column->column_name . '" 
                                name="' . $column->column_name . '" 
                                class="form-control" 
                                placeholder="' . $column->placeholder_text . '">';
            }
            
            if ($column->help_text) {
                $html .= '<small class="form-text text-muted">' . $column->help_text . '</small>';
            }
            
            $html .= '</div>';
        }
        
        $html .= '<button type="submit" class="btn btn-primary">Create</button>';
        $html .= '</form>';
        
        return $html;
    }

    private function generateListTableHtml($columns, $layout): string
    {
        $html = '<div class="view-container list-view">';
        $html .= '<div class="table-responsive">';
        $html .= '<table class="table table-striped">';
        $html .= '<thead><tr>';
        
        foreach ($columns as $column) {
            if (!$column->is_visible) continue;
            
            $sortableClass = $column->is_sortable ? 'sortable' : '';
            $html .= '<th class="' . $sortableClass . '" data-column="' . $column->column_name . '">';
            $html .= $column->display_name;
            $html .= '</th>';
        }
        
        $html .= '<th>Actions</th>';
        $html .= '</tr></thead>';
        $html .= '<tbody id="table-body">';
        $html .= '<!-- Data will be loaded dynamically -->';
        $html .= '</tbody>';
        $html .= '</table>';
        $html .= '</div>';
        $html .= '</div>';
        
        return $html;
    }

    private function generateCssContent(ViewDefinition $viewDefinition): string
    {
        return '
        .view-container {
            padding: 20px;
        }
        .password-field {
            font-family: monospace;
        }
        .sortable {
            cursor: pointer;
        }
        .sortable:hover {
            background-color: #f8f9fa;
        }
        ';
    }

    private function generateJsContent(ViewDefinition $viewDefinition): string
    {
        return '
        function initializeView() {
            console.log("View initialized for: ' . $viewDefinition->view_name . '");
            
            // Initialize form validation
            if (document.querySelector(".view-form")) {
                initializeFormValidation();
            }
            
            // Initialize table sorting
            if (document.querySelector(".sortable")) {
                initializeTableSorting();
            }
            
            // Initialize password field
            if (document.querySelector(".password-field")) {
                initializePasswordField();
            }
        }
        
        function initializePasswordField() {
            const passwordFields = document.querySelectorAll(".password-field");
            passwordFields.forEach(field => {
                field.addEventListener("input", function() {
                    // Password strength validation
                    validatePasswordStrength(this.value);
                });
            });
        }
        
        function validatePasswordStrength(password) {
            // Implementation for password strength validation
        }
        ';
    }

    private function prepareMetadata(ViewDefinition $viewDefinition): array
    {
        return [
            'view_id' => $viewDefinition->id,
            'view_type' => $viewDefinition->view_type,
            'table_name' => $viewDefinition->table_name,
            'columns' => $viewDefinition->columnConfigurations()->ordered()->get()->toArray(),
            'layout' => $viewDefinition->layoutConfigurations()->first()?->toArray(),
            'permissions' => $viewDefinition->permissions()->get()->toArray()
        ];
    }

    private function formatBytes(int $bytes): string
    {
        $units = ['B', 'KB', 'MB', 'GB'];
        $bytes = max($bytes, 0);
        $pow = floor(($bytes ? log($bytes) : 0) / log(1024));
        $pow = min($pow, count($units) - 1);
        $bytes /= pow(1024, $pow);
        return round($bytes, 2) . ' ' . $units[$pow];
    }
}
```

---

## Summary

This design provides a comprehensive foundation for a view rendering system with:

1. **Complete Database Schema**: Tables for view definitions, column configurations, layouts, and caching
2. **Flexible Column Management**: Support for visibility, editability, validation, and password encryption
3. **Multiple View Types**: Create, update, list, and analytics views
4. **Caching System**: Built-in caching with TTL and invalidation
5. **Password Security**: Dedicated password field type with encryption
6. **Metadata APIs**: Enhanced data types and column information
7. **Extensible Architecture**: Easy to add new view types and features

The system is designed to be scalable, secure, and maintainable while providing a solid foundation for building dynamic views with caching capabilities.
