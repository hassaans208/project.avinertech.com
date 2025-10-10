<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\ViewType;

class ViewTypeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $viewTypes = [
            [
                'name' => 'list',
                'display_name' => 'List View',
                'description' => 'Display data in a tabular format with sorting, filtering, and pagination capabilities',
                'icon' => 'table',
                'color' => '#3B82F6',
                'default_config' => [
                    'pagination' => true,
                    'page_size' => 25,
                    'show_search' => true,
                    'show_filters' => true,
                    'export_enabled' => true
                ],
                'is_active' => true,
                'sort_order' => 1
            ],
            [
                'name' => 'create/update',
                'display_name' => 'Create/Update Form',
                'description' => 'Form-based interface for creating and editing records with validation',
                'icon' => 'edit',
                'color' => '#10B981',
                'default_config' => [
                    'auto_save' => false,
                    'show_validation_errors' => true,
                    'confirm_before_save' => true,
                    'redirect_after_save' => true
                ],
                'is_active' => true,
                'sort_order' => 2
            ],
            [
                'name' => 'analytics',
                'display_name' => 'Analytics Dashboard',
                'description' => 'Interactive dashboard with charts, graphs, and data analysis tools',
                'icon' => 'chart-bar',
                'color' => '#F59E0B',
                'default_config' => [
                    'chart_types' => ['bar', 'line', 'pie', 'scatter'],
                    'time_range_options' => ['7d', '30d', '90d', '1y'],
                    'auto_refresh' => true,
                    'refresh_interval' => 300
                ],
                'is_active' => true,
                'sort_order' => 3
            ],
            [
                'name' => 'soft-delete',
                'display_name' => 'Soft Delete Management',
                'description' => 'Manage soft-deleted records with restore and permanent delete options',
                'icon' => 'trash',
                'color' => '#EF4444',
                'default_config' => [
                    'show_deleted_at' => true,
                    'allow_restore' => true,
                    'allow_permanent_delete' => false,
                    'bulk_operations' => true
                ],
                'is_active' => true,
                'sort_order' => 4
            ]
        ];

        foreach ($viewTypes as $viewType) {
            ViewType::updateOrCreate(
                ['name' => $viewType['name']],
                $viewType
            );
        }
    }
}