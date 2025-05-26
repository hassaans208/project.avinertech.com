<?php

namespace App\Services\RouteBuilder;

use Illuminate\Support\Str;

class RouteBuilderService
{
    protected $routes = [];
    protected $groups = [];
    protected $middleware = [];
    protected $prefix = '';
    protected $name = '';
    protected $namespace = '';

    /**
     * Add a resource route
     *
     * @param string $name
     * @param string $controller
     * @param array $options
     * @return self
     */
    public function addResource(string $name, string $controller, array $options = []): self
    {
        $this->routes[] = [
            'type' => 'resource',
            'name' => $name,
            'controller' => $controller,
            'options' => $options
        ];
        return $this;
    }

    /**
     * Add a custom route
     *
     * @param string|array $methods
     * @param string $uri
     * @param string|array $action
     * @param array $options
     * @return self
     */
    public function addRoute($methods, string $uri, $action, array $options = []): self
    {
        $this->routes[] = [
            'type' => 'custom',
            'methods' => (array) $methods,
            'uri' => $uri,
            'action' => $action,
            'options' => $options
        ];
        return $this;
    }

    /**
     * Add a route group
     *
     * @param array $attributes
     * @param callable $callback
     * @return self
     */
    public function addGroup(array $attributes, callable $callback): self
    {
        $this->groups[] = [
            'attributes' => $attributes,
            'callback' => $callback
        ];
        return $this;
    }

    /**
     * Set middleware for routes
     *
     * @param array|string $middleware
     * @return self
     */
    public function setMiddleware($middleware): self
    {
        $this->middleware = (array) $middleware;
        return $this;
    }

    /**
     * Set route prefix
     *
     * @param string $prefix
     * @return self
     */
    public function setPrefix(string $prefix): self
    {
        $this->prefix = $prefix;
        return $this;
    }

    /**
     * Set route name prefix
     *
     * @param string $name
     * @return self
     */
    public function setName(string $name): self
    {
        $this->name = $name;
        return $this;
    }

    /**
     * Set controller namespace
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
     * Generate the routes content
     *
     * @return array
     */
    public function generate(): array
    {
        $content = "<?php\n\n";
        $content .= "use Illuminate\\Support\\Facades\\Route;\n\n";

        // Add any custom use statements for controllers
        $usedControllers = $this->getUsedControllers();
        foreach ($usedControllers as $controller) {
            $content .= "use {$controller};\n";
        }
        $content .= "\n";

        // Generate route groups
        foreach ($this->groups as $group) {
            $content .= $this->formatGroup($group);
        }

        // Generate individual routes
        foreach ($this->routes as $route) {
            if ($route['type'] === 'resource') {
                $content .= $this->formatResourceRoute($route);
            } else {
                $content .= $this->formatCustomRoute($route);
            }
        }

        return [
            'content' => $content,
            'file_path' => 'routes/web.php',
            'routes' => $this->getRouteList(),
            'groups' => $this->getGroupList()
        ];
    }

    /**
     * Format a route group
     *
     * @param array $group
     * @return string
     */
    protected function formatGroup(array $group): string
    {
        $content = "Route::group([\n";
        
        $attributes = [];
        if (!empty($this->middleware)) {
            $attributes[] = "    'middleware' => " . $this->formatArray($this->middleware);
        }
        if ($this->prefix) {
            $attributes[] = "    'prefix' => '{$this->prefix}'";
        }
        if ($this->name) {
            $attributes[] = "    'as' => '{$this->name}.'";
        }
        if ($this->namespace) {
            $attributes[] = "    'namespace' => '{$this->namespace}'";
        }

        // Add custom group attributes
        foreach ($group['attributes'] as $key => $value) {
            if (!in_array($key, ['middleware', 'prefix', 'as', 'namespace'])) {
                $attributes[] = "    '{$key}' => " . $this->formatValue($value);
            }
        }

        $content .= implode(",\n", $attributes);
        $content .= "\n], function () {\n";

        // Generate routes within the group
        $callback = $group['callback'];
        $builder = new self();
        $callback($builder);
        $groupContent = $builder->generate()['content'];
        
        // Indent the group content
        $groupContent = preg_replace('/^/m', '    ', $groupContent);
        $content .= $groupContent;

        $content .= "});\n\n";
        return $content;
    }

    /**
     * Format a resource route
     *
     * @param array $route
     * @return string
     */
    protected function formatResourceRoute(array $route): string
    {
        $content = "Route::resource('{$route['name']}', {$this->formatController($route['controller'])}";
        
        if (!empty($route['options'])) {
            $options = [];
            foreach ($route['options'] as $key => $value) {
                $options[] = "    '{$key}' => " . $this->formatValue($value);
            }
            $content .= ", [\n" . implode(",\n", $options) . "\n]";
        }
        
        $content .= ");\n\n";
        return $content;
    }

    /**
     * Format a custom route
     *
     * @param array $route
     * @return string
     */
    protected function formatCustomRoute(array $route): string
    {
        $methods = array_map('strtoupper', $route['methods']);
        $content = "Route::" . strtolower(implode('|', $methods)) . "('{$route['uri']}', ";
        
        if (is_array($route['action'])) {
            $content .= $this->formatController($route['action'][0]) . "@{$route['action'][1]}";
        } else {
            $content .= $route['action'];
        }

        if (!empty($route['options'])) {
            $options = [];
            foreach ($route['options'] as $key => $value) {
                $options[] = "    '{$key}' => " . $this->formatValue($value);
            }
            $content .= ")->" . implode(')->', array_map(function($key, $value) {
                return "{$key}(" . $this->formatValue($value) . ")";
            }, array_keys($route['options']), $route['options']));
        }
        
        $content .= ";\n\n";
        return $content;
    }

    /**
     * Format a controller reference
     *
     * @param string $controller
     * @return string
     */
    protected function formatController(string $controller): string
    {
        if (Str::startsWith($controller, '\\')) {
            return substr($controller, 1);
        }
        return $controller;
    }

    /**
     * Format an array for route definitions
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
            if (is_numeric($key)) {
                $items[] = "        '{$value}'";
            } else {
                $items[] = "        '{$key}' => " . $this->formatValue($value);
            }
        }

        return "[\n" . implode(",\n", $items) . "\n    ]";
    }

    /**
     * Format a value for route definitions
     *
     * @param mixed $value
     * @return string
     */
    protected function formatValue($value): string
    {
        if (is_array($value)) {
            return $this->formatArray($value);
        }
        if (is_string($value)) {
            return "'{$value}'";
        }
        if (is_bool($value)) {
            return $value ? 'true' : 'false';
        }
        if (is_null($value)) {
            return 'null';
        }
        return (string) $value;
    }

    /**
     * Get list of used controllers
     *
     * @return array
     */
    protected function getUsedControllers(): array
    {
        $controllers = [];
        foreach ($this->routes as $route) {
            if ($route['type'] === 'resource') {
                $controllers[] = $route['controller'];
            } elseif ($route['type'] === 'custom' && is_array($route['action'])) {
                $controllers[] = $route['action'][0];
            }
        }
        return array_unique($controllers);
    }

    /**
     * Get list of defined routes
     *
     * @return array
     */
    protected function getRouteList(): array
    {
        $list = [];
        foreach ($this->routes as $route) {
            if ($route['type'] === 'resource') {
                $list[] = [
                    'type' => 'resource',
                    'name' => $route['name'],
                    'controller' => $route['controller'],
                    'uri' => $route['name'],
                    'methods' => ['GET', 'POST', 'PUT', 'PATCH', 'DELETE']
                ];
            } else {
                $list[] = [
                    'type' => 'custom',
                    'methods' => $route['methods'],
                    'uri' => $route['uri'],
                    'action' => $route['action']
                ];
            }
        }
        return $list;
    }

    /**
     * Get list of defined groups
     *
     * @return array
     */
    protected function getGroupList(): array
    {
        return array_map(function($group) {
            return [
                'attributes' => $group['attributes'],
                'routes' => (new self())->setMiddleware($this->middleware)
                    ->setPrefix($this->prefix)
                    ->setName($this->name)
                    ->setNamespace($this->namespace)
                    ->getRouteList()
            ];
        }, $this->groups);
    }
} 