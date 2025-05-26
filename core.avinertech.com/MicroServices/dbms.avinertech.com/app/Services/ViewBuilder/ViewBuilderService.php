<?php

namespace App\Services\ViewBuilder;

use Illuminate\Support\Str;

class ViewBuilderService
{
    protected $modelName;
    protected $resourceName;
    protected $fields;
    protected $viewPath;
    protected $layout;
    protected $title;
    protected $description;
    protected $actions;
    protected $relationships;

    public function __construct(
        string $modelName,
        array $fields = [],
        string $viewPath = null,
        string $layout = 'layouts.app'
    ) {
        $this->modelName = Str::studly($modelName);
        $this->resourceName = Str::kebab($modelName);
        $this->fields = $fields;
        $this->viewPath = $viewPath ?? $this->resourceName;
        $this->layout = $layout;
        $this->title = Str::title($modelName);
        $this->description = "Manage {$this->title}";
        $this->actions = [];
        $this->relationships = [];
    }

    /**
     * Set the view title
     *
     * @param string $title
     * @return self
     */
    public function setTitle(string $title): self
    {
        $this->title = $title;
        return $this;
    }

    /**
     * Set the view description
     *
     * @param string $description
     * @return self
     */
    public function setDescription(string $description): self
    {
        $this->description = $description;
        return $this;
    }

    /**
     * Set custom actions
     *
     * @param array $actions
     * @return self
     */
    public function setActions(array $actions): self
    {
        $this->actions = $actions;
        return $this;
    }

    /**
     * Set relationships
     *
     * @param array $relationships
     * @return self
     */
    public function setRelationships(array $relationships): self
    {
        $this->relationships = $relationships;
        return $this;
    }

    /**
     * Generate all views
     *
     * @return array
     */
    public function generate(): array
    {
        return [
            'index' => $this->generateIndexView(),
            'create' => $this->generateCreateView(),
            'edit' => $this->generateEditView(),
            'show' => $this->generateShowView()
        ];
    }

    /**
     * Generate the index view
     *
     * @return array
     */
    protected function generateIndexView(): array
    {
        $content = "@extends('{$this->layout}')\n\n";
        $content .= "@section('title', '{$this->title} List')\n\n";
        $content .= "@section('content')\n";
        $content .= "    <div class=\"container mx-auto px-4 sm:px-6 lg:px-8 py-8\">\n";
        $content .= "        <div class=\"sm:flex sm:items-center\">\n";
        $content .= "            <div class=\"sm:flex-auto\">\n";
        $content .= "                <h1 class=\"text-2xl font-semibold text-gray-900\">{$this->title}</h1>\n";
        $content .= "                <p class=\"mt-2 text-sm text-gray-700\">{$this->description}</p>\n";
        $content .= "            </div>\n";
        $content .= "            <div class=\"mt-4 sm:mt-0 sm:ml-16 sm:flex-none\">\n";
        $content .= "                <a href=\"{{ route('{$this->resourceName}.create') }}\" class=\"inline-flex items-center justify-center rounded-md border border-transparent bg-indigo-600 px-4 py-2 text-sm font-medium text-white shadow-sm hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 sm:w-auto\">\n";
        $content .= "                    Add {$this->title}\n";
        $content .= "                </a>\n";
        $content .= "            </div>\n";
        $content .= "        </div>\n\n";

        // Add custom actions if any
        if (!empty($this->actions)) {
            $content .= "        <div class=\"mt-4 flex space-x-3\">\n";
            foreach ($this->actions as $action) {
                $content .= $this->formatAction($action);
            }
            $content .= "        </div>\n\n";
        }

        // Table
        $content .= "        <div class=\"mt-8 flex flex-col\">\n";
        $content .= "            <div class=\"-my-2 -mx-4 overflow-x-auto sm:-mx-6 lg:-mx-8\">\n";
        $content .= "                <div class=\"inline-block min-w-full py-2 align-middle md:px-6 lg:px-8\">\n";
        $content .= "                    <div class=\"overflow-hidden shadow ring-1 ring-black ring-opacity-5 md:rounded-lg\">\n";
        $content .= "                        <table class=\"min-w-full divide-y divide-gray-300\">\n";
        $content .= "                            <thead class=\"bg-gray-50\">\n";
        $content .= "                                <tr>\n";
        
        // Table headers
        foreach ($this->fields as $field) {
            if (!isset($field['hidden']) || !$field['hidden']) {
                $content .= "                                    <th scope=\"col\" class=\"py-3.5 pl-4 pr-3 text-left text-sm font-semibold text-gray-900 sm:pl-6\">" . Str::title($field['name']) . "</th>\n";
            }
        }
        
        $content .= "                                    <th scope=\"col\" class=\"relative py-3.5 pl-3 pr-4 sm:pr-6\">\n";
        $content .= "                                        <span class=\"sr-only\">Actions</span>\n";
        $content .= "                                    </th>\n";
        $content .= "                                </tr>\n";
        $content .= "                            </thead>\n";
        $content .= "                            <tbody class=\"divide-y divide-gray-200 bg-white\">\n";
        $content .= "                                @foreach(\${$this->resourceName} as \${$this->resourceName}Item)\n";
        $content .= "                                    <tr>\n";
        
        // Table cells
        foreach ($this->fields as $field) {
            if (!isset($field['hidden']) || !$field['hidden']) {
                $content .= $this->formatTableCell($field);
            }
        }
        
        // Actions column
        $content .= "                                        <td class=\"relative whitespace-nowrap py-4 pl-3 pr-4 text-right text-sm font-medium sm:pr-6\">\n";
        $content .= "                                            <div class=\"flex justify-end space-x-3\">\n";
        $content .= "                                                <a href=\"{{ route('{$this->resourceName}.show', \${$this->resourceName}Item) }}\" class=\"text-indigo-600 hover:text-indigo-900\">View</a>\n";
        $content .= "                                                <a href=\"{{ route('{$this->resourceName}.edit', \${$this->resourceName}Item) }}\" class=\"text-indigo-600 hover:text-indigo-900\">Edit</a>\n";
        $content .= "                                                <form action=\"{{ route('{$this->resourceName}.destroy', \${$this->resourceName}Item) }}\" method=\"POST\" class=\"inline\">\n";
        $content .= "                                                    @csrf\n";
        $content .= "                                                    @method('DELETE')\n";
        $content .= "                                                    <button type=\"submit\" class=\"text-red-600 hover:text-red-900\" onclick=\"return confirm('Are you sure you want to delete this item?');\">Delete</button>\n";
        $content .= "                                                </form>\n";
        $content .= "                                            </div>\n";
        $content .= "                                        </td>\n";
        $content .= "                                    </tr>\n";
        $content .= "                                @endforeach\n";
        $content .= "                            </tbody>\n";
        $content .= "                        </table>\n";
        $content .= "                    </div>\n";
        $content .= "                </div>\n";
        $content .= "            </div>\n";
        $content .= "        </div>\n\n";

        // Pagination
        $content .= "        <div class=\"mt-4\">\n";
        $content .= "            {{ \${$this->resourceName}->links() }}\n";
        $content .= "        </div>\n";
        $content .= "    </div>\n";
        $content .= "@endsection\n";

        return [
            'name' => 'index',
            'content' => $content,
            'file_path' => "resources/views/{$this->viewPath}/index.blade.php"
        ];
    }

    /**
     * Generate the create view
     *
     * @return array
     */
    protected function generateCreateView(): array
    {
        $content = "@extends('{$this->layout}')\n\n";
        $content .= "@section('title', 'Create {$this->title}')\n\n";
        $content .= "@section('content')\n";
        $content .= "    <div class=\"container mx-auto px-4 sm:px-6 lg:px-8 py-8\">\n";
        $content .= "        <div class=\"md:grid md:grid-cols-3 md:gap-6\">\n";
        $content .= "            <div class=\"md:col-span-1\">\n";
        $content .= "                <div class=\"px-4 sm:px-0\">\n";
        $content .= "                    <h3 class=\"text-lg font-medium leading-6 text-gray-900\">Create {$this->title}</h3>\n";
        $content .= "                    <p class=\"mt-1 text-sm text-gray-600\">{$this->description}</p>\n";
        $content .= "                </div>\n";
        $content .= "            </div>\n\n";
        $content .= "            <div class=\"mt-5 md:mt-0 md:col-span-2\">\n";
        $content .= "                <form action=\"{{ route('{$this->resourceName}.store') }}\" method=\"POST\">\n";
        $content .= "                    @csrf\n\n";
        $content .= "                    <div class=\"shadow sm:rounded-md sm:overflow-hidden\">\n";
        $content .= "                        <div class=\"px-4 py-5 bg-white space-y-6 sm:p-6\">\n";

        // Form fields
        foreach ($this->fields as $field) {
            if (!isset($field['hidden']) || !$field['hidden']) {
                $content .= $this->formatFormField($field);
            }
        }

        $content .= "                        </div>\n";
        $content .= "                        <div class=\"px-4 py-3 bg-gray-50 text-right sm:px-6\">\n";
        $content .= "                            <a href=\"{{ route('{$this->resourceName}.index') }}\" class=\"inline-flex justify-center py-2 px-4 border border-gray-300 shadow-sm text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 mr-3\">\n";
        $content .= "                                Cancel\n";
        $content .= "                            </a>\n";
        $content .= "                            <button type=\"submit\" class=\"inline-flex justify-center py-2 px-4 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500\">\n";
        $content .= "                                Create\n";
        $content .= "                            </button>\n";
        $content .= "                        </div>\n";
        $content .= "                    </div>\n";
        $content .= "                </form>\n";
        $content .= "            </div>\n";
        $content .= "        </div>\n";
        $content .= "    </div>\n";
        $content .= "@endsection\n";

        return [
            'name' => 'create',
            'content' => $content,
            'file_path' => "resources/views/{$this->viewPath}/create.blade.php"
        ];
    }

    /**
     * Generate the edit view
     *
     * @return array
     */
    protected function generateEditView(): array
    {
        $content = "@extends('{$this->layout}')\n\n";
        $content .= "@section('title', 'Edit {$this->title}')\n\n";
        $content .= "@section('content')\n";
        $content .= "    <div class=\"container mx-auto px-4 sm:px-6 lg:px-8 py-8\">\n";
        $content .= "        <div class=\"md:grid md:grid-cols-3 md:gap-6\">\n";
        $content .= "            <div class=\"md:col-span-1\">\n";
        $content .= "                <div class=\"px-4 sm:px-0\">\n";
        $content .= "                    <h3 class=\"text-lg font-medium leading-6 text-gray-900\">Edit {$this->title}</h3>\n";
        $content .= "                    <p class=\"mt-1 text-sm text-gray-600\">{$this->description}</p>\n";
        $content .= "                </div>\n";
        $content .= "            </div>\n\n";
        $content .= "            <div class=\"mt-5 md:mt-0 md:col-span-2\">\n";
        $content .= "                <form action=\"{{ route('{$this->resourceName}.update', \${$this->resourceName}Item) }}\" method=\"POST\">\n";
        $content .= "                    @csrf\n";
        $content .= "                    @method('PUT')\n\n";
        $content .= "                    <div class=\"shadow sm:rounded-md sm:overflow-hidden\">\n";
        $content .= "                        <div class=\"px-4 py-5 bg-white space-y-6 sm:p-6\">\n";

        // Form fields
        foreach ($this->fields as $field) {
            if (!isset($field['hidden']) || !$field['hidden']) {
                $content .= $this->formatFormField($field, true);
            }
        }

        $content .= "                        </div>\n";
        $content .= "                        <div class=\"px-4 py-3 bg-gray-50 text-right sm:px-6\">\n";
        $content .= "                            <a href=\"{{ route('{$this->resourceName}.index') }}\" class=\"inline-flex justify-center py-2 px-4 border border-gray-300 shadow-sm text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 mr-3\">\n";
        $content .= "                                Cancel\n";
        $content .= "                            </a>\n";
        $content .= "                            <button type=\"submit\" class=\"inline-flex justify-center py-2 px-4 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500\">\n";
        $content .= "                                Update\n";
        $content .= "                            </button>\n";
        $content .= "                        </div>\n";
        $content .= "                    </div>\n";
        $content .= "                </form>\n";
        $content .= "            </div>\n";
        $content .= "        </div>\n";
        $content .= "    </div>\n";
        $content .= "@endsection\n";

        return [
            'name' => 'edit',
            'content' => $content,
            'file_path' => "resources/views/{$this->viewPath}/edit.blade.php"
        ];
    }

    /**
     * Generate the show view
     *
     * @return array
     */
    protected function generateShowView(): array
    {
        $content = "@extends('{$this->layout}')\n\n";
        $content .= "@section('title', '{$this->title} Details')\n\n";
        $content .= "@section('content')\n";
        $content .= "    <div class=\"container mx-auto px-4 sm:px-6 lg:px-8 py-8\">\n";
        $content .= "        <div class=\"bg-white shadow overflow-hidden sm:rounded-lg\">\n";
        $content .= "            <div class=\"px-4 py-5 sm:px-6\">\n";
        $content .= "                <h3 class=\"text-lg leading-6 font-medium text-gray-900\">{$this->title} Information</h3>\n";
        $content .= "                <p class=\"mt-1 max-w-2xl text-sm text-gray-500\">{$this->description}</p>\n";
        $content .= "            </div>\n";
        $content .= "            <div class=\"border-t border-gray-200\">\n";
        $content .= "                <dl>\n";

        // Show fields
        foreach ($this->fields as $field) {
            if (!isset($field['hidden']) || !$field['hidden']) {
                $content .= $this->formatShowField($field);
            }
        }

        // Show relationships if any
        if (!empty($this->relationships)) {
            foreach ($this->relationships as $relationship) {
                $content .= $this->formatRelationship($relationship);
            }
        }

        $content .= "                </dl>\n";
        $content .= "            </div>\n";
        $content .= "            <div class=\"px-4 py-3 bg-gray-50 text-right sm:px-6\">\n";
        $content .= "                <a href=\"{{ route('{$this->resourceName}.index') }}\" class=\"inline-flex justify-center py-2 px-4 border border-gray-300 shadow-sm text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 mr-3\">\n";
        $content .= "                    Back to List\n";
        $content .= "                </a>\n";
        $content .= "                <a href=\"{{ route('{$this->resourceName}.edit', \${$this->resourceName}Item) }}\" class=\"inline-flex justify-center py-2 px-4 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500\">\n";
        $content .= "                    Edit\n";
        $content .= "                </a>\n";
        $content .= "            </div>\n";
        $content .= "        </div>\n";
        $content .= "    </div>\n";
        $content .= "@endsection\n";

        return [
            'name' => 'show',
            'content' => $content,
            'file_path' => "resources/views/{$this->viewPath}/show.blade.php"
        ];
    }

    /**
     * Format a form field
     *
     * @param array $field
     * @param bool $isEdit
     * @return string
     */
    protected function formatFormField(array $field, bool $isEdit = false): string
    {
        $name = $field['name'];
        $label = Str::title($name);
        $type = $field['type'] ?? 'text';
        $required = isset($field['required']) && $field['required'] ? 'required' : '';
        $value = $isEdit ? "{{ old('{$name}', \${$this->resourceName}Item->{$name}) }}" : "{{ old('{$name}') }}";
        
        $content = "                            <div class=\"col-span-6 sm:col-span-4\">\n";
        $content .= "                                <label for=\"{$name}\" class=\"block text-sm font-medium text-gray-700\">{$label}</label>\n";
        
        switch ($type) {
            case 'textarea':
                $content .= "                                <textarea name=\"{$name}\" id=\"{$name}\" rows=\"3\" class=\"mt-1 focus:ring-indigo-500 focus:border-indigo-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md\" {$required}>{$value}</textarea>\n";
                break;
                
            case 'select':
                $content .= "                                <select name=\"{$name}\" id=\"{$name}\" class=\"mt-1 block w-full py-2 px-3 border border-gray-300 bg-white rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm\" {$required}>\n";
                if (isset($field['options'])) {
                    foreach ($field['options'] as $option) {
                        $content .= "                                    <option value=\"{$option['value']}\" {{ {$value} == '{$option['value']}' ? 'selected' : '' }}>{$option['label']}</option>\n";
                    }
                }
                $content .= "                                </select>\n";
                break;
                
            case 'checkbox':
                $content .= "                                <div class=\"mt-2\">\n";
                $content .= "                                    <input type=\"checkbox\" name=\"{$name}\" id=\"{$name}\" value=\"1\" class=\"focus:ring-indigo-500 h-4 w-4 text-indigo-600 border-gray-300 rounded\" {{ {$value} ? 'checked' : '' }} {$required}>\n";
                $content .= "                                    <label for=\"{$name}\" class=\"ml-2 block text-sm text-gray-900\">{$label}</label>\n";
                $content .= "                                </div>\n";
                break;
                
            default:
                $content .= "                                <input type=\"{$type}\" name=\"{$name}\" id=\"{$name}\" value=\"{$value}\" class=\"mt-1 focus:ring-indigo-500 focus:border-indigo-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md\" {$required}>\n";
        }
        
        $content .= "                                @error('{$name}')\n";
        $content .= "                                    <p class=\"mt-2 text-sm text-red-600\">{{ \$message }}</p>\n";
        $content .= "                                @enderror\n";
        $content .= "                            </div>\n";
        
        return $content;
    }

    /**
     * Format a table cell
     *
     * @param array $field
     * @return string
     */
    protected function formatTableCell(array $field): string
    {
        $name = $field['name'];
        $type = $field['type'] ?? 'text';
        
        $content = "                                        <td class=\"whitespace-nowrap py-4 pl-4 pr-3 text-sm sm:pl-6\">\n";
        
        switch ($type) {
            case 'boolean':
                $content .= "                                            <span class=\"inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ \${$this->resourceName}Item->{$name} ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}\">\n";
                $content .= "                                                {{ \${$this->resourceName}Item->{$name} ? 'Yes' : 'No' }}\n";
                $content .= "                                            </span>\n";
                break;
                
            case 'date':
                $content .= "                                            {{ \${$this->resourceName}Item->{$name}->format('Y-m-d') }}\n";
                break;
                
            case 'datetime':
                $content .= "                                            {{ \${$this->resourceName}Item->{$name}->format('Y-m-d H:i:s') }}\n";
                break;
                
            default:
                $content .= "                                            {{ \${$this->resourceName}Item->{$name} }}\n";
        }
        
        $content .= "                                        </td>\n";
        
        return $content;
    }

    /**
     * Format a show field
     *
     * @param array $field
     * @return string
     */
    protected function formatShowField(array $field): string
    {
        $name = $field['name'];
        $label = Str::title($name);
        $type = $field['type'] ?? 'text';
        
        $content = "                    <div class=\"bg-gray-50 px-4 py-5 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6\">\n";
        $content .= "                        <dt class=\"text-sm font-medium text-gray-500\">{$label}</dt>\n";
        $content .= "                        <dd class=\"mt-1 text-sm text-gray-900 sm:mt-0 sm:col-span-2\">\n";
        
        switch ($type) {
            case 'boolean':
                $content .= "                            <span class=\"inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ \${$this->resourceName}Item->{$name} ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}\">\n";
                $content .= "                                {{ \${$this->resourceName}Item->{$name} ? 'Yes' : 'No' }}\n";
                $content .= "                            </span>\n";
                break;
                
            case 'date':
                $content .= "                            {{ \${$this->resourceName}Item->{$name}->format('Y-m-d') }}\n";
                break;
                
            case 'datetime':
                $content .= "                            {{ \${$this->resourceName}Item->{$name}->format('Y-m-d H:i:s') }}\n";
                break;
                
            default:
                $content .= "                            {{ \${$this->resourceName}Item->{$name} }}\n";
        }
        
        $content .= "                        </dd>\n";
        $content .= "                    </div>\n";
        
        return $content;
    }

    /**
     * Format a relationship section
     *
     * @param array $relationship
     * @return string
     */
    protected function formatRelationship(array $relationship): string
    {
        $name = $relationship['name'];
        $label = Str::title($name);
        $type = $relationship['type'] ?? 'hasMany';
        
        $content = "                    <div class=\"bg-white px-4 py-5 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6\">\n";
        $content .= "                        <dt class=\"text-sm font-medium text-gray-500\">{$label}</dt>\n";
        $content .= "                        <dd class=\"mt-1 text-sm text-gray-900 sm:mt-0 sm:col-span-2\">\n";
        
        if ($type === 'hasMany') {
            $content .= "                            <ul class=\"border border-gray-200 rounded-md divide-y divide-gray-200\">\n";
            $content .= "                                @foreach(\${$this->resourceName}Item->{$name} as \$item)\n";
            $content .= "                                    <li class=\"pl-3 pr-4 py-3 flex items-center justify-between text-sm\">\n";
            $content .= "                                        <div class=\"w-0 flex-1 flex items-center\">\n";
            $content .= "                                            <span class=\"ml-2 flex-1 w-0 truncate\">\n";
            $content .= "                                                {{ \$item->name }}\n";
            $content .= "                                            </span>\n";
            $content .= "                                        </div>\n";
            $content .= "                                    </li>\n";
            $content .= "                                @endforeach\n";
            $content .= "                            </ul>\n";
        } else {
            $content .= "                            {{ \${$this->resourceName}Item->{$name}->name ?? 'N/A' }}\n";
        }
        
        $content .= "                        </dd>\n";
        $content .= "                    </div>\n";
        
        return $content;
    }

    /**
     * Format a custom action
     *
     * @param array $action
     * @return string
     */
    protected function formatAction(array $action): string
    {
        $name = $action['name'];
        $label = $action['label'] ?? Str::title($name);
        $route = $action['route'] ?? "{$this->resourceName}.{$name}";
        $method = $action['method'] ?? 'get';
        $class = $action['class'] ?? 'inline-flex items-center px-4 py-2 border border-gray-300 shadow-sm text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500';
        
        if ($method === 'get') {
            return "                <a href=\"{{ route('{$route}') }}\" class=\"{$class}\">{$label}</a>\n";
        }
        
        $content = "                <form action=\"{{ route('{$route}') }}\" method=\"POST\" class=\"inline\">\n";
        $content .= "                    @csrf\n";
        if ($method !== 'post') {
            $content .= "                    @method('{$method}')\n";
        }
        $content .= "                    <button type=\"submit\" class=\"{$class}\">{$label}</button>\n";
        $content .= "                </form>\n";
        
        return $content;
    }
} 