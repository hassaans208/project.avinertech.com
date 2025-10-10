# Enhanced View System Documentation

## Overview

The Enhanced View System provides a flexible and configurable way to manage different types of views for database tables. It supports four main view types: `list`, `create/update`, `analytics`, and `soft-delete`, each with their own set of configurable options.

## Architecture

### Core Components

1. **ViewType Model** - Defines the available view types
2. **ViewTypeOption Model** - Defines configurable options for each view type
3. **ViewDefinition Model** - Enhanced to work with the new system
4. **ViewColumnConfiguration Model** - Enhanced with column-specific options
5. **EnhancedViewService** - Business logic for view management
6. **EnhancedViewController** - API endpoints for view management

### Database Schema

#### view_types Table
- `id` - Primary key
- `name` - Unique identifier (list, create/update, analytics, soft-delete)
- `display_name` - Human-readable name
- `description` - Description of the view type
- `icon` - Icon identifier for UI
- `color` - Color theme for UI
- `default_config` - Default configuration JSON
- `is_active` - Whether the view type is active
- `sort_order` - Display order

#### view_type_options Table
- `id` - Primary key
- `view_type_id` - Foreign key to view_types
- `option_key` - Unique option identifier within view type
- `display_name` - Human-readable option name
- `description` - Option description
- `option_type` - Data type (boolean, string, number, array, object)
- `default_value` - Default value for the option
- `validation_rules` - Validation rules JSON
- `possible_values` - Available values for select options
- `is_required` - Whether the option is required
- `is_active` - Whether the option is active
- `sort_order` - Display order

## View Types and Options

### 1. List View (`list`)

**Purpose**: Display data in a tabular format with sorting, filtering, and pagination.

**Options**:
- `show_encrypted` (boolean) - Display encrypted fields in their encrypted form
- `substr` (object) - Show only a portion of long text fields
  - `enabled` (boolean) - Enable substring display
  - `length` (number) - Maximum length to display
- `hide` (boolean) - Hide this column from the list view
- `sortable` (boolean) - Allow sorting by this column
- `searchable` (boolean) - Include this column in search functionality
- `filterable` (boolean) - Allow filtering by this column
- `exportable` (boolean) - Include this column in export functionality
- `column_width` (string) - Set the width of this column

### 2. Create/Update Form (`create/update`)

**Purpose**: Form-based interface for creating and editing records with validation.

**Options**:
- `password` (boolean) - Treat this field as a password input
- `is_editable` (boolean) - Allow editing of this field
- `hide` (boolean) - Hide this field from the form
- `is_required` (boolean) - Make this field required
- `validation_rules` (array) - Custom validation rules for this field
- `placeholder_text` (string) - Placeholder text for the input field
- `help_text` (string) - Additional help text for the field
- `input_type` (string) - Type of input field to use

### 3. Analytics Dashboard (`analytics`)

**Purpose**: Interactive dashboard with charts, graphs, and data analysis tools.

**Options**:
- `chart_type` (string) - Type of chart to display for this data
- `aggregation_type` (string) - How to aggregate the data
- `group_by` (string) - Field to group the data by
- `time_range` (string) - Default time range for the analytics
- `show_trends` (boolean) - Display trend indicators
- `compare_period` (boolean) - Enable period comparison
- `drill_down` (boolean) - Allow drilling down into detailed data

### 4. Soft Delete Management (`soft-delete`)

**Purpose**: Manage soft-deleted records with restore and permanent delete options.

**Options**:
- `show_deleted_at` (boolean) - Display the deletion timestamp
- `allow_restore` (boolean) - Allow restoring deleted records
- `allow_permanent_delete` (boolean) - Allow permanently deleting records
- `bulk_operations` (boolean) - Enable bulk restore/delete operations

## API Endpoints

### Enhanced View Management

#### Get View Types
```
GET /api/v1/database/enhanced-views/view-types
```
Returns all available view types with their options.

#### Get Table Schema
```
GET /api/v1/database/enhanced-views/table-schema?schema_name={schema}&table_name={table}
```
Returns the schema information for a specific table.

#### Generate Default Configurations
```
POST /api/v1/database/enhanced-views/generate-configurations
{
    "schema_name": "example_schema",
    "table_name": "users",
    "view_type": "list"
}
```
Generates default column configurations for a table and view type.

#### Validate Configuration
```
POST /api/v1/database/enhanced-views/validate-configuration
{
    "view_type": "list",
    "view_configuration": {...},
    "columns": [...]
}
```
Validates a complete view configuration.

#### Create View Definition
```
POST /api/v1/database/enhanced-views/
{
    "tenant_id": "tenant_123",
    "schema_name": "example_schema",
    "table_name": "users",
    "view_name": "user_list_view",
    "view_type": "list",
    "title": "User List View",
    "description": "A list view for users",
    "view_configuration": {
        "pagination": true,
        "page_size": 25
    },
    "columns": [
        {
            "column_name": "id",
            "display_name": "ID",
            "column_options": {
                "hide": false,
                "sortable": true,
                "searchable": true
            }
        }
    ]
}
```

#### Update View Definition
```
PATCH /api/v1/database/enhanced-views/{id}
{
    "view_configuration": {...},
    "columns": [...]
}
```

#### Get View Definition
```
GET /api/v1/database/enhanced-views/{id}
```
Returns a view definition with full configuration.

#### Get View Definitions
```
GET /api/v1/database/enhanced-views/?tenant_id={tenant}&schema_name={schema}&table_name={table}&view_type={type}
```
Returns view definitions with optional filtering.

## Usage Examples

### Creating a List View

```php
use App\Services\EnhancedViewService;

$service = new EnhancedViewService();

// Create a list view for users table
$viewData = [
    'tenant_id' => 'tenant_123',
    'schema_name' => 'public',
    'table_name' => 'users',
    'view_name' => 'user_list',
    'view_type' => 'list',
    'title' => 'User List',
    'description' => 'List view for users',
    'view_configuration' => [
        'pagination' => true,
        'page_size' => 25,
        'show_search' => true
    ],
    'columns' => [
        [
            'column_name' => 'id',
            'display_name' => 'ID',
            'column_options' => [
                'sortable' => true,
                'searchable' => true,
                'column_width' => '100px'
            ]
        ],
        [
            'column_name' => 'email',
            'display_name' => 'Email',
            'column_options' => [
                'sortable' => true,
                'searchable' => true,
                'filterable' => true
            ]
        ],
        [
            'column_name' => 'password',
            'display_name' => 'Password',
            'column_options' => [
                'hide' => true,
                'show_encrypted' => false
            ]
        ]
    ]
];

$viewDefinition = $service->createViewDefinition($viewData);
```

### Creating an Analytics View

```php
$analyticsData = [
    'tenant_id' => 'tenant_123',
    'schema_name' => 'public',
    'table_name' => 'orders',
    'view_name' => 'order_analytics',
    'view_type' => 'analytics',
    'title' => 'Order Analytics',
    'description' => 'Analytics dashboard for orders',
    'view_configuration' => [
        'auto_refresh' => true,
        'refresh_interval' => 300
    ],
    'columns' => [
        [
            'column_name' => 'created_at',
            'display_name' => 'Order Date',
            'column_options' => [
                'chart_type' => 'line',
                'aggregation_type' => 'count',
                'group_by' => 'created_at',
                'time_range' => '30d',
                'show_trends' => true
            ]
        ],
        [
            'column_name' => 'total_amount',
            'display_name' => 'Total Amount',
            'column_options' => [
                'chart_type' => 'bar',
                'aggregation_type' => 'sum',
                'group_by' => 'created_at',
                'drill_down' => true
            ]
        ]
    ]
];

$analyticsView = $service->createViewDefinition($analyticsData);
```

## Data Type Integration

The system automatically detects column data types from the database schema and applies appropriate default options:

- **Password fields**: Automatically detected by column name containing "password"
- **Encrypted fields**: Automatically detected by column name containing "encrypted" or "hash"
- **Primary keys**: Automatically set as non-editable
- **Required fields**: Automatically detected based on nullable constraints

## Validation

The system includes comprehensive validation:

1. **View Type Validation**: Ensures the specified view type exists and is active
2. **Configuration Validation**: Validates view configuration against available options
3. **Column Option Validation**: Validates column options against view type options
4. **Data Type Validation**: Ensures option values match their expected data types
5. **Required Field Validation**: Ensures required options are provided

## Future Enhancements

1. **Custom View Types**: Allow users to create custom view types
2. **Conditional Options**: Options that appear based on other option values
3. **Template System**: Pre-built view templates for common use cases
4. **Import/Export**: Export and import view configurations
5. **Version Control**: Track changes to view configurations
6. **A/B Testing**: Test different view configurations
7. **Performance Metrics**: Track view performance and usage statistics

## Migration Guide

To migrate existing view definitions to the new system:

1. Run the migrations to add new tables and columns
2. Run the seeders to populate view types and options
3. Update existing view definitions to use the new configuration format
4. Test view rendering with the new system
5. Gradually migrate views to use the enhanced options

## Troubleshooting

### Common Issues

1. **View Type Not Found**: Ensure the view type exists in the database and is active
2. **Invalid Configuration**: Check that all required options are provided and valid
3. **Column Options Not Applied**: Verify that column options are properly formatted
4. **Validation Errors**: Check the validation rules for each option

### Debugging

Use the validation endpoints to debug configuration issues:

```php
$errors = $service->validateViewConfiguration($viewData);
if (!empty($errors)) {
    foreach ($errors as $error) {
        echo "Error: " . $error . "\n";
    }
}
```
