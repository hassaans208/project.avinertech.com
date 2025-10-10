<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Log;

class ViewMetadataController extends Controller
{
    /**
     * Get all supported data types
     */
    public function getDataTypes(Request $request): JsonResponse
    {
        try {
            $dataTypes = [
                [
                    'name' => 'VARCHAR',
                    'label' => 'Text',
                    'description' => 'Variable-length character string',
                    'category' => 'text',
                    'max_length' => 65535,
                    'supports_encryption' => false,
                    'form_controls' => ['text', 'textarea', 'email', 'url'],
                    'validation_rules' => ['max', 'min', 'pattern', 'required'],
                    'example' => 'Hello World'
                ],
                [
                    'name' => 'CHAR',
                    'label' => 'Fixed Text',
                    'description' => 'Fixed-length character string',
                    'category' => 'text',
                    'max_length' => 255,
                    'supports_encryption' => false,
                    'form_controls' => ['text'],
                    'validation_rules' => ['max', 'min', 'required'],
                    'example' => 'ABC'
                ],
                [
                    'name' => 'TEXT',
                    'label' => 'Long Text',
                    'description' => 'Variable-length text string',
                    'category' => 'text',
                    'max_length' => 65535,
                    'supports_encryption' => false,
                    'form_controls' => ['textarea'],
                    'validation_rules' => ['max', 'required'],
                    'example' => 'This is a long text field'
                ],
                [
                    'name' => 'PASSWORD',
                    'label' => 'Password',
                    'description' => 'Encrypted password field',
                    'category' => 'security',
                    'max_length' => 255,
                    'supports_encryption' => true,
                    'form_controls' => ['password'],
                    'validation_rules' => ['required', 'min', 'pattern'],
                    'encryption_method' => 'bcrypt',
                    'mask_display' => true,
                    'example' => '********'
                ],
                [
                    'name' => 'INT',
                    'label' => 'Integer',
                    'description' => 'Whole number',
                    'category' => 'numeric',
                    'min_value' => -2147483648,
                    'max_value' => 2147483647,
                    'supports_encryption' => false,
                    'form_controls' => ['number', 'range'],
                    'validation_rules' => ['min', 'max', 'required', 'integer'],
                    'example' => 123
                ],
                [
                    'name' => 'BIGINT',
                    'label' => 'Big Integer',
                    'description' => 'Large whole number',
                    'category' => 'numeric',
                    'min_value' => -9223372036854775808,
                    'max_value' => 9223372036854775807,
                    'supports_encryption' => false,
                    'form_controls' => ['number'],
                    'validation_rules' => ['min', 'max', 'required', 'integer'],
                    'example' => 9223372036854775807
                ],
                [
                    'name' => 'DECIMAL',
                    'label' => 'Decimal',
                    'description' => 'Decimal number with precision',
                    'category' => 'numeric',
                    'precision' => 10,
                    'scale' => 2,
                    'supports_encryption' => false,
                    'form_controls' => ['number'],
                    'validation_rules' => ['min', 'max', 'required', 'decimal'],
                    'example' => 123.45
                ],
                [
                    'name' => 'FLOAT',
                    'label' => 'Float',
                    'description' => 'Floating-point number',
                    'category' => 'numeric',
                    'supports_encryption' => false,
                    'form_controls' => ['number'],
                    'validation_rules' => ['min', 'max', 'required', 'numeric'],
                    'example' => 123.456
                ],
                [
                    'name' => 'DOUBLE',
                    'label' => 'Double',
                    'description' => 'Double-precision floating-point number',
                    'category' => 'numeric',
                    'supports_encryption' => false,
                    'form_controls' => ['number'],
                    'validation_rules' => ['min', 'max', 'required', 'numeric'],
                    'example' => 123.456789
                ],
                [
                    'name' => 'DATE',
                    'label' => 'Date',
                    'description' => 'Date value',
                    'category' => 'datetime',
                    'supports_encryption' => false,
                    'form_controls' => ['date', 'datepicker'],
                    'validation_rules' => ['required', 'date'],
                    'example' => '2024-01-01'
                ],
                [
                    'name' => 'DATETIME',
                    'label' => 'Date and Time',
                    'description' => 'Date and time value',
                    'category' => 'datetime',
                    'supports_encryption' => false,
                    'form_controls' => ['datetime', 'datetimepicker'],
                    'validation_rules' => ['required', 'datetime'],
                    'example' => '2024-01-01 12:30:45'
                ],
                [
                    'name' => 'TIMESTAMP',
                    'label' => 'Timestamp',
                    'description' => 'Timestamp value',
                    'category' => 'datetime',
                    'supports_encryption' => false,
                    'form_controls' => ['datetime', 'datetimepicker'],
                    'validation_rules' => ['required', 'datetime'],
                    'example' => '2024-01-01 12:30:45'
                ],
                [
                    'name' => 'TIME',
                    'label' => 'Time',
                    'description' => 'Time value',
                    'category' => 'datetime',
                    'supports_encryption' => false,
                    'form_controls' => ['time', 'timepicker'],
                    'validation_rules' => ['required', 'time'],
                    'example' => '12:30:45'
                ],
                [
                    'name' => 'YEAR',
                    'label' => 'Year',
                    'description' => 'Year value',
                    'category' => 'datetime',
                    'supports_encryption' => false,
                    'form_controls' => ['number', 'select'],
                    'validation_rules' => ['required', 'integer', 'min', 'max'],
                    'example' => 2024
                ],
                [
                    'name' => 'BOOLEAN',
                    'label' => 'Boolean',
                    'description' => 'True/false value',
                    'category' => 'boolean',
                    'supports_encryption' => false,
                    'form_controls' => ['checkbox', 'switch', 'radio'],
                    'validation_rules' => ['required', 'boolean'],
                    'example' => true
                ],
                [
                    'name' => 'TINYINT',
                    'label' => 'Tiny Integer',
                    'description' => 'Very small integer',
                    'category' => 'numeric',
                    'min_value' => -128,
                    'max_value' => 127,
                    'supports_encryption' => false,
                    'form_controls' => ['number', 'checkbox'],
                    'validation_rules' => ['min', 'max', 'required', 'integer'],
                    'example' => 1
                ],
                [
                    'name' => 'SMALLINT',
                    'label' => 'Small Integer',
                    'description' => 'Small integer',
                    'category' => 'numeric',
                    'min_value' => -32768,
                    'max_value' => 32767,
                    'supports_encryption' => false,
                    'form_controls' => ['number'],
                    'validation_rules' => ['min', 'max', 'required', 'integer'],
                    'example' => 1234
                ],
                [
                    'name' => 'MEDIUMINT',
                    'label' => 'Medium Integer',
                    'description' => 'Medium integer',
                    'category' => 'numeric',
                    'min_value' => -8388608,
                    'max_value' => 8388607,
                    'supports_encryption' => false,
                    'form_controls' => ['number'],
                    'validation_rules' => ['min', 'max', 'required', 'integer'],
                    'example' => 123456
                ],
                [
                    'name' => 'JSON',
                    'label' => 'JSON',
                    'description' => 'JSON object or array',
                    'category' => 'complex',
                    'supports_encryption' => false,
                    'form_controls' => ['textarea', 'json-editor'],
                    'validation_rules' => ['required', 'json'],
                    'example' => '{"key": "value"}'
                ],
                [
                    'name' => 'BLOB',
                    'label' => 'Binary Data',
                    'description' => 'Binary large object',
                    'category' => 'binary',
                    'supports_encryption' => false,
                    'form_controls' => ['file'],
                    'validation_rules' => ['required', 'file'],
                    'example' => '[Binary Data]'
                ],
                [
                    'name' => 'LONGBLOB',
                    'label' => 'Long Binary Data',
                    'description' => 'Long binary large object',
                    'category' => 'binary',
                    'supports_encryption' => false,
                    'form_controls' => ['file'],
                    'validation_rules' => ['required', 'file'],
                    'example' => '[Long Binary Data]'
                ],
                [
                    'name' => 'MEDIUMBLOB',
                    'label' => 'Medium Binary Data',
                    'description' => 'Medium binary large object',
                    'category' => 'binary',
                    'supports_encryption' => false,
                    'form_controls' => ['file'],
                    'validation_rules' => ['required', 'file'],
                    'example' => '[Medium Binary Data]'
                ],
                [
                    'name' => 'TINYBLOB',
                    'label' => 'Tiny Binary Data',
                    'description' => 'Tiny binary large object',
                    'category' => 'binary',
                    'supports_encryption' => false,
                    'form_controls' => ['file'],
                    'validation_rules' => ['required', 'file'],
                    'example' => '[Tiny Binary Data]'
                ],
                [
                    'name' => 'ENUM',
                    'label' => 'Enumeration',
                    'description' => 'Enumeration value',
                    'category' => 'enum',
                    'supports_encryption' => false,
                    'form_controls' => ['select', 'radio'],
                    'validation_rules' => ['required', 'in'],
                    'example' => 'option1'
                ],
                [
                    'name' => 'SET',
                    'label' => 'Set',
                    'description' => 'Set of values',
                    'category' => 'set',
                    'supports_encryption' => false,
                    'form_controls' => ['checkbox', 'multiselect'],
                    'validation_rules' => ['required', 'array'],
                    'example' => ['option1', 'option2']
                ]
            ];

            return response()->json([
                'status' => 'success',
                'message' => 'Data types retrieved successfully',
                'data' => $dataTypes
            ]);

        } catch (\Exception $e) {
            Log::error('Failed to retrieve data types', [
                'error' => $e->getMessage()
            ]);

            return response()->json([
                'status' => 'error',
                'message' => 'Failed to retrieve data types',
                'error' => [
                    'code' => 'DATA_TYPES_RETRIEVAL_FAILED',
                    'details' => $e->getMessage()
                ]
            ], 500);
        }
    }

    /**
     * Get data types by category
     */
    public function getDataTypesByCategory(Request $request): JsonResponse
    {
        try {
            $categories = [
                'text' => [
                    'name' => 'Text',
                    'description' => 'Text and string data types',
                    'types' => ['VARCHAR', 'CHAR', 'TEXT', 'PASSWORD']
                ],
                'numeric' => [
                    'name' => 'Numeric',
                    'description' => 'Numeric data types',
                    'types' => ['INT', 'BIGINT', 'DECIMAL', 'FLOAT', 'DOUBLE', 'TINYINT', 'SMALLINT', 'MEDIUMINT']
                ],
                'datetime' => [
                    'name' => 'Date and Time',
                    'description' => 'Date and time data types',
                    'types' => ['DATE', 'DATETIME', 'TIMESTAMP', 'TIME', 'YEAR']
                ],
                'boolean' => [
                    'name' => 'Boolean',
                    'description' => 'Boolean data types',
                    'types' => ['BOOLEAN']
                ],
                'complex' => [
                    'name' => 'Complex',
                    'description' => 'Complex data types',
                    'types' => ['JSON']
                ],
                'binary' => [
                    'name' => 'Binary',
                    'description' => 'Binary data types',
                    'types' => ['BLOB', 'LONGBLOB', 'MEDIUMBLOB', 'TINYBLOB']
                ],
                'enum' => [
                    'name' => 'Enumeration',
                    'description' => 'Enumeration data types',
                    'types' => ['ENUM']
                ],
                'set' => [
                    'name' => 'Set',
                    'description' => 'Set data types',
                    'types' => ['SET']
                ],
                'security' => [
                    'name' => 'Security',
                    'description' => 'Security-related data types',
                    'types' => ['PASSWORD']
                ]
            ];

            return response()->json([
                'status' => 'success',
                'message' => 'Data type categories retrieved successfully',
                'data' => $categories
            ]);

        } catch (\Exception $e) {
            Log::error('Failed to retrieve data type categories', [
                'error' => $e->getMessage()
            ]);

            return response()->json([
                'status' => 'error',
                'message' => 'Failed to retrieve data type categories',
                'error' => [
                    'code' => 'DATA_TYPE_CATEGORIES_RETRIEVAL_FAILED',
                    'details' => $e->getMessage()
                ]
            ], 500);
        }
    }

    /**
     * Get form controls for data types
     */
    public function getFormControls(Request $request): JsonResponse
    {
        try {
            $formControls = [
                'text' => [
                    'name' => 'Text Input',
                    'description' => 'Single-line text input',
                    'suitable_for' => ['VARCHAR', 'CHAR'],
                    'attributes' => ['type' => 'text', 'maxlength', 'placeholder']
                ],
                'textarea' => [
                    'name' => 'Text Area',
                    'description' => 'Multi-line text input',
                    'suitable_for' => ['TEXT', 'JSON'],
                    'attributes' => ['rows', 'cols', 'maxlength', 'placeholder']
                ],
                'email' => [
                    'name' => 'Email Input',
                    'description' => 'Email address input',
                    'suitable_for' => ['VARCHAR'],
                    'attributes' => ['type' => 'email', 'placeholder']
                ],
                'url' => [
                    'name' => 'URL Input',
                    'description' => 'URL input',
                    'suitable_for' => ['VARCHAR'],
                    'attributes' => ['type' => 'url', 'placeholder']
                ],
                'password' => [
                    'name' => 'Password Input',
                    'description' => 'Password input with masking',
                    'suitable_for' => ['PASSWORD'],
                    'attributes' => ['type' => 'password', 'minlength', 'placeholder']
                ],
                'number' => [
                    'name' => 'Number Input',
                    'description' => 'Numeric input',
                    'suitable_for' => ['INT', 'BIGINT', 'DECIMAL', 'FLOAT', 'DOUBLE', 'TINYINT', 'SMALLINT', 'MEDIUMINT', 'YEAR'],
                    'attributes' => ['type' => 'number', 'min', 'max', 'step']
                ],
                'range' => [
                    'name' => 'Range Slider',
                    'description' => 'Range slider for numeric values',
                    'suitable_for' => ['INT', 'TINYINT', 'SMALLINT', 'MEDIUMINT'],
                    'attributes' => ['type' => 'range', 'min', 'max', 'step']
                ],
                'date' => [
                    'name' => 'Date Input',
                    'description' => 'Date picker',
                    'suitable_for' => ['DATE'],
                    'attributes' => ['type' => 'date']
                ],
                'datetime' => [
                    'name' => 'Date Time Input',
                    'description' => 'Date and time picker',
                    'suitable_for' => ['DATETIME', 'TIMESTAMP'],
                    'attributes' => ['type' => 'datetime-local']
                ],
                'time' => [
                    'name' => 'Time Input',
                    'description' => 'Time picker',
                    'suitable_for' => ['TIME'],
                    'attributes' => ['type' => 'time']
                ],
                'checkbox' => [
                    'name' => 'Checkbox',
                    'description' => 'Checkbox input',
                    'suitable_for' => ['BOOLEAN', 'TINYINT'],
                    'attributes' => ['type' => 'checkbox', 'value']
                ],
                'switch' => [
                    'name' => 'Switch',
                    'description' => 'Toggle switch',
                    'suitable_for' => ['BOOLEAN', 'TINYINT'],
                    'attributes' => ['type' => 'checkbox', 'class' => 'switch']
                ],
                'radio' => [
                    'name' => 'Radio Button',
                    'description' => 'Radio button group',
                    'suitable_for' => ['BOOLEAN', 'ENUM'],
                    'attributes' => ['type' => 'radio', 'name', 'value']
                ],
                'select' => [
                    'name' => 'Select Dropdown',
                    'description' => 'Dropdown selection',
                    'suitable_for' => ['ENUM', 'YEAR'],
                    'attributes' => ['multiple', 'size']
                ],
                'multiselect' => [
                    'name' => 'Multi Select',
                    'description' => 'Multiple selection dropdown',
                    'suitable_for' => ['SET'],
                    'attributes' => ['multiple', 'size']
                ],
                'file' => [
                    'name' => 'File Input',
                    'description' => 'File upload input',
                    'suitable_for' => ['BLOB', 'LONGBLOB', 'MEDIUMBLOB', 'TINYBLOB'],
                    'attributes' => ['type' => 'file', 'accept', 'multiple']
                ],
                'json-editor' => [
                    'name' => 'JSON Editor',
                    'description' => 'JSON editor with syntax highlighting',
                    'suitable_for' => ['JSON'],
                    'attributes' => ['mode' => 'json', 'theme']
                ],
                'datepicker' => [
                    'name' => 'Date Picker',
                    'description' => 'Advanced date picker',
                    'suitable_for' => ['DATE'],
                    'attributes' => ['format', 'locale', 'minDate', 'maxDate']
                ],
                'datetimepicker' => [
                    'name' => 'Date Time Picker',
                    'description' => 'Advanced date and time picker',
                    'suitable_for' => ['DATETIME', 'TIMESTAMP'],
                    'attributes' => ['format', 'locale', 'minDate', 'maxDate']
                ],
                'timepicker' => [
                    'name' => 'Time Picker',
                    'description' => 'Advanced time picker',
                    'suitable_for' => ['TIME'],
                    'attributes' => ['format', 'locale', 'minTime', 'maxTime']
                ]
            ];

            return response()->json([
                'status' => 'success',
                'message' => 'Form controls retrieved successfully',
                'data' => $formControls
            ]);

        } catch (\Exception $e) {
            Log::error('Failed to retrieve form controls', [
                'error' => $e->getMessage()
            ]);

            return response()->json([
                'status' => 'error',
                'message' => 'Failed to retrieve form controls',
                'error' => [
                    'code' => 'FORM_CONTROLS_RETRIEVAL_FAILED',
                    'details' => $e->getMessage()
                ]
            ], 500);
        }
    }

    /**
     * Get validation rules for data types
     */
    public function getValidationRules(Request $request): JsonResponse
    {
        try {
            $validationRules = [
                'required' => [
                    'name' => 'Required',
                    'description' => 'Field is required',
                    'applies_to' => ['all'],
                    'parameters' => []
                ],
                'min' => [
                    'name' => 'Minimum Value',
                    'description' => 'Minimum value or length',
                    'applies_to' => ['VARCHAR', 'CHAR', 'TEXT', 'INT', 'BIGINT', 'DECIMAL', 'FLOAT', 'DOUBLE', 'PASSWORD'],
                    'parameters' => ['value']
                ],
                'max' => [
                    'name' => 'Maximum Value',
                    'description' => 'Maximum value or length',
                    'applies_to' => ['VARCHAR', 'CHAR', 'TEXT', 'INT', 'BIGINT', 'DECIMAL', 'FLOAT', 'DOUBLE'],
                    'parameters' => ['value']
                ],
                'pattern' => [
                    'name' => 'Pattern',
                    'description' => 'Regular expression pattern',
                    'applies_to' => ['VARCHAR', 'CHAR', 'TEXT', 'PASSWORD'],
                    'parameters' => ['regex']
                ],
                'email' => [
                    'name' => 'Email',
                    'description' => 'Valid email address',
                    'applies_to' => ['VARCHAR'],
                    'parameters' => []
                ],
                'url' => [
                    'name' => 'URL',
                    'description' => 'Valid URL',
                    'applies_to' => ['VARCHAR'],
                    'parameters' => []
                ],
                'integer' => [
                    'name' => 'Integer',
                    'description' => 'Whole number',
                    'applies_to' => ['INT', 'BIGINT', 'TINYINT', 'SMALLINT', 'MEDIUMINT', 'YEAR'],
                    'parameters' => []
                ],
                'decimal' => [
                    'name' => 'Decimal',
                    'description' => 'Decimal number',
                    'applies_to' => ['DECIMAL', 'FLOAT', 'DOUBLE'],
                    'parameters' => ['precision', 'scale']
                ],
                'numeric' => [
                    'name' => 'Numeric',
                    'description' => 'Numeric value',
                    'applies_to' => ['INT', 'BIGINT', 'DECIMAL', 'FLOAT', 'DOUBLE', 'TINYINT', 'SMALLINT', 'MEDIUMINT'],
                    'parameters' => []
                ],
                'date' => [
                    'name' => 'Date',
                    'description' => 'Valid date',
                    'applies_to' => ['DATE'],
                    'parameters' => ['format']
                ],
                'datetime' => [
                    'name' => 'Date Time',
                    'description' => 'Valid date and time',
                    'applies_to' => ['DATETIME', 'TIMESTAMP'],
                    'parameters' => ['format']
                ],
                'time' => [
                    'name' => 'Time',
                    'description' => 'Valid time',
                    'applies_to' => ['TIME'],
                    'parameters' => ['format']
                ],
                'boolean' => [
                    'name' => 'Boolean',
                    'description' => 'True or false value',
                    'applies_to' => ['BOOLEAN', 'TINYINT'],
                    'parameters' => []
                ],
                'json' => [
                    'name' => 'JSON',
                    'description' => 'Valid JSON',
                    'applies_to' => ['JSON'],
                    'parameters' => []
                ],
                'file' => [
                    'name' => 'File',
                    'description' => 'Valid file',
                    'applies_to' => ['BLOB', 'LONGBLOB', 'MEDIUMBLOB', 'TINYBLOB'],
                    'parameters' => ['types', 'size']
                ],
                'in' => [
                    'name' => 'In List',
                    'description' => 'Value must be in specified list',
                    'applies_to' => ['ENUM'],
                    'parameters' => ['values']
                ],
                'array' => [
                    'name' => 'Array',
                    'description' => 'Array of values',
                    'applies_to' => ['SET'],
                    'parameters' => []
                ]
            ];

            return response()->json([
                'status' => 'success',
                'message' => 'Validation rules retrieved successfully',
                'data' => $validationRules
            ]);

        } catch (\Exception $e) {
            Log::error('Failed to retrieve validation rules', [
                'error' => $e->getMessage()
            ]);

            return response()->json([
                'status' => 'error',
                'message' => 'Failed to retrieve validation rules',
                'error' => [
                    'code' => 'VALIDATION_RULES_RETRIEVAL_FAILED',
                    'details' => $e->getMessage()
                ]
            ], 500);
        }
    }

    /**
     * Get view types
     */
    public function getViewTypes(Request $request): JsonResponse
    {
        try {
            $viewTypes = [
                'create' => [
                    'name' => 'Create',
                    'description' => 'Form for creating new records',
                    'features' => ['form_validation', 'password_encryption', 'field_grouping'],
                    'layout' => 'form',
                    'icon' => 'plus-circle'
                ],
                'update' => [
                    'name' => 'Update',
                    'description' => 'Form for editing existing records',
                    'features' => ['pre_populated_fields', 'conditional_editing', 'audit_trail'],
                    'layout' => 'form',
                    'icon' => 'edit'
                ],
                'list' => [
                    'name' => 'List',
                    'description' => 'Table/grid for displaying multiple records',
                    'features' => ['pagination', 'sorting', 'filtering', 'search', 'bulk_actions'],
                    'layout' => 'table',
                    'icon' => 'list'
                ],
                'analytics' => [
                    'name' => 'Analytics',
                    'description' => 'Dashboard with charts and metrics',
                    'features' => ['aggregations', 'charts', 'filters', 'date_ranges'],
                    'layout' => 'dashboard',
                    'icon' => 'chart-bar'
                ]
            ];

            return response()->json([
                'status' => 'success',
                'message' => 'View types retrieved successfully',
                'data' => $viewTypes
            ]);

        } catch (\Exception $e) {
            Log::error('Failed to retrieve view types', [
                'error' => $e->getMessage()
            ]);

            return response()->json([
                'status' => 'error',
                'message' => 'Failed to retrieve view types',
                'error' => [
                    'code' => 'VIEW_TYPES_RETRIEVAL_FAILED',
                    'details' => $e->getMessage()
                ]
            ], 500);
        }
    }

    /**
     * Get layout types
     */
    public function getLayoutTypes(Request $request): JsonResponse
    {
        try {
            $layoutTypes = [
                'form' => [
                    'name' => 'Form',
                    'description' => 'Form layout for data input',
                    'suitable_for' => ['create', 'update'],
                    'features' => ['field_grouping', 'validation', 'responsive']
                ],
                'table' => [
                    'name' => 'Table',
                    'description' => 'Table layout for data display',
                    'suitable_for' => ['list'],
                    'features' => ['sorting', 'filtering', 'pagination', 'responsive']
                ],
                'grid' => [
                    'name' => 'Grid',
                    'description' => 'Grid layout for data display',
                    'suitable_for' => ['list'],
                    'features' => ['card_view', 'responsive', 'filtering']
                ],
                'card' => [
                    'name' => 'Card',
                    'description' => 'Card layout for data display',
                    'suitable_for' => ['list'],
                    'features' => ['card_view', 'responsive', 'filtering']
                ],
                'dashboard' => [
                    'name' => 'Dashboard',
                    'description' => 'Dashboard layout with widgets',
                    'suitable_for' => ['analytics'],
                    'features' => ['widgets', 'charts', 'responsive']
                ]
            ];

            return response()->json([
                'status' => 'success',
                'message' => 'Layout types retrieved successfully',
                'data' => $layoutTypes
            ]);

        } catch (\Exception $e) {
            Log::error('Failed to retrieve layout types', [
                'error' => $e->getMessage()
            ]);

            return response()->json([
                'status' => 'error',
                'message' => 'Failed to retrieve layout types',
                'error' => [
                    'code' => 'LAYOUT_TYPES_RETRIEVAL_FAILED',
                    'details' => $e->getMessage()
                ]
            ], 500);
        }
    }
}
