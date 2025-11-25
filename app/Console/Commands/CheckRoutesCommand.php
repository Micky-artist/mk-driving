<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Routing\Route;
use Illuminate\Routing\Router;
use Illuminate\Support\Str;
use ReflectionClass;
use ReflectionMethod;

class CheckRoutesCommand extends Command
{
    protected $signature = 'routes:check';
    protected $description = 'Check if all route controllers and methods exist';

    public function handle(Router $router)
    {
        $routes = $router->getRoutes();
        $errors = [];

        foreach ($routes as $route) {
            $action = $route->getAction();
            
            if (!isset($action['controller'])) {
                continue;
            }

            $controllerAction = $action['controller'];

            // Skip closure routes
            if ($controllerAction === 'Closure') {
                continue;
            }

            // Handle different controller action formats
            if (!str_contains($controllerAction, '@')) {
                $errors[] = [
                    'route' => $route->uri(),
                    'action' => $controllerAction,
                    'error' => 'Invalid controller action format. Expected: Controller@method',
                ];
                continue;
            }

            list($controller, $method) = explode('@', $controllerAction, 2);

            // Check if controller exists
            if (!class_exists($controller)) {
                $errors[] = [
                    'route' => $route->uri(),
                    'controller' => $controller,
                    'method' => $method,
                    'error' => 'Controller does not exist',
                ];
                continue;
            }

            // Check if method exists in controller
            $reflection = new ReflectionClass($controller);
            
            if (!$reflection->hasMethod($method)) {
                $errors[] = [
                    'route' => $route->uri(),
                    'controller' => $controller,
                    'method' => $method,
                    'error' => "Method '{$method}' does not exist on controller",
                ];
            }
        }

        if (count($errors) > 0) {
            $this->error('Route validation failed!');
            $this->table(['Route', 'Controller', 'Method', 'Error'], $errors);
            return 1; // Exit with error code
        }

        $this->info('All routes are valid!');
        return 0; // Success
    }
}
