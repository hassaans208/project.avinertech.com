<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\ViewType;
use App\Models\ViewTypeOption;

class ViewTypeOptionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $viewTypes = ViewType::all()->keyBy('name');
        
        $options = [
            // List View Options
            'list' => [
                [
                    'option_key' => 'show_encrypted',
                    'display_name' => 'Show Encrypted Fields',
                    'description' => 'Display encrypted fields in their encrypted form',
                    'option_type' => 'boolean',
                    'default_value' => false,
                    'is_required' => false,
                    'sort_order' => 1
                ],
                [
                    'option_key' => 'substr',
                    'display_name' => 'Substring Display',
                    'description' => 'Show only a portion of long text fields',
                    'option_type' => 'object',
                    'default_value' => ['enabled' => false, 'length' => 50],
                    'validation_rules' => ['max_length' => 200],
                    'is_required' => false,
                    'sort_order' => 2
                ],
                [
                    'option_key' => 'hide',
                    'display_name' => 'Hide Column',
                    'description' => 'Hide this column from the list view',
                    'option_type' => 'boolean',
                    'default_value' => false,
                    'is_required' => false,
                    'sort_order' => 3
                ],
                [
                    'option_key' => 'sortable',
                    'display_name' => 'Sortable',
                    'description' => 'Allow sorting by this column',
                    'option_type' => 'boolean',
                    'default_value' => true,
                    'is_required' => false,
                    'sort_order' => 4
                ],
                [
                    'option_key' => 'searchable',
                    'display_name' => 'Searchable',
                    'description' => 'Include this column in search functionality',
                    'option_type' => 'boolean',
                    'default_value' => true,
                    'is_required' => false,
                    'sort_order' => 5
                ],
                [
                    'option_key' => 'filterable',
                    'display_name' => 'Filterable',
                    'description' => 'Allow filtering by this column',
                    'option_type' => 'boolean',
                    'default_value' => false,
                    'is_required' => false,
                    'sort_order' => 6
                ],
                [
                    'option_key' => 'exportable',
                    'display_name' => 'Exportable',
                    'description' => 'Include this column in export functionality',
                    'option_type' => 'boolean',
                    'default_value' => true,
                    'is_required' => false,
                    'sort_order' => 7
                ],
                [
                    'option_key' => 'column_width',
                    'display_name' => 'Column Width',
                    'description' => 'Set the width of this column',
                    'option_type' => 'string',
                    'default_value' => 'auto',
                    'possible_values' => ['auto', '50px', '100px', '150px', '200px', '300px'],
                    'is_required' => false,
                    'sort_order' => 8
                ]
            ],
            
            // Create/Update Form Options
            'create/update' => [
                [
                    'option_key' => 'password',
                    'display_name' => 'Password Field',
                    'description' => 'Treat this field as a password input',
                    'option_type' => 'boolean',
                    'default_value' => false,
                    'is_required' => false,
                    'sort_order' => 1
                ],
                [
                    'option_key' => 'is_editable',
                    'display_name' => 'Editable',
                    'description' => 'Allow editing of this field',
                    'option_type' => 'boolean',
                    'default_value' => true,
                    'is_required' => false,
                    'sort_order' => 2
                ],
                [
                    'option_key' => 'hide',
                    'display_name' => 'Hide Field',
                    'description' => 'Hide this field from the form',
                    'option_type' => 'boolean',
                    'default_value' => false,
                    'is_required' => false,
                    'sort_order' => 3
                ],
                [
                    'option_key' => 'is_required',
                    'display_name' => 'Required Field',
                    'description' => 'Make this field required',
                    'option_type' => 'boolean',
                    'default_value' => false,
                    'is_required' => false,
                    'sort_order' => 4
                ],
                [
                    'option_key' => 'validation_rules',
                    'display_name' => 'Validation Rules',
                    'description' => 'Custom validation rules for this field',
                    'option_type' => 'array',
                    'default_value' => [],
                    'is_required' => false,
                    'sort_order' => 5
                ],
                [
                    'option_key' => 'placeholder_text',
                    'display_name' => 'Placeholder Text',
                    'description' => 'Placeholder text for the input field',
                    'option_type' => 'string',
                    'default_value' => '',
                    'validation_rules' => ['max_length' => 100],
                    'is_required' => false,
                    'sort_order' => 6
                ],
                [
                    'option_key' => 'help_text',
                    'display_name' => 'Help Text',
                    'description' => 'Additional help text for the field',
                    'option_type' => 'string',
                    'default_value' => '',
                    'validation_rules' => ['max_length' => 200],
                    'is_required' => false,
                    'sort_order' => 7
                ],
                [
                    'option_key' => 'input_type',
                    'display_name' => 'Input Type',
                    'description' => 'Type of input field to use',
                    'option_type' => 'string',
                    'default_value' => 'text',
                    'possible_values' => [
                        ['value' => 'text', 'label' => 'Text'],
                        ['value' => 'email', 'label' => 'Email'],
                        ['value' => 'number', 'label' => 'Number'],
                        ['value' => 'date', 'label' => 'Date'],
                        ['value' => 'datetime', 'label' => 'Date & Time'],
                        ['value' => 'textarea', 'label' => 'Textarea'],
                        ['value' => 'select', 'label' => 'Select'],
                        ['value' => 'checkbox', 'label' => 'Checkbox'],
                        ['value' => 'radio', 'label' => 'Radio']
                    ],
                    'is_required' => false,
                    'sort_order' => 8
                ]
            ],
            
            // Analytics Options
            'analytics' => [
                [
                    'option_key' => 'chart_type',
                    'display_name' => 'Chart Type',
                    'description' => 'Type of chart to display for this data',
                    'option_type' => 'string',
                    'default_value' => 'bar',
                    'possible_values' => [
                        ['value' => 'bar', 'label' => 'Bar Chart'],
                        ['value' => 'line', 'label' => 'Line Chart'],
                        ['value' => 'pie', 'label' => 'Pie Chart'],
                        ['value' => 'scatter', 'label' => 'Scatter Plot'],
                        ['value' => 'area', 'label' => 'Area Chart'],
                        ['value' => 'doughnut', 'label' => 'Doughnut Chart']
                    ],
                    'is_required' => true,
                    'sort_order' => 1
                ],
                [
                    'option_key' => 'aggregation_type',
                    'display_name' => 'Aggregation Type',
                    'description' => 'How to aggregate the data',
                    'option_type' => 'string',
                    'default_value' => 'count',
                    'possible_values' => [
                        ['value' => 'count', 'label' => 'Count'],
                        ['value' => 'sum', 'label' => 'Sum'],
                        ['value' => 'avg', 'label' => 'Average'],
                        ['value' => 'min', 'label' => 'Minimum'],
                        ['value' => 'max', 'label' => 'Maximum']
                    ],
                    'is_required' => true,
                    'sort_order' => 2
                ],
                [
                    'option_key' => 'group_by',
                    'display_name' => 'Group By',
                    'description' => 'Field to group the data by',
                    'option_type' => 'string',
                    'default_value' => '',
                    'is_required' => false,
                    'sort_order' => 3
                ],
                [
                    'option_key' => 'time_range',
                    'display_name' => 'Time Range',
                    'description' => 'Default time range for the analytics',
                    'option_type' => 'string',
                    'default_value' => '30d',
                    'possible_values' => [
                        ['value' => '7d', 'label' => 'Last 7 Days'],
                        ['value' => '30d', 'label' => 'Last 30 Days'],
                        ['value' => '90d', 'label' => 'Last 90 Days'],
                        ['value' => '1y', 'label' => 'Last Year'],
                        ['value' => 'all', 'label' => 'All Time']
                    ],
                    'is_required' => false,
                    'sort_order' => 4
                ],
                [
                    'option_key' => 'show_trends',
                    'display_name' => 'Show Trends',
                    'description' => 'Display trend indicators',
                    'option_type' => 'boolean',
                    'default_value' => true,
                    'is_required' => false,
                    'sort_order' => 5
                ],
                [
                    'option_key' => 'compare_period',
                    'display_name' => 'Compare Period',
                    'description' => 'Enable period comparison',
                    'option_type' => 'boolean',
                    'default_value' => false,
                    'is_required' => false,
                    'sort_order' => 6
                ],
                [
                    'option_key' => 'drill_down',
                    'display_name' => 'Drill Down',
                    'description' => 'Allow drilling down into detailed data',
                    'option_type' => 'boolean',
                    'default_value' => true,
                    'is_required' => false,
                    'sort_order' => 7
                ]
            ],
            
            // Soft Delete Options (minimal options as specified)
            'soft-delete' => [
                [
                    'option_key' => 'show_deleted_at',
                    'display_name' => 'Show Deleted At',
                    'description' => 'Display the deletion timestamp',
                    'option_type' => 'boolean',
                    'default_value' => true,
                    'is_required' => false,
                    'sort_order' => 1
                ],
                [
                    'option_key' => 'allow_restore',
                    'display_name' => 'Allow Restore',
                    'description' => 'Allow restoring deleted records',
                    'option_type' => 'boolean',
                    'default_value' => true,
                    'is_required' => false,
                    'sort_order' => 2
                ],
                [
                    'option_key' => 'allow_permanent_delete',
                    'display_name' => 'Allow Permanent Delete',
                    'description' => 'Allow permanently deleting records',
                    'option_type' => 'boolean',
                    'default_value' => false,
                    'is_required' => false,
                    'sort_order' => 3
                ],
                [
                    'option_key' => 'bulk_operations',
                    'display_name' => 'Bulk Operations',
                    'description' => 'Enable bulk restore/delete operations',
                    'option_type' => 'boolean',
                    'default_value' => true,
                    'is_required' => false,
                    'sort_order' => 4
                ]
            ]
        ];

        foreach ($options as $viewTypeName => $viewTypeOptions) {
            $viewType = $viewTypes->get($viewTypeName);
            
            if (!$viewType) {
                continue;
            }
            
            foreach ($viewTypeOptions as $option) {
                ViewTypeOption::updateOrCreate(
                    [
                        'view_type_id' => $viewType->id,
                        'option_key' => $option['option_key']
                    ],
                    array_merge($option, ['is_active' => true])
                );
            }
        }
    }
}