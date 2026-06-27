<?php

class Router
{
    protected $routes = [];

    public function get($uri, $controller, $method)
    {
        $this->addRoute('GET', $uri, $controller, $method);
    }

    public function post($uri, $controller, $method)
    {
        $this->addRoute('POST', $uri, $controller, $method);
    }

    protected function addRoute($httpMethod, $uri, $controller, $method)
    {
        $basePath = '/movie-ticket-booking/public';
        $fullUri = $basePath . $uri;
        
        $fullUri = rtrim($fullUri, '/');
        if (empty($fullUri)) {
             $fullUri = '/';
        }

        $this->routes[] = [
            'method' => $httpMethod,
            'uri' => $fullUri,
            'controller' => $controller,
            'action' => $method
        ];
    }

    public function dispatch($uri, $method)
    {
        $uri = rtrim($uri, '/');
        if (empty($uri)) {
             $uri = '/';
        }

        foreach ($this->routes as $route) {
            if ($route['method'] === $method && $route['uri'] === $uri) {
                
                $controllerFile = '';
                if (file_exists(__DIR__ . '/../app/controllers/admin/' . $route['controller'] . '.php')) {
                    $controllerFile = __DIR__ . '/../app/controllers/admin/' . $route['controller'] . '.php';
                } elseif (file_exists(__DIR__ . '/../app/controllers/user/' . $route['controller'] . '.php')) {
                    $controllerFile = __DIR__ . '/../app/controllers/user/' . $route['controller'] . '.php';
                } elseif (file_exists(__DIR__ . '/../app/controllers/' . $route['controller'] . '.php')) {
                    $controllerFile = __DIR__ . '/../app/controllers/' . $route['controller'] . '.php';
                }

                if ($controllerFile && file_exists($controllerFile)) {
                    require_once $controllerFile;
                    $controller = new $route['controller']();
                    $action = $route['action'];

                    if (method_exists($controller, $action)) {
                        $controller->$action();
                        return;
                    }
                }
            }
        }

        // 404 Not Found
        http_response_code(404);
        require_once __DIR__ . '/../app/views/errors/404.php';
    }
}
