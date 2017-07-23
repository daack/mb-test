<?php

namespace Framework;

class Router {

    private static $routes = [];
    public $method;
    public $path;

    public function __construct($method, $path) {
        $this->method = strtolower($method);
        $this->path = strtolower($path);
    }

    /**
     * Retrive the handler for the currect request
     *
     * @return array|boolean
     */
    public function getCurrentHandler() {
        try {
            $paths = self::$routes[$this->method];

            if ( ! $paths)
                return false;

            foreach ($paths as $path => $handler) {
                $regex = '/' . preg_replace('/\\//', '\/', $path) . '/';

                $matches = [];

                preg_match($regex , $this->path, $matches);

                if (count($matches) and $matches[0] == $this->path)
                    return $handler;
            }
        } catch (\Exception $e) {
            //
        }

        return false;
    }

    /**
     * Sets a new route configuration
     *
     * @param  array $config
     */
    public static function route($config) {
        extract($config);

        $method = isset($method) ? strtolower($method) : 'get';

        if ( ! self::$routes[$method])
            self::$routes[$method] = [];

        $handler = is_array($handler) ? $handler : [$handler];

        self::$routes[$method][$path] = $handler;
    }
}
