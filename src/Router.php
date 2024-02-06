<?php
namespace App;
class Router {
    private $routeConfigurationFile = "../config/routes.yaml";
    private $routing = null;
    public function __construct($routeConfigurationFile = null) { 
        $this->readRouteConfiguration($routeConfigurationFile);
        //$this->start();
        $this->run();
    }
    public function readRouteConfiguration($routeConfigurationFile) {
        if (!is_null($routeConfigurationFile)) {
            $this->routeConfigurationFile = $routeConfigurationFile;
        }
        if (file_exists($this->routeConfigurationFile)) {
            $this->routing = yaml_parse_file($this->routeConfigurationFile);
        } else {
            throw new \Exception("Route Configuration file not found.", 500);
        }

    }
    public function run($uri = null, $method = null, $messages = []) {
        
        /* get parameters and body content from request */
        $requestUri = parse_url($uri ?? $_SERVER['REQUEST_URI']);
        $requestMethod = $method ?? $_SERVER['REQUEST_METHOD'];
        $requestPath = $requestUri['path'];
        parse_str($requestUri['query'] ?? '', $requestQuery);

        /* iterate trough defined routes */
        foreach($this->routing as $route => $config) {
            /* create a list of request methods */
            $methods = explode(",",$config['method'] ?? 'PUT,GET,POST,DELETE,PATCH,HEAD,OPTIONS');
            /* compare routing parameters with user request */
            if (!in_array($requestMethod, $methods)) {
                continue;
            }

            $match = $config['match'] ?? null;
            $matches = false;
            if ($match) {
                /* if a "match" directive is present, perform a regexp match */
                preg_match_all($match, $requestPath, $matches, PREG_SET_ORDER);
                if ($matches && is_array($config['match_params'] ?? null)) {
                    foreach($config['match_params'] as $key => $param) {
                        $requestQuery[$param] = $matches[0][$key];
                    }
                }
            }

            if (!$matches) {
                /* gather all aliases for this route */
                $aliases = array_filter(array_merge([$route], is_string($config['alias'] ?? '') ? [$config['alias'] ?? ''] : ($config['alias'] ?? [])));

                if (!in_array($requestPath, $aliases) && !in_array(substr($requestPath, 1), $aliases)) {
                    continue;
                }
            }

                
            /* Instantiate Controller specified in the route */
            $controllerClassName = "\App\Controller\\".($config['controller'] ?? 'DefaultController');
            $controller = new $controllerClassName();
            $controller->setMessages($messages);
            /* provide request query to controller */
            $controller->setQuery($requestQuery);
            $actionName = $config['action'] ?? 'index';
            $exception = null;
            try {
                /* call the specified action */
                $result = $controller->$actionName();
            } catch (\Exception $exception) {
                $controller->addMessage($exception->getMessage());
                http_response_code($exception->getCode());
                $controller->display('error.html');
                die();
            }
            /* redirect to another route if specified while keeping current message stack */
            if (($config['redirect'] ?? '')) {
                if (is_array($config['redirect']) && $config['redirect'][$result] ?? '') {
                    $this->run($config['redirect'][$result], 'GET', $controller->getMessages());
                } else {
                    $this->run($config['redirect'], 'GET', $controller->getMessages());
                }
            }
        }
    }
}