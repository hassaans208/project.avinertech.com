<?php

namespace App\Services\CrudBuilder;

use Illuminate\Support\Str;
use Illuminate\Support\Facades\File;

class CrudBuilderService
{
    protected $modelName;
    protected $modelNamespace;
    protected $controllerNamespace;
    protected $viewPath;
    protected $routePrefix;
    protected $resourceName;
    protected $fields;
    protected $relationships;
    protected $validationRules;

    public function __construct(
        string $modelName,
        string $modelNamespace = 'App\\Models',
        string $controllerNamespace = 'App\\Http\\Controllers',
        string $viewPath = 'resources/views',
        string $routePrefix = ''
    ) {
        $this->modelName = Str::studly($modelName);
        $this->modelNamespace = $modelNamespace;
        $this->controllerNamespace = $controllerNamespace;
        $this->viewPath = $viewPath;
        $this->routePrefix = $routePrefix;
        $this->resourceName = Str::kebab($modelName);
        $this->fields = [];
        $this->relationships = [];
        $this->validationRules = [];
    }

    /**
     * Set the fields for the CRUD operations
     *
     * @param array $fields
     * @return self
     */
    public function setFields(array $fields): self
    {
        $this->fields = $fields;
        return $this;
    }

    /**
     * Set the relationships for the model
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
     * Set the validation rules
     *
     * @param array $rules
     * @return self
     */
    public function setValidationRules(array $rules): self
    {
        $this->validationRules = $rules;
        return $this;
    }

    /**
     * Generate the complete CRUD structure
     *
     * @return array
     */
    public function generate(): array
    {
        return [
            'controller' => $this->generateController(),
            'views' => $this->generateViews(),
            'routes' => $this->generateRoutes(),
            'model' => $this->generateModel(),
        ];
    }

    /**
     * Generate the controller code
     *
     * @return string
     */
    protected function generateController(): string
    {
        $controllerName = "{$this->modelName}Controller";
        $viewPath = $this->resourceName;
        $modelClass = "{$this->modelNamespace}\\{$this->modelName}";
        $modelVariable = Str::camel($this->modelName);
        $validationRules = $this->formatValidationRules();

        $content = "<?php\n\n";
        $content .= "namespace {$this->controllerNamespace};\n\n";
        $content .= "use App\\Http\\Controllers\\Controller;\n";
        $content .= "use {$modelClass};\n";
        $content .= "use Illuminate\\Http\\Request;\n";
        $content .= "use Illuminate\\Support\\Facades\\View;\n\n";
        $content .= "class {$controllerName} extends Controller\n{\n";
        
        // Index method
        $content .= "    public function index()\n    {\n";
        $content .= "        \${$modelVariable}s = {$this->modelName}::paginate(10);\n";
        $content .= "        return view('{$viewPath}.index', compact('{$modelVariable}s'));\n";
        $content .= "    }\n\n";

        // Create method
        $content .= "    public function create()\n    {\n";
        $content .= "        return view('{$viewPath}.create');\n";
        $content .= "    }\n\n";

        // Store method
        $content .= "    public function store(Request \$request)\n    {\n";
        $content .= "        \$validated = \$request->validate({$validationRules});\n\n";
        $content .= "        \${$modelVariable} = {$this->modelName}::create(\$validated);\n\n";
        $content .= "        return redirect()->route('{$this->resourceName}.index')\n";
        $content .= "            ->with('success', '{$this->modelName} created successfully.');\n";
        $content .= "    }\n\n";

        // Show method
        $content .= "    public function show({$this->modelName} \${$modelVariable})\n    {\n";
        $content .= "        return view('{$viewPath}.show', compact('{$modelVariable}'));\n";
        $content .= "    }\n\n";

        // Edit method
        $content .= "    public function edit({$this->modelName} \${$modelVariable})\n    {\n";
        $content .= "        return view('{$viewPath}.edit', compact('{$modelVariable}'));\n";
        $content .= "    }\n\n";

        // Update method
        $content .= "    public function update(Request \$request, {$this->modelName} \${$modelVariable})\n    {\n";
        $content .= "        \$validated = \$request->validate({$validationRules});\n\n";
        $content .= "        \${$modelVariable}->update(\$validated);\n\n";
        $content .= "        return redirect()->route('{$this->resourceName}.index')\n";
        $content .= "            ->with('success', '{$this->modelName} updated successfully.');\n";
        $content .= "    }\n\n";

        // Destroy method
        $content .= "    public function destroy({$this->modelName} \${$modelVariable})\n    {\n";
        $content .= "        \${$modelVariable}->delete();\n\n";
        $content .= "        return redirect()->route('{$this->resourceName}.index')\n";
        $content .= "            ->with('success', '{$this->modelName} deleted successfully.');\n";
        $content .= "    }\n";

        $content .= "}\n";

        return $content;
    }

    /**
     * Generate the views
     *
     * @return array
     */
    protected function generateViews(): array
    {
        $views = [];
        $viewPath = "{$this->viewPath}/{$this->resourceName}";
        $modelVariable = Str::camel($this->modelName);

        // Create view directory if it doesn't exist
        if (!File::exists($viewPath)) {
            File::makeDirectory($viewPath, 0755, true);
        }

        // Index view
        $views['index'] = $this->generateIndexView();
        File::put("{$viewPath}/index.blade.php", $views['index']);

        // Create view
        $views['create'] = $this->generateCreateView();
        File::put("{$viewPath}/create.blade.php", $views['create']);

        // Edit view
        $views['edit'] = $this->generateEditView();
        File::put("{$viewPath}/edit.blade.php", $views['edit']);

        // Show view
        $views['show'] = $this->generateShowView();
        File::put("{$viewPath}/show.blade.php", $views['show']);

        return $views;
    }

    /**
     * Generate the index view
     *
     * @return string
     */
    protected function generateIndexView(): string
    {
        $content = "@extends('layouts.app')\n\n";
        $content .= "@section('content')\n";
        $content .= "<div class=\"container mx-auto px-4 py-8\">\n";
        $content .= "    <div class=\"flex justify-between items-center mb-6\">\n";
        $content .= "        <h1 class=\"text-2xl font-bold\">{{ __('{$this->modelName} List') }}</h1>\n";
        $content .= "        <a href=\"{{ route('{$this->resourceName}.create') }}\" class=\"bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded\">\n";
        $content .= "            {{ __('Create New') }}\n";
        $content .= "        </a>\n";
        $content .= "    </div>\n\n";

        // Success message
        $content .= "    @if (session('success'))\n";
        $content .= "        <div class=\"bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative mb-4\" role=\"alert\">\n";
        $content .= "            <span class=\"block sm:inline\">{{ session('success') }}</span>\n";
        $content .= "        </div>\n";
        $content .= "    @endif\n\n";

        // Table
        $content .= "    <div class=\"bg-white shadow-md rounded my-6\">\n";
        $content .= "        <table class=\"min-w-full\">\n";
        $content .= "            <thead>\n";
        $content .= "                <tr class=\"bg-gray-100\">\n";
        
        // Table headers
        foreach ($this->fields as $field) {
            $content .= "                    <th class=\"px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider\">\n";
            $content .= "                        {{ __('" . Str::title($field['name']) . "') }}\n";
            $content .= "                    </th>\n";
        }
        
        $content .= "                    <th class=\"px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider\">\n";
        $content .= "                        {{ __('Actions') }}\n";
        $content .= "                    </th>\n";
        $content .= "                </tr>\n";
        $content .= "            </thead>\n";
        $content .= "            <tbody class=\"bg-white divide-y divide-gray-200\">\n";
        $content .= "                @foreach (\${$modelVariable}s as \${$modelVariable})\n";
        $content .= "                    <tr>\n";
        
        // Table cells
        foreach ($this->fields as $field) {
            $content .= "                        <td class=\"px-6 py-4 whitespace-nowrap\">\n";
            $content .= "                            {{ \${$modelVariable}->{$field['name']} }}\n";
            $content .= "                        </td>\n";
        }
        
        // Action buttons
        $content .= "                        <td class=\"px-6 py-4 whitespace-nowrap text-right text-sm font-medium\">\n";
        $content .= "                            <a href=\"{{ route('{$this->resourceName}.show', \${$modelVariable}) }}\" class=\"text-blue-600 hover:text-blue-900 mr-3\">{{ __('View') }}</a>\n";
        $content .= "                            <a href=\"{{ route('{$this->resourceName}.edit', \${$modelVariable}) }}\" class=\"text-indigo-600 hover:text-indigo-900 mr-3\">{{ __('Edit') }}</a>\n";
        $content .= "                            <form action=\"{{ route('{$this->resourceName}.destroy', \${$modelVariable}) }}\" method=\"POST\" class=\"inline\">\n";
        $content .= "                                @csrf\n";
        $content .= "                                @method('DELETE')\n";
        $content .= "                                <button type=\"submit\" class=\"text-red-600 hover:text-red-900\" onclick=\"return confirm('{{ __('Are you sure?') }}')\">{{ __('Delete') }}</button>\n";
        $content .= "                            </form>\n";
        $content .= "                        </td>\n";
        $content .= "                    </tr>\n";
        $content .= "                @endforeach\n";
        $content .= "            </tbody>\n";
        $content .= "        </table>\n";
        $content .= "    </div>\n\n";
        
        // Pagination
        $content .= "    <div class=\"mt-4\">\n";
        $content .= "        {{ \${$modelVariable}s->links() }}\n";
        $content .= "    </div>\n";
        $content .= "</div>\n";
        $content .= "@endsection\n";

        return $content;
    }

    /**
     * Generate the create view
     *
     * @return string
     */
    protected function generateCreateView(): string
    {
        $content = "@extends('layouts.app')\n\n";
        $content .= "@section('content')\n";
        $content .= "<div class=\"container mx-auto px-4 py-8\">\n";
        $content .= "    <div class=\"max-w-2xl mx-auto\">\n";
        $content .= "        <h1 class=\"text-2xl font-bold mb-6\">{{ __('Create {$this->modelName}') }}</h1>\n\n";
        
        // Form
        $content .= "        <form action=\"{{ route('{$this->resourceName}.store') }}\" method=\"POST\" class=\"bg-white shadow-md rounded px-8 pt-6 pb-8 mb-4\">\n";
        $content .= "            @csrf\n\n";
        
        // Form fields
        foreach ($this->fields as $field) {
            if ($field['name'] === 'id' || $field['name'] === 'created_at' || $field['name'] === 'updated_at') {
                continue;
            }
            
            $content .= $this->generateFormField($field);
        }
        
        // Submit button
        $content .= "            <div class=\"flex items-center justify-between mt-6\">\n";
        $content .= "                <button type=\"submit\" class=\"bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded focus:outline-none focus:shadow-outline\">\n";
        $content .= "                    {{ __('Create') }}\n";
        $content .= "                </button>\n";
        $content .= "                <a href=\"{{ route('{$this->resourceName}.index') }}\" class=\"text-gray-600 hover:text-gray-800\">\n";
        $content .= "                    {{ __('Cancel') }}\n";
        $content .= "                </a>\n";
        $content .= "            </div>\n";
        $content .= "        </form>\n";
        $content .= "    </div>\n";
        $content .= "</div>\n";
        $content .= "@endsection\n";

        return $content;
    }

    /**
     * Generate the edit view
     *
     * @return string
     */
    protected function generateEditView(): string
    {
        $content = "@extends('layouts.app')\n\n";
        $content .= "@section('content')\n";
        $content .= "<div class=\"container mx-auto px-4 py-8\">\n";
        $content .= "    <div class=\"max-w-2xl mx-auto\">\n";
        $content .= "        <h1 class=\"text-2xl font-bold mb-6\">{{ __('Edit {$this->modelName}') }}</h1>\n\n";
        
        // Form
        $content .= "        <form action=\"{{ route('{$this->resourceName}.update', \${$modelVariable}) }}\" method=\"POST\" class=\"bg-white shadow-md rounded px-8 pt-6 pb-8 mb-4\">\n";
        $content .= "            @csrf\n";
        $content .= "            @method('PUT')\n\n";
        
        // Form fields
        foreach ($this->fields as $field) {
            if ($field['name'] === 'id' || $field['name'] === 'created_at' || $field['name'] === 'updated_at') {
                continue;
            }
            
            $content .= $this->generateFormField($field, true);
        }
        
        // Submit button
        $content .= "            <div class=\"flex items-center justify-between mt-6\">\n";
        $content .= "                <button type=\"submit\" class=\"bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded focus:outline-none focus:shadow-outline\">\n";
        $content .= "                    {{ __('Update') }}\n";
        $content .= "                </button>\n";
        $content .= "                <a href=\"{{ route('{$this->resourceName}.index') }}\" class=\"text-gray-600 hover:text-gray-800\">\n";
        $content .= "                    {{ __('Cancel') }}\n";
        $content .= "                </a>\n";
        $content .= "            </div>\n";
        $content .= "        </form>\n";
        $content .= "    </div>\n";
        $content .= "</div>\n";
        $content .= "@endsection\n";

        return $content;
    }

    /**
     * Generate the show view
     *
     * @return string
     */
    protected function generateShowView(): string
    {
        $content = "@extends('layouts.app')\n\n";
        $content .= "@section('content')\n";
        $content .= "<div class=\"container mx-auto px-4 py-8\">\n";
        $content .= "    <div class=\"max-w-2xl mx-auto\">\n";
        $content .= "        <div class=\"flex justify-between items-center mb-6\">\n";
        $content .= "            <h1 class=\"text-2xl font-bold\">{{ __('{$this->modelName} Details') }}</h1>\n";
        $content .= "            <div class=\"space-x-4\">\n";
        $content .= "                <a href=\"{{ route('{$this->resourceName}.edit', \${$modelVariable}) }}\" class=\"bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded\">\n";
        $content .= "                    {{ __('Edit') }}\n";
        $content .= "                </a>\n";
        $content .= "                <a href=\"{{ route('{$this->resourceName}.index') }}\" class=\"bg-gray-500 hover:bg-gray-700 text-white font-bold py-2 px-4 rounded\">\n";
        $content .= "                    {{ __('Back to List') }}\n";
        $content .= "                </a>\n";
        $content .= "            </div>\n";
        $content .= "        </div>\n\n";
        
        // Details
        $content .= "        <div class=\"bg-white shadow-md rounded px-8 pt-6 pb-8 mb-4\">\n";
        foreach ($this->fields as $field) {
            if ($field['name'] === 'id' || $field['name'] === 'created_at' || $field['name'] === 'updated_at') {
                continue;
            }
            
            $content .= "            <div class=\"mb-4\">\n";
            $content .= "                <label class=\"block text-gray-700 text-sm font-bold mb-2\">\n";
            $content .= "                    {{ __('" . Str::title($field['name']) . "') }}\n";
            $content .= "                </label>\n";
            $content .= "                <p class=\"text-gray-900\">{{ \${$modelVariable}->{$field['name']} }}</p>\n";
            $content .= "            </div>\n";
        }
        $content .= "        </div>\n";
        $content .= "    </div>\n";
        $content .= "</div>\n";
        $content .= "@endsection\n";

        return $content;
    }

    /**
     * Generate a form field
     *
     * @param array $field
     * @param bool $isEdit
     * @return string
     */
    protected function generateFormField(array $field, bool $isEdit = false): string
    {
        $content = "            <div class=\"mb-4\">\n";
        $content .= "                <label for=\"{$field['name']}\" class=\"block text-gray-700 text-sm font-bold mb-2\">\n";
        $content .= "                    {{ __('" . Str::title($field['name']) . "') }}\n";
        $content .= "                </label>\n";

        $fieldType = $this->getFieldType($field);
        $modelVariable = Str::camel($this->modelName);

        switch ($fieldType) {
            case 'textarea':
                $content .= "                <textarea name=\"{$field['name']}\" id=\"{$field['name']}\" rows=\"4\" class=\"shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline @error('{$field['name']}') border-red-500 @enderror\">";
                if ($isEdit) {
                    $content .= "{{ old('{$field['name']}', \${$modelVariable}->{$field['name']}) }}";
                } else {
                    $content .= "{{ old('{$field['name']}') }}";
                }
                $content .= "</textarea>\n";
                break;

            case 'select':
                $content .= "                <select name=\"{$field['name']}\" id=\"{$field['name']}\" class=\"shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline @error('{$field['name']}') border-red-500 @enderror\">\n";
                $content .= "                    <option value=\"\">{{ __('Select " . Str::title($field['name']) . "') }}</option>\n";
                if (isset($field['options'])) {
                    foreach ($field['options'] as $value => $label) {
                        $content .= "                    <option value=\"{$value}\" {{ (old('{$field['name']}', " . ($isEdit ? "\${$modelVariable}->{$field['name']}" : "''") . ") == '{$value}') ? 'selected' : '' }}>{{ __('{$label}') }}</option>\n";
                    }
                }
                $content .= "                </select>\n";
                break;

            case 'checkbox':
                $content .= "                <input type=\"checkbox\" name=\"{$field['name']}\" id=\"{$field['name']}\" value=\"1\" class=\"mr-2 @error('{$field['name']}') border-red-500 @enderror\" ";
                if ($isEdit) {
                    $content .= "{{ \${$modelVariable}->{$field['name']} ? 'checked' : '' }}";
                } else {
                    $content .= "{{ old('{$field['name']}') ? 'checked' : '' }}";
                }
                $content .= ">\n";
                break;

            default:
                $content .= "                <input type=\"{$fieldType}\" name=\"{$field['name']}\" id=\"{$field['name']}\" value=\"";
                if ($isEdit) {
                    $content .= "{{ old('{$field['name']}', \${$modelVariable}->{$field['name']}) }}";
                } else {
                    $content .= "{{ old('{$field['name']}') }}";
                }
                $content .= "\" class=\"shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline @error('{$field['name']}') border-red-500 @enderror\">\n";
        }

        $content .= "                @error('{$field['name']}')\n";
        $content .= "                    <p class=\"text-red-500 text-xs italic mt-1\">{{ \$message }}</p>\n";
        $content .= "                @enderror\n";
        $content .= "            </div>\n";

        return $content;
    }

    /**
     * Get the HTML field type based on the field definition
     *
     * @param array $field
     * @return string
     */
    protected function getFieldType(array $field): string
    {
        if (isset($field['type'])) {
            switch ($field['type']) {
                case 'text':
                    return 'text';
                case 'textarea':
                    return 'textarea';
                case 'email':
                    return 'email';
                case 'password':
                    return 'password';
                case 'number':
                    return 'number';
                case 'date':
                    return 'date';
                case 'datetime':
                    return 'datetime-local';
                case 'boolean':
                    return 'checkbox';
                case 'select':
                    return 'select';
                default:
                    return 'text';
            }
        }

        return 'text';
    }

    /**
     * Generate the routes
     *
     * @return string
     */
    protected function generateRoutes(): string
    {
        $routePrefix = $this->routePrefix ? "{$this->routePrefix}/" : '';
        $controllerName = "{$this->modelName}Controller";
        $controllerClass = "{$this->controllerNamespace}\\{$controllerName}";

        return "Route::resource('{$routePrefix}{$this->resourceName}', {$controllerClass}::class);\n";
    }

    /**
     * Generate the model
     *
     * @return string
     */
    protected function generateModel(): string
    {
        $content = "<?php\n\n";
        $content .= "namespace {$this->modelNamespace};\n\n";
        $content .= "use Illuminate\\Database\\Eloquent\\Model;\n";
        $content .= "use Illuminate\\Database\\Eloquent\\Factories\\HasFactory;\n\n";
        $content .= "class {$this->modelName} extends Model\n{\n";
        $content .= "    use HasFactory;\n\n";

        // Fillable attributes
        $fillable = array_map(function ($field) {
            return $field['name'];
        }, array_filter($this->fields, function ($field) {
            return $field['name'] !== 'id' && $field['name'] !== 'created_at' && $field['name'] !== 'updated_at';
        }));

        $content .= "    protected \$fillable = " . $this->formatArray($fillable) . ";\n\n";

        // Casts
        $casts = [];
        foreach ($this->fields as $field) {
            if (isset($field['cast'])) {
                $casts[$field['name']] = $field['cast'];
            }
        }

        if (!empty($casts)) {
            $content .= "    protected \$casts = " . $this->formatArray($casts) . ";\n\n";
        }

        // Relationships
        foreach ($this->relationships as $relationship) {
            $content .= $this->generateRelationshipMethod($relationship);
        }

        $content .= "}\n";

        return $content;
    }

    /**
     * Generate a relationship method
     *
     * @param array $relationship
     * @return string
     */
    protected function generateRelationshipMethod(array $relationship): string
    {
        $method = "    public function {$relationship['name']}()\n    {\n";
        $method .= "        return \$this->{$relationship['type']}(";
        
        $args = ["{$relationship['related_model']}::class"];
        if (isset($relationship['foreign_key'])) {
            $args[] = "'{$relationship['foreign_key']}'";
        }
        if (isset($relationship['local_key'])) {
            $args[] = "'{$relationship['local_key']}'";
        }
        
        $method .= implode(', ', $args);
        $method .= ");\n    }\n\n";
        
        return $method;
    }

    /**
     * Format an array for PHP code
     *
     * @param array $array
     * @return string
     */
    protected function formatArray(array $array): string
    {
        if (empty($array)) {
            return '[]';
        }

        $items = [];
        foreach ($array as $key => $value) {
            if (is_string($key)) {
                $items[] = "'{$key}' => " . (is_string($value) ? "'{$value}'" : $value);
            } else {
                $items[] = is_string($value) ? "'{$value}'" : $value;
            }
        }

        return "[\n            " . implode(",\n            ", $items) . "\n        ]";
    }

    /**
     * Format validation rules for the controller
     *
     * @return string
     */
    protected function formatValidationRules(): string
    {
        if (empty($this->validationRules)) {
            return '[]';
        }

        $rules = [];
        foreach ($this->validationRules as $field => $rule) {
            $rules[] = "            '{$field}' => '{$rule}'";
        }

        return "[\n" . implode(",\n", $rules) . "\n        ]";
    }
} 