<?php

namespace Framework;

use Framework\Request;
use Framework\HttpError;
use Framework\Replyer;

class App {

    private $request;

    public function __construct(Request $request) {
        $this->request = $request;
    }

    /**
     * It gets and execute the handler for the current request
     *
     * @return Framework\Replyer|Framework\HttpError
     */
    public function run() {
        $handler = $this->request->router->getCurrentHandler();

        if ( ! $handler)
            throw new HttpError('Not Found', 404);

        $reply = function ($content = '', $code = 200) {
            return new Replyer($content, $code);
        };

        foreach ($handler as $middleware) {
            if (is_string($middleware)) {
                list($class, $method) = explode("::", $middleware);

                $replyer = (new $class)->{$method}($this, $reply);
            } else {
                $replyer = $middleware($this, $reply);
            }
        }

        if ( ! ($replyer instanceof Replyer))
            return $reply($replyer);

        return $replyer;
    }

    /**
     * Current request
     *
     * @return Framework\Request
     */
    public function getRequest() {
        return $this->request;
    }
}
