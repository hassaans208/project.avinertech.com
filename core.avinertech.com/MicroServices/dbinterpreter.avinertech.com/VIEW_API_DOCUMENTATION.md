# View Management API Documentation

This document provides comprehensive documentation for the View Management APIs and Metadata APIs for data types.

## Table of Contents

1. [View Management APIs](#view-management-apis)
2. [View Metadata APIs](#view-metadata-apis)
3. [API Examples](#api-examples)
4. [Error Handling](#error-handling)

---

## View Management APIs

### Base URL
```
/api/v1/database/views
```

### Authentication
All endpoints require tenant authentication via middleware.

---

## 1. View Definition Management

### 1.1 Get All View Definitions

**GET** `/api/v1/database/views`

Retrieves all view definitions for the authenticated tenant.

**Query Parameters:**
- `table_name` (optional): Filter by table name
- `view_type` (optional): Filter by view type (create, update, list, analytics)

**Response:**
```json
{
  "status": "success",
  "message": "View definitions retrieved successfully",
  "data": [
    {
      "id": 1,
      "tenant_id": "tenant-123",
      "schema_name": "tenant_db",
      "table_name": "users",
      "view_name": "user_management",
      "view_type": "list",
      "title": "User Management",
      "description": "Manage system users",
      "is_active": true,
      "cache_key": "view_tenant123_tenant_db_users_user_management_list",
      "cached_content": null,
      "cache_expires_at": null,
      "schema_version": 1704067200,
      "rendering_mode": "hybrid",
      "created_at": "2024-01-01T00:00:00Z",
      "updated_at": "2024-01-01T00:00:00Z",
      "column_configurations": [
        {
          "id": 1,
          "view_definition_id": 1,
          "column_name": "id",
          "display_name": "ID",
          "is_visible": true,
          "is_editable": false,
          "is_required": false,
          "is_searchable": true,
          "is_sortable": true,
          "display_order": 1,
          "data_type": "BIGINT",
          "is_password_field": false,
          "validation_rules": {},
          "placeholder_text": null,
          "help_text": null
        }
      ],
      "layout_configurations": [
        {
          "id": 1,
          "view_definition_id": 1,
          "layout_type": "table",
          "layout_config": {
            "columns_per_row": 3,
            "show_actions": true,
            "pagination": {
              "per_page": 25
            }
          },
          "responsive_config": null,
          "theme_config": null
        }
      ]
    }
  ]
}
```

### 1.2 Get Specific View Definition

**GET** `/api/v1/database/views/{viewId}`

Retrieves a specific view definition by ID.

**Response:**
```json
{
  "status": "success",
  "message": "View definition retrieved successfully",
  "data": {
    "id": 1,
    "tenant_id": "tenant-123",
    "schema_name": "tenant_db",
    "table_name": "users",
    "view_name": "user_management",
    "view_type": "list",
    "title": "User Management",
    "description": "Manage system users",
    "is_active": true,
    "cache_key": "view_tenant123_tenant_db_users_user_management_list",
    "cached_content": null,
    "cache_expires_at": null,
    "schema_version": 1704067200,
    "rendering_mode": "hybrid",
    "created_at": "2024-01-01T00:00:00Z",
    "updated_at": "2024-01-01T00:00:00Z",
    "column_configurations": [...],
    "layout_configurations": [...],
    "permissions": [...]
  }
}
```

### 1.3 Create View Definition

**POST** `/api/v1/database/views`

Creates a new view definition.

**Request Body:**
```json
{
  "table_name": "users",
  "view_name": "user_management",
  "view_type": "list",
  "title": "User Management",
  "description": "Manage system users",
  "rendering_mode": "hybrid",
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
      "data_type": "BIGINT",
      "validation_rules": {}
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

**Response:**
```json
{
  "status": "success",
  "message": "View definition created successfully",
  "data": {
    "id": 1,
    "tenant_id": "tenant-123",
    "schema_name": "tenant_db",
    "table_name": "users",
    "view_name": "user_management",
    "view_type": "list",
    "title": "User Management",
    "description": "Manage system users",
    "is_active": true,
    "rendering_mode": "hybrid",
    "created_at": "2024-01-01T00:00:00Z",
    "updated_at": "2024-01-01T00:00:00Z",
    "column_configurations": [...],
    "layout_configurations": [...]
  }
}
```

### 1.4 Update View Definition

**PATCH** `/api/v1/database/views/{viewId}`

Updates an existing view definition.

**Request Body:**
```json
{
  "title": "Updated User Management",
  "description": "Updated description",
  "rendering_mode": "cached",
  "is_active": true
}
```

**Response:**
```json
{
  "status": "success",
  "message": "View definition updated successfully",
  "data": {
    "id": 1,
    "title": "Updated User Management",
    "description": "Updated description",
    "rendering_mode": "cached",
    "is_active": true,
    "updated_at": "2024-01-01T00:00:00Z"
  }
}
```

### 1.5 Delete View Definition

**DELETE** `/api/v1/database/views/{viewId}`

Deletes a view definition.

**Response:**
```json
{
  "status": "success",
  "message": "View definition deleted successfully"
}
```

---

## 2. View Rendering

### 2.1 Render View (Hybrid)

**POST** `/api/v1/database/views/render`

Renders a view using the hybrid approach (dynamic, cached, or hybrid mode).

**Request Body:**
```json
{
  "table_name": "users",
  "view_type": "list",
  "rendering_mode": "hybrid",
  "force_refresh": false
}
```

**Response:**
```json
{
  "status": "success",
  "message": "View rendered successfully",
  "data": {
    "view_type": "list",
    "table_name": "users",
    "schema_name": "tenant_db",
    "rendered_at": "2024-01-01T00:00:00Z",
    "render_time": "5ms (hybrid)",
    "schema_version": 1704067200,
    "html_content": "<div class=\"dynamic-list-view\">...</div>",
    "css_content": ".dynamic-list-view { ... }",
    "js_content": "function initializeDynamicList() { ... }",
    "metadata": {
      "columns": [...],
      "searchable_columns": [...],
      "sortable_columns": [...],
      "pagination": {
        "per_page": 25,
        "show_page_info": true
      }
    },
    "cache_info": {
      "cache_key": "view_tenant123_tenant_db_users_list",
      "cached_at": "2024-01-01T00:00:00Z",
      "expires_at": "2024-01-02T00:00:00Z",
      "schema_version": 1704067200,
      "overlay_applied": true
    }
  }
}
```

### 2.2 Build and Cache View

**POST** `/api/v1/database/views/{viewId}/build`

Builds and caches a view definition.

**Request Body:**
```json
{
  "force_rebuild": false,
  "cache_duration": 3600
}
```

**Response:**
```json
{
  "status": "success",
  "message": "View built and cached successfully",
  "data": {
    "view_id": 1,
    "cache_key": "view_tenant123_tenant_db_users_user_management_list",
    "cache_expires_at": "2024-01-01T01:00:00Z",
    "build_time": "500ms",
    "cache_size": "2.5KB"
  }
}
```

### 2.3 Get Cached View

**GET** `/api/v1/database/views/{viewId}/cached`

Retrieves cached view content.

**Response:**
```json
{
  "status": "success",
  "message": "Cached view retrieved successfully",
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
      "cache_key": "view_tenant123_tenant_db_users_user_management_list",
      "cached_at": "2024-01-01T00:00:00Z",
      "expires_at": "2024-01-01T01:00:00Z",
      "hit_count": 15
    }
  }
}
```

---

## 3. Column Management

### 3.1 Update Column Configuration

**PATCH** `/api/v1/database/views/{viewId}/columns/{columnName}`

Updates a specific column configuration.

**Request Body:**
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

**Response:**
```json
{
  "status": "success",
  "message": "Column configuration updated successfully",
  "data": {
    "id": 1,
    "view_definition_id": 1,
    "column_name": "id",
    "display_name": "User ID",
    "is_visible": true,
    "is_editable": false,
    "is_required": false,
    "is_searchable": true,
    "is_sortable": true,
    "display_order": 1,
    "data_type": "BIGINT",
    "is_password_field": false,
    "validation_rules": {
      "required": false
    },
    "placeholder_text": "Enter user ID",
    "help_text": "Unique identifier for the user"
  }
}
```

### 3.2 Reorder Columns

**POST** `/api/v1/database/views/{viewId}/columns/reorder`

Reorders columns in a view.

**Request Body:**
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

**Response:**
```json
{
  "status": "success",
  "message": "Columns reordered successfully"
}
```

---

## 4. Schema Analysis

### 4.1 Get Schema Analysis

**GET** `/api/v1/database/views/tables/{tableName}/schema`

Retrieves schema analysis for a specific table.

**Response:**
```json
{
  "status": "success",
  "message": "Schema analysis retrieved successfully",
  "data": {
    "table_name": "users",
    "schema_name": "tenant_db",
    "columns": [
      {
        "name": "id",
        "type": "bigint",
        "full_type": "bigint(20)",
        "nullable": false,
        "default": null,
        "is_primary_key": true,
        "is_auto_increment": true,
        "comment": null,
        "is_password_field": false,
        "form_control": "number",
        "validation_rules": {
          "required": false
        },
        "display_name": "Id",
        "is_editable": false,
        "is_visible": true,
        "is_searchable": true,
        "is_sortable": true
      },
      {
        "name": "email",
        "type": "varchar",
        "full_type": "varchar(255)",
        "nullable": false,
        "default": null,
        "is_primary_key": false,
        "is_auto_increment": false,
        "comment": null,
        "is_password_field": false,
        "form_control": "text",
        "validation_rules": {
          "required": true,
          "max": 255
        },
        "display_name": "Email",
        "is_editable": true,
        "is_visible": true,
        "is_searchable": true,
        "is_sortable": true
      },
      {
        "name": "password",
        "type": "varchar",
        "full_type": "varchar(255)",
        "nullable": false,
        "default": null,
        "is_primary_key": false,
        "is_auto_increment": false,
        "comment": null,
        "is_password_field": true,
        "form_control": "password",
        "validation_rules": {
          "required": true,
          "min": 8,
          "pattern": "^(?=.*[a-z])(?=.*[A-Z])(?=.*\\d)"
        },
        "display_name": "Password",
        "is_editable": true,
        "is_visible": true,
        "is_searchable": false,
        "is_sortable": false
      }
    ],
    "indexes": [
      {
        "name": "PRIMARY",
        "columns": ["id"],
        "is_unique": true,
        "type": "BTREE"
      }
    ],
    "foreign_keys": [],
    "analyzed_at": "2024-01-01T00:00:00Z"
  }
}
```

### 4.2 Build All Views for Table

**POST** `/api/v1/database/views/tables/{tableName}/build-all`

Builds all view types for a specific table.

**Request Body:**
```json
{
  "view_types": ["create", "update", "list", "analytics"],
  "force_rebuild": false,
  "cache_duration": 3600
}
```

**Response:**
```json
{
  "status": "success",
  "message": "All views built successfully",
  "data": {
    "table_name": "users",
    "view_types": ["create", "update", "list", "analytics"],
    "results": [
      {
        "view_type": "create",
        "table_name": "users",
        "schema_name": "tenant_db",
        "rendered_at": "2024-01-01T00:00:00Z",
        "render_time": "50ms",
        "schema_version": 1704067200,
        "html_content": "<form class=\"dynamic-form create-form\">...</form>",
        "css_content": ".dynamic-form { ... }",
        "js_content": "function initializeDynamicForm() { ... }",
        "metadata": {...}
      }
    ]
  }
}
```

---

## View Metadata APIs

### Base URL
```
/api/v1/database/view-metadata
```

---

## 1. Data Types

### 1.1 Get All Data Types

**GET** `/api/v1/database/view-metadata/data-types`

Retrieves all supported data types with their properties.

**Response:**
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
      "validation_rules": ["max", "min", "pattern", "required"],
      "example": "Hello World"
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
      "mask_display": true,
      "example": "********"
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
      "validation_rules": ["min", "max", "required", "integer"],
      "example": 123
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
      "validation_rules": ["min", "max", "required", "decimal"],
      "example": 123.45
    },
    {
      "name": "DATE",
      "label": "Date",
      "description": "Date value",
      "category": "datetime",
      "supports_encryption": false,
      "form_controls": ["date", "datepicker"],
      "validation_rules": ["required", "date"],
      "example": "2024-01-01"
    },
    {
      "name": "DATETIME",
      "label": "Date and Time",
      "description": "Date and time value",
      "category": "datetime",
      "supports_encryption": false,
      "form_controls": ["datetime", "datetimepicker"],
      "validation_rules": ["required", "datetime"],
      "example": "2024-01-01 12:30:45"
    },
    {
      "name": "BOOLEAN",
      "label": "Boolean",
      "description": "True/false value",
      "category": "boolean",
      "supports_encryption": false,
      "form_controls": ["checkbox", "switch", "radio"],
      "validation_rules": ["required", "boolean"],
      "example": true
    },
    {
      "name": "JSON",
      "label": "JSON",
      "description": "JSON object or array",
      "category": "complex",
      "supports_encryption": false,
      "form_controls": ["textarea", "json-editor"],
      "validation_rules": ["required", "json"],
      "example": "{\"key\": \"value\"}"
    }
  ]
}
```

### 1.2 Get Data Types by Category

**GET** `/api/v1/database/view-metadata/data-types/categories`

Retrieves data types organized by category.

**Response:**
```json
{
  "status": "success",
  "message": "Data type categories retrieved successfully",
  "data": {
    "text": {
      "name": "Text",
      "description": "Text and string data types",
      "types": ["VARCHAR", "CHAR", "TEXT", "PASSWORD"]
    },
    "numeric": {
      "name": "Numeric",
      "description": "Numeric data types",
      "types": ["INT", "BIGINT", "DECIMAL", "FLOAT", "DOUBLE", "TINYINT", "SMALLINT", "MEDIUMINT"]
    },
    "datetime": {
      "name": "Date and Time",
      "description": "Date and time data types",
      "types": ["DATE", "DATETIME", "TIMESTAMP", "TIME", "YEAR"]
    },
    "boolean": {
      "name": "Boolean",
      "description": "Boolean data types",
      "types": ["BOOLEAN"]
    },
    "complex": {
      "name": "Complex",
      "description": "Complex data types",
      "types": ["JSON"]
    },
    "binary": {
      "name": "Binary",
      "description": "Binary data types",
      "types": ["BLOB", "LONGBLOB", "MEDIUMBLOB", "TINYBLOB"]
    },
    "enum": {
      "name": "Enumeration",
      "description": "Enumeration data types",
      "types": ["ENUM"]
    },
    "set": {
      "name": "Set",
      "description": "Set data types",
      "types": ["SET"]
    },
    "security": {
      "name": "Security",
      "description": "Security-related data types",
      "types": ["PASSWORD"]
    }
  }
}
```

---

## 2. Form Controls

### 2.1 Get Form Controls

**GET** `/api/v1/database/view-metadata/form-controls`

Retrieves all available form controls with their properties.

**Response:**
```json
{
  "status": "success",
  "message": "Form controls retrieved successfully",
  "data": {
    "text": {
      "name": "Text Input",
      "description": "Single-line text input",
      "suitable_for": ["VARCHAR", "CHAR"],
      "attributes": ["type", "maxlength", "placeholder"]
    },
    "textarea": {
      "name": "Text Area",
      "description": "Multi-line text input",
      "suitable_for": ["TEXT", "JSON"],
      "attributes": ["rows", "cols", "maxlength", "placeholder"]
    },
    "email": {
      "name": "Email Input",
      "description": "Email address input",
      "suitable_for": ["VARCHAR"],
      "attributes": ["type", "placeholder"]
    },
    "password": {
      "name": "Password Input",
      "description": "Password input with masking",
      "suitable_for": ["PASSWORD"],
      "attributes": ["type", "minlength", "placeholder"]
    },
    "number": {
      "name": "Number Input",
      "description": "Numeric input",
      "suitable_for": ["INT", "BIGINT", "DECIMAL", "FLOAT", "DOUBLE"],
      "attributes": ["type", "min", "max", "step"]
    },
    "date": {
      "name": "Date Input",
      "description": "Date picker",
      "suitable_for": ["DATE"],
      "attributes": ["type"]
    },
    "datetime": {
      "name": "Date Time Input",
      "description": "Date and time picker",
      "suitable_for": ["DATETIME", "TIMESTAMP"],
      "attributes": ["type"]
    },
    "checkbox": {
      "name": "Checkbox",
      "description": "Checkbox input",
      "suitable_for": ["BOOLEAN", "TINYINT"],
      "attributes": ["type", "value"]
    },
    "select": {
      "name": "Select Dropdown",
      "description": "Dropdown selection",
      "suitable_for": ["ENUM"],
      "attributes": ["multiple", "size"]
    },
    "file": {
      "name": "File Input",
      "description": "File upload input",
      "suitable_for": ["BLOB", "LONGBLOB", "MEDIUMBLOB", "TINYBLOB"],
      "attributes": ["type", "accept", "multiple"]
    }
  }
}
```

---

## 3. Validation Rules

### 3.1 Get Validation Rules

**GET** `/api/v1/database/view-metadata/validation-rules`

Retrieves all available validation rules with their properties.

**Response:**
```json
{
  "status": "success",
  "message": "Validation rules retrieved successfully",
  "data": {
    "required": {
      "name": "Required",
      "description": "Field is required",
      "applies_to": ["all"],
      "parameters": []
    },
    "min": {
      "name": "Minimum Value",
      "description": "Minimum value or length",
      "applies_to": ["VARCHAR", "CHAR", "TEXT", "INT", "BIGINT", "DECIMAL", "FLOAT", "DOUBLE", "PASSWORD"],
      "parameters": ["value"]
    },
    "max": {
      "name": "Maximum Value",
      "description": "Maximum value or length",
      "applies_to": ["VARCHAR", "CHAR", "TEXT", "INT", "BIGINT", "DECIMAL", "FLOAT", "DOUBLE"],
      "parameters": ["value"]
    },
    "pattern": {
      "name": "Pattern",
      "description": "Regular expression pattern",
      "applies_to": ["VARCHAR", "CHAR", "TEXT", "PASSWORD"],
      "parameters": ["regex"]
    },
    "email": {
      "name": "Email",
      "description": "Valid email address",
      "applies_to": ["VARCHAR"],
      "parameters": []
    },
    "integer": {
      "name": "Integer",
      "description": "Whole number",
      "applies_to": ["INT", "BIGINT", "TINYINT", "SMALLINT", "MEDIUMINT"],
      "parameters": []
    },
    "decimal": {
      "name": "Decimal",
      "description": "Decimal number",
      "applies_to": ["DECIMAL", "FLOAT", "DOUBLE"],
      "parameters": ["precision", "scale"]
    },
    "date": {
      "name": "Date",
      "description": "Valid date",
      "applies_to": ["DATE"],
      "parameters": ["format"]
    },
    "boolean": {
      "name": "Boolean",
      "description": "True or false value",
      "applies_to": ["BOOLEAN", "TINYINT"],
      "parameters": []
    },
    "json": {
      "name": "JSON",
      "description": "Valid JSON",
      "applies_to": ["JSON"],
      "parameters": []
    }
  }
}
```

---

## 4. View Types

### 4.1 Get View Types

**GET** `/api/v1/database/view-metadata/view-types`

Retrieves all available view types with their properties.

**Response:**
```json
{
  "status": "success",
  "message": "View types retrieved successfully",
  "data": {
    "create": {
      "name": "Create",
      "description": "Form for creating new records",
      "features": ["form_validation", "password_encryption", "field_grouping"],
      "layout": "form",
      "icon": "plus-circle"
    },
    "update": {
      "name": "Update",
      "description": "Form for editing existing records",
      "features": ["pre_populated_fields", "conditional_editing", "audit_trail"],
      "layout": "form",
      "icon": "edit"
    },
    "list": {
      "name": "List",
      "description": "Table/grid for displaying multiple records",
      "features": ["pagination", "sorting", "filtering", "search", "bulk_actions"],
      "layout": "table",
      "icon": "list"
    },
    "analytics": {
      "name": "Analytics",
      "description": "Dashboard with charts and metrics",
      "features": ["aggregations", "charts", "filters", "date_ranges"],
      "layout": "dashboard",
      "icon": "chart-bar"
    }
  }
}
```

---

## 5. Layout Types

### 5.1 Get Layout Types

**GET** `/api/v1/database/view-metadata/layout-types`

Retrieves all available layout types with their properties.

**Response:**
```json
{
  "status": "success",
  "message": "Layout types retrieved successfully",
  "data": {
    "form": {
      "name": "Form",
      "description": "Form layout for data input",
      "suitable_for": ["create", "update"],
      "features": ["field_grouping", "validation", "responsive"]
    },
    "table": {
      "name": "Table",
      "description": "Table layout for data display",
      "suitable_for": ["list"],
      "features": ["sorting", "filtering", "pagination", "responsive"]
    },
    "grid": {
      "name": "Grid",
      "description": "Grid layout for data display",
      "suitable_for": ["list"],
      "features": ["card_view", "responsive", "filtering"]
    },
    "card": {
      "name": "Card",
      "description": "Card layout for data display",
      "suitable_for": ["list"],
      "features": ["card_view", "responsive", "filtering"]
    },
    "dashboard": {
      "name": "Dashboard",
      "description": "Dashboard layout with widgets",
      "suitable_for": ["analytics"],
      "features": ["widgets", "charts", "responsive"]
    }
  }
}
```

---

## API Examples

### Example 1: Create a Complete View Definition

```bash
curl -X POST "https://api.example.com/api/v1/database/views" \
  -H "Content-Type: application/json" \
  -H "X-Tenant-ID: tenant-123" \
  -H "X-Schema-Name: tenant_db" \
  -d '{
    "table_name": "products",
    "view_name": "product_management",
    "view_type": "list",
    "title": "Product Management",
    "description": "Manage product catalog",
    "rendering_mode": "hybrid",
    "column_configurations": [
      {
        "column_name": "id",
        "display_name": "Product ID",
        "is_visible": true,
        "is_editable": false,
        "is_required": false,
        "is_searchable": true,
        "is_sortable": true,
        "display_order": 1,
        "data_type": "BIGINT"
      },
      {
        "column_name": "name",
        "display_name": "Product Name",
        "is_visible": true,
        "is_editable": true,
        "is_required": true,
        "is_searchable": true,
        "is_sortable": true,
        "display_order": 2,
        "data_type": "VARCHAR",
        "validation_rules": {
          "required": true,
          "max": 255
        },
        "placeholder_text": "Enter product name"
      },
      {
        "column_name": "price",
        "display_name": "Price",
        "is_visible": true,
        "is_editable": true,
        "is_required": true,
        "is_searchable": false,
        "is_sortable": true,
        "display_order": 3,
        "data_type": "DECIMAL",
        "validation_rules": {
          "required": true,
          "min": 0
        },
        "placeholder_text": "Enter price"
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
  }'
```

### Example 2: Render a View

```bash
curl -X POST "https://api.example.com/api/v1/database/views/render" \
  -H "Content-Type: application/json" \
  -H "X-Tenant-ID: tenant-123" \
  -H "X-Schema-Name: tenant_db" \
  -d '{
    "table_name": "products",
    "view_type": "list",
    "rendering_mode": "hybrid"
  }'
```

### Example 3: Get Schema Analysis

```bash
curl -X GET "https://api.example.com/api/v1/database/views/tables/products/schema" \
  -H "X-Tenant-ID: tenant-123" \
  -H "X-Schema-Name: tenant_db"
```

### Example 4: Get Data Types

```bash
curl -X GET "https://api.example.com/api/v1/database/view-metadata/data-types" \
  -H "X-Tenant-ID: tenant-123"
```

---

## Error Handling

### Common Error Responses

#### 400 Bad Request
```json
{
  "status": "error",
  "message": "Validation failed",
  "error": {
    "code": "VALIDATION_FAILED",
    "details": {
      "table_name": ["The table name field is required."],
      "view_type": ["The view type must be one of: create, update, list, analytics."]
    }
  }
}
```

#### 404 Not Found
```json
{
  "status": "error",
  "message": "View definition not found",
  "error": {
    "code": "VIEW_NOT_FOUND",
    "details": "The requested view definition does not exist."
  }
}
```

#### 409 Conflict
```json
{
  "status": "error",
  "message": "View definition already exists",
  "error": {
    "code": "VIEW_ALREADY_EXISTS",
    "details": "A view with this name and type already exists for this table."
  }
}
```

#### 500 Internal Server Error
```json
{
  "status": "error",
  "message": "Failed to create view definition",
  "error": {
    "code": "VIEW_CREATION_FAILED",
    "details": "An unexpected error occurred while creating the view definition."
  }
}
```

### Error Codes

| Code | Description |
|------|-------------|
| `VALIDATION_FAILED` | Request validation failed |
| `VIEW_NOT_FOUND` | View definition not found |
| `VIEW_ALREADY_EXISTS` | View definition already exists |
| `VIEW_CREATION_FAILED` | Failed to create view definition |
| `VIEW_UPDATE_FAILED` | Failed to update view definition |
| `VIEW_DELETION_FAILED` | Failed to delete view definition |
| `VIEW_RENDER_FAILED` | Failed to render view |
| `VIEW_BUILD_FAILED` | Failed to build view |
| `CACHE_INVALID` | View cache is invalid or expired |
| `CACHE_RETRIEVAL_FAILED` | Failed to retrieve cached view |
| `COLUMN_UPDATE_FAILED` | Failed to update column configuration |
| `COLUMN_REORDER_FAILED` | Failed to reorder columns |
| `SCHEMA_ANALYSIS_FAILED` | Failed to get schema analysis |
| `BULK_BUILD_FAILED` | Failed to build all views |
| `DATA_TYPES_RETRIEVAL_FAILED` | Failed to retrieve data types |
| `FORM_CONTROLS_RETRIEVAL_FAILED` | Failed to retrieve form controls |
| `VALIDATION_RULES_RETRIEVAL_FAILED` | Failed to retrieve validation rules |
| `VIEW_TYPES_RETRIEVAL_FAILED` | Failed to retrieve view types |
| `LAYOUT_TYPES_RETRIEVAL_FAILED` | Failed to retrieve layout types |

---

## Summary

The View Management APIs provide comprehensive functionality for:

1. **View Definition Management**: Create, read, update, and delete view definitions
2. **View Rendering**: Hybrid rendering with dynamic, cached, and hybrid modes
3. **Column Management**: Configure column properties and reorder columns
4. **Schema Analysis**: Analyze table schemas and generate dynamic views
5. **Metadata APIs**: Get data types, form controls, validation rules, and layout types

The APIs support password encryption, caching, and hybrid rendering for optimal performance and flexibility.
