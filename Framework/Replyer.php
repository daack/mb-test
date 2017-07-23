<?php

namespace Framework;

class Replyer {

    public $body;
    public $code;
    public $headers = [];

    public function __construct($content = '', $code = 200) {
        if (is_array($content)) {
            $this->body = json_encode($content);

            $this->headers['Content-Type'] = 'application/json';
        } else {
            $this->body = $content;
        }

        $this->code = $code;
    }

    /**
     * Set a header value for the response
     *
     * @param  string $key
     * @param  string $value
     * @return Framework\Replyer
     */
    public function header($key, $value) {
        $this->headers[$key] = $value;

        return $this;
    }

    /**
     * Sets the response
     */
    public function send() {
        http_response_code($this->code);

        foreach ($this->headers as $key => $value) {
            header($key . ': ' . $value);
        }

        echo $this->body;
    }
}
