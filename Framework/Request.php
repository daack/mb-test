<?php

namespace Framework;

use Framework\HttpError;

class Request {

    public $router;
    public $id;

    public $headers = [];
    public $query = [];
    public $payload = [];

    public function __construct(Router $router, $query, $payload, $headers) {
        $this->router = $router;
        $this->query = $query;
        $this->payload = $payload;
        $this->headers = $headers;

        $this->id = uniqid();
    }

    /**
     * Validates input for payload or query
     *
     * @param  string $type    payload|query
     * @param  array $compare  [ field => func ]
     */
    public function validate($type, $compare) {
        $data = $this->$type;

        foreach ($compare as $key => $fn) {
            if ( ! isset($data[$key]))
                throw new HttpError([ 'error' => 'missing field', 'field' => $key ], 400);

            if ( ! call_user_func($fn, $data[$key]))
                throw new HttpError([ 'error' => 'field not valid', 'field' => $key ], 400);
        }

        return true;
    }
}
