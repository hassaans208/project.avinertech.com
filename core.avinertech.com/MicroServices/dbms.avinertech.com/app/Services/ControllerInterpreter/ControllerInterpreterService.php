<?php

namespace App\Services\ControllerInterpreter;

use Illuminate\Support\Str;

class ControllerInterpreterService
{
    protected $modelName;
    protected $modelNamespace;
    protected $controllerNamespace;
    protected $resourceName;
    protected $validationRules;
    protected $methods;
    protected $traits;
    protected $uses;

    public function __construct(
        string $modelName,
        string $modelNamespace = 'App\\Models',
        string $controllerNamespace = 'App\\Http\\Controllers'
    ) {
        $this->modelName = Str::studly($modelName);
        $this->modelNamespace = $modelNamespace;
        $this->controllerNamespace = $controllerNamespace;
        $this->resourceName = Str::kebab($modelName);
        $this->validationRules = [];
        $this->methods = [];
        $this->traits = [];
        $this->uses = [];
    }

    /**
     * Set the validation rules for the controller
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
     * Add custom methods to the controller
     *
     * @param array $methods
     * @return self
     */
    public function setMethods(array $methods): self
    {
        $this->methods = $methods;
        return $this;
    }

    /**
     * Add traits to the controller
     *
     * @param array $traits
     * @return self
     */
    public function setTraits(array $traits): self
    {
        $this->traits = $traits;
        return $this;
    }

    /**
     * Add use statements to the controller
     *
     * @param array $uses
     * @return self
     */
    public function setUses(array $uses): self
    {
        $this->uses = $uses;
        return $this;
    }

    /**
     * Generate the controller code
     *
     * @return array
     */
    public function generate(): array
    {
        $controllerName = "{$this->modelName}Controller";
        $viewPath = $this->resourceName;
        $modelClass = "{$this->modelNamespace}\\{$this->modelName}";
        $modelVariable = Str::camel($this->modelName);
        $validationRules = $this->formatValidationRules();

        $content = "<?php\n\n";
        $content .= "namespace {$this->controllerNamespace};\n\n";
        
        // Add use statements
        $content .= "use App\\Http\\Controllers\\Controller;\n";
        $content .= "use {$modelClass};\n";
        $content .= "use Illuminate\\Http\\Request;\n";
        $content .= "use Illuminate\\Support\\Facades\\View;\n";
        
        // Add custom use statements
        foreach ($this->uses as $use) {
            $content .= "use {$use};\n";
        }
        $content .= "\n";

        // Start class definition
        $content .= "class {$controllerName} extends Controller\n{\n";
        
        // Add traits
        if (!empty($this->traits)) {
            foreach ($this->traits as $trait) {
                $content .= "    use {$trait};\n";
            }
            $content .= "\n";
        }

        // Index method
        $content .= "    /**\n";
        $content .= "     * Display a listing of the resource.\n";
        $content .= "     *\n";
        $content .= "     * @return \\Illuminate\\View\\View\n";
        $content .= "     */\n";
        $content .= "    public function index()\n    {\n";
        $content .= "        \${$modelVariable}s = {$this->modelName}::paginate(10);\n";
        $content .= "        return view('{$viewPath}.index', compact('{$modelVariable}s'));\n";
        $content .= "    }\n\n";

        // Create method
        $content .= "    /**\n";
        $content .= "     * Show the form for creating a new resource.\n";
        $content .= "     *\n";
        $content .= "     * @return \\Illuminate\\View\\View\n";
        $content .= "     */\n";
        $content .= "    public function create()\n    {\n";
        $content .= "        return view('{$viewPath}.create');\n";
        $content .= "    }\n\n";

        // Store method
        $content .= "    /**\n";
        $content .= "     * Store a newly created resource in storage.\n";
        $content .= "     *\n";
        $content .= "     * @param  \\Illuminate\\Http\\Request  \$request\n";
        $content .= "     * @return \\Illuminate\\Http\\RedirectResponse\n";
        $content .= "     */\n";
        $content .= "    public function store(Request \$request)\n    {\n";
        $content .= "        \$validated = \$request->validate({$validationRules});\n\n";
        $content .= "        \${$modelVariable} = {$this->modelName}::create(\$validated);\n\n";
        $content .= "        return redirect()->route('{$this->resourceName}.index')\n";
        $content .= "            ->with('success', '{$this->modelName} created successfully.');\n";
        $content .= "    }\n\n";

        // Show method
        $content .= "    /**\n";
        $content .= "     * Display the specified resource.\n";
        $content .= "     *\n";
        $content .= "     * @param  {$this->modelName}  \${$modelVariable}\n";
        $content .= "     * @return \\Illuminate\\View\\View\n";
        $content .= "     */\n";
        $content .= "    public function show({$this->modelName} \${$modelVariable})\n    {\n";
        $content .= "        return view('{$viewPath}.show', compact('{$modelVariable}'));\n";
        $content .= "    }\n\n";

        // Edit method
        $content .= "    /**\n";
        $content .= "     * Show the form for editing the specified resource.\n";
        $content .= "     *\n";
        $content .= "     * @param  {$this->modelName}  \${$modelVariable}\n";
        $content .= "     * @return \\Illuminate\\View\\View\n";
        $content .= "     */\n";
        $content .= "    public function edit({$this->modelName} \${$modelVariable})\n    {\n";
        $content .= "        return view('{$viewPath}.edit', compact('{$modelVariable}'));\n";
        $content .= "    }\n\n";

        // Update method
        $content .= "    /**\n";
        $content .= "     * Update the specified resource in storage.\n";
        $content .= "     *\n";
        $content .= "     * @param  \\Illuminate\\Http\\Request  \$request\n";
        $content .= "     * @param  {$this->modelName}  \${$modelVariable}\n";
        $content .= "     * @return \\Illuminate\\Http\\RedirectResponse\n";
        $content .= "     */\n";
        $content .= "    public function update(Request \$request, {$this->modelName} \${$modelVariable})\n    {\n";
        $content .= "        \$validated = \$request->validate({$validationRules});\n\n";
        $content .= "        \${$modelVariable}->update(\$validated);\n\n";
        $content .= "        return redirect()->route('{$this->resourceName}.index')\n";
        $content .= "            ->with('success', '{$this->modelName} updated successfully.');\n";
        $content .= "    }\n\n";

        // Destroy method
        $content .= "    /**\n";
        $content .= "     * Remove the specified resource from storage.\n";
        $content .= "     *\n";
        $content .= "     * @param  {$this->modelName}  \${$modelVariable}\n";
        $content .= "     * @return \\Illuminate\\Http\\RedirectResponse\n";
        $content .= "     */\n";
        $content .= "    public function destroy({$this->modelName} \${$modelVariable})\n    {\n";
        $content .= "        \${$modelVariable}->delete();\n\n";
        $content .= "        return redirect()->route('{$this->resourceName}.index')\n";
        $content .= "            ->with('success', '{$this->modelName} deleted successfully.');\n";
        $content .= "    }\n";

        // Add custom methods
        if (!empty($this->methods)) {
            $content .= "\n";
            foreach ($this->methods as $method) {
                $content .= $this->formatMethod($method);
            }
        }

        $content .= "}\n";

        return [
            'name' => $controllerName,
            'namespace' => $this->controllerNamespace,
            'content' => $content,
            'file_path' => "app/Http/Controllers/{$controllerName}.php",
            'model' => [
                'name' => $this->modelName,
                'namespace' => $this->modelNamespace,
                'variable' => $modelVariable
            ],
            'resource' => [
                'name' => $this->resourceName,
                'view_path' => $viewPath
            ]
        ];
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

    /**
     * Format a custom method for the controller
     *
     * @param array $method
     * @return string
     */
    protected function formatMethod(array $method): string
    {
        $content = "    /**\n";
        if (isset($method['description'])) {
            $content .= "     * {$method['description']}\n";
            $content .= "     *\n";
        }

        // Add parameter documentation
        if (isset($method['parameters'])) {
            foreach ($method['parameters'] as $param) {
                $content .= "     * @param  {$param['type']}  \${$param['name']}";
                if (isset($param['description'])) {
                    $content .= "  {$param['description']}";
                }
                $content .= "\n";
            }
            $content .= "     *\n";
        }

        // Add return documentation
        if (isset($method['return'])) {
            $content .= "     * @return {$method['return']}\n";
        }
        $content .= "     */\n";

        // Method signature
        $content .= "    public function {$method['name']}(";
        if (isset($method['parameters'])) {
            $params = [];
            foreach ($method['parameters'] as $param) {
                $params[] = "{$param['type']} \${$param['name']}";
            }
            $content .= implode(', ', $params);
        }
        $content .= ")\n    {\n";

        // Method body
        if (isset($method['body'])) {
            $content .= "        {$method['body']}\n";
        }

        $content .= "    }\n\n";

        return $content;
    }
} 