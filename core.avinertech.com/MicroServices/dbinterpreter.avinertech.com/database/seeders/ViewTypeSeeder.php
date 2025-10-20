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
                'name' => 'create',
                'display_name' => 'Create Form',
                'description' => 'Form-based interface for creating new records with validation',
                'icon' => 'plus',
                'color' => '#10B981',
                'default_config' => [
                    'auto_save' => false,
                    'show_validation_errors' => true,
                    'confirm_before_save' => true,
                    'redirect_after_save' => true
                ],
                'is_active' => true,
                'sort_order' => 1
            ],
            [
                'name' => 'update',
                'display_name' => 'Update Form',
                'description' => 'Form-based interface for editing existing records with validation',
                'icon' => 'edit',
                'color' => '#F59E0B',
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
                'sort_order' => 3
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