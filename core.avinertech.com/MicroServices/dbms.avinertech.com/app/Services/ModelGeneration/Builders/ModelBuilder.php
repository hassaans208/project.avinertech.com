<?php

namespace App\Services\ModelGeneration\Builders;

use App\Services\ModelGeneration\Contracts\ModelBuilderInterface;
use Illuminate\Support\Str;

class ModelBuilder implements ModelBuilderInterface
{
    protected string $name;
    protected string $namespace = 'App\\Models';
    protected array $traits = [];
    protected array $uses = [];
    protected array $properties = [];
    protected array $methods = [];
    protected array $relationships = [];
    protected array $validationRules = [];
    protected array $casts = [];
    protected array $fillable = [];
    protected array $guarded = ['*'];
    protected array $hidden = [];
    protected array $appends = [];

    /**
     * Set the model name
     *
     * @param string $name
     * @return self
     */
    public function setName(string $name): self
    {
        $this->name = Str::studly($name);
        return $this;
    }

    /**
     * Set the model namespace
     *
     * @param string $namespace
     * @return self
     */
    public function setNamespace(string $namespace): self
    {
        $this->namespace = $namespace;
        return $this;
    }

    /**
     * Add a trait to the model
     *
     * @param string $trait
     * @return self
     */
    public function addTrait(string $trait): self
    {
        if (!in_array($trait, $this->traits)) {
            $this->traits[] = $trait;
        }
        return $this;
    }

    /**
     * Add a use statement
     *
     * @param string $class
     * @return self
     */
    public function addUse(string $class): self
    {
        if (!in_array($class, $this->uses)) {
            $this->uses[] = $class;
        }
        return $this;
    }

    /**
     * Add a property to the model
     *
     * @param string $name
     * @param mixed $value
     * @param string $visibility
     * @return self
     */
    public function addProperty(string $name, $value, string $visibility = 'protected'): self
    {
        $this->properties[] = [
            'name' => $name,
            'value' => $value,
            'visibility' => $visibility
        ];
        return $this;
    }

    /**
     * Add a method to the model
     *
     * @param string $name
     * @param array $parameters
     * @param string $body
     * @param string $visibility
     * @param string $returnType
     * @return self
     */
    public function addMethod(
        string $name,
        array $parameters,
        string $body,
        string $visibility = 'public',
        string $returnType = 'void'
    ): self {
        $this->methods[] = [
            'name' => $name,
            'parameters' => $parameters,
            'body' => $body,
            'visibility' => $visibility,
            'returnType' => $returnType
        ];
        return $this;
    }

    /**
     * Add a relationship method
     *
     * @param string $name
     * @param string $type
     * @param string $relatedModel
     * @param string|null $foreignKey
     * @param string|null $localKey
     * @return self
     */
    public function addRelationship(
        string $name,
        string $type,
        string $relatedModel,
        ?string $foreignKey = null,
        ?string $localKey = null
    ): self {
        $this->relationships[] = [
            'name' => $name,
            'type' => $type,
            'relatedModel' => $relatedModel,
            'foreignKey' => $foreignKey,
            'localKey' => $localKey
        ];
        return $this;
    }

    /**
     * Add validation rules
     *
     * @param array $rules
     * @return self
     */
    public function addValidationRules(array $rules): self
    {
        $this->validationRules = array_merge($this->validationRules, $rules);
        return $this;
    }

    /**
     * Add attribute casts
     *
     * @param array $casts
     * @return self
     */
    public function addCasts(array $casts): self
    {
        $this->casts = array_merge($this->casts, $casts);
        return $this;
    }

    /**
     * Add fillable attributes
     *
     * @param array $attributes
     * @return self
     */
    public function addFillable(array $attributes): self
    {
        $this->fillable = array_merge($this->fillable, $attributes);
        return $this;
    }

    /**
     * Add guarded attributes
     *
     * @param array $attributes
     * @return self
     */
    public function addGuarded(array $attributes): self
    {
        $this->guarded = array_merge($this->guarded, $attributes);
        return $this;
    }

    /**
     * Add hidden attributes
     *
     * @param array $attributes
     * @return self
     */
    public function addHidden(array $attributes): self
    {
        $this->hidden = array_merge($this->hidden, $attributes);
        return $this;
    }

    /**
     * Add appends attributes
     *
     * @param array $attributes
     * @return self
     */
    public function addAppends(array $attributes): self
    {
        $this->appends = array_merge($this->appends, $attributes);
        return $this;
    }

    /**
     * Build the model class
     *
     * @return string
     */
    public function build(): string
    {
        $content = "<?php\n\n";
        $content .= "namespace {$this->namespace};\n\n";

        // Add use statements
        foreach ($this->uses as $use) {
            $content .= "use {$use};\n";
        }
        $content .= "\n";

        // Add class definition
        $content .= "class {$this->name} extends Model\n{\n";

        // Add traits
        if (!empty($this->traits)) {
            foreach ($this->traits as $trait) {
                $content .= "    use {$trait};\n";
            }
            $content .= "\n";
        }

        // Add properties
        foreach ($this->properties as $property) {
            $value = is_string($property['value']) ? "'{$property['value']}'" : $property['value'];
            $content .= "    {$property['visibility']} \${$property['name']} = {$value};\n";
        }
        if (!empty($this->properties)) {
            $content .= "\n";
        }

        // Add fillable
        if (!empty($this->fillable)) {
            $content .= "    protected \$fillable = " . $this->formatArray($this->fillable) . ";\n\n";
        }

        // Add guarded
        if (!empty($this->guarded)) {
            $content .= "    protected \$guarded = " . $this->formatArray($this->guarded) . ";\n\n";
        }

        // Add hidden
        if (!empty($this->hidden)) {
            $content .= "    protected \$hidden = " . $this->formatArray($this->hidden) . ";\n\n";
        }

        // Add appends
        if (!empty($this->appends)) {
            $content .= "    protected \$appends = " . $this->formatArray($this->appends) . ";\n\n";
        }

        // Add casts
        if (!empty($this->casts)) {
            $content .= "    protected \$casts = " . $this->formatArray($this->casts) . ";\n\n";
        }

        // Add validation rules
        if (!empty($this->validationRules)) {
            $content .= "    public static function rules(): array\n    {\n";
            $content .= "        return " . $this->formatArray($this->validationRules, true) . ";\n";
            $content .= "    }\n\n";
        }

        // Add relationships
        foreach ($this->relationships as $relationship) {
            $content .= $this->buildRelationshipMethod($relationship);
        }

        // Add custom methods
        foreach ($this->methods as $method) {
            $content .= $this->buildMethod($method);
        }

        $content .= "}\n";

        return $content;
    }

    /**
     * Format an array for PHP code
     *
     * @param array $array
     * @param bool $multiline
     * @return string
     */
    protected function formatArray(array $array, bool $multiline = false): string
    {
        if (empty($array)) {
            return '[]';
        }

        if (!$multiline) {
            $items = [];
            foreach ($array as $key => $value) {
                if (is_string($key)) {
                    $items[] = "'{$key}' => " . (is_string($value) ? "'{$value}'" : $value);
                } else {
                    $items[] = is_string($value) ? "'{$value}'" : $value;
                }
            }
            return '[' . implode(', ', $items) . ']';
        }

        $content = "[\n";
        foreach ($array as $key => $value) {
            $content .= "            ";
            if (is_string($key)) {
                $content .= "'{$key}' => " . (is_string($value) ? "'{$value}'" : $value);
            } else {
                $content .= is_string($value) ? "'{$value}'" : $value;
            }
            $content .= ",\n";
        }
        $content .= "        ]";
        return $content;
    }

    /**
     * Build a relationship method
     *
     * @param array $relationship
     * @return string
     */
    protected function buildRelationshipMethod(array $relationship): string
    {
        $method = "    public function {$relationship['name']}()\n    {\n";
        $method .= "        return \$this->{$relationship['type']}(";
        
        $args = ["{$relationship['relatedModel']}::class"];
        if ($relationship['foreignKey']) {
            $args[] = "'{$relationship['foreignKey']}'";
        }
        if ($relationship['localKey']) {
            $args[] = "'{$relationship['localKey']}'";
        }
        
        $method .= implode(', ', $args);
        $method .= ");\n    }\n\n";
        
        return $method;
    }

    /**
     * Build a method
     *
     * @param array $method
     * @return string
     */
    protected function buildMethod(array $method): string
    {
        $content = "    {$method['visibility']} function {$method['name']}(";
        
        // Add parameters
        $params = [];
        foreach ($method['parameters'] as $param) {
            $paramStr = '';
            if (isset($param['type'])) {
                $paramStr .= "{$param['type']} ";
            }
            $paramStr .= "\${$param['name']}";
            if (isset($param['default'])) {
                $paramStr .= " = " . (is_string($param['default']) ? "'{$param['default']}'" : $param['default']);
            }
            $params[] = $paramStr;
        }
        $content .= implode(', ', $params);
        
        // Add return type
        if ($method['returnType'] !== 'void') {
            $content .= "): {$method['returnType']}";
        }
        
        $content .= "\n    {\n";
        $content .= "        {$method['body']}\n";
        $content .= "    }\n\n";
        
        return $content;
    }
} 