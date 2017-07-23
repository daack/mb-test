<?php

namespace Framework;

class HttpError extends \Exception {

    private $content;

    public function __construct($content, $code = 0, \Exception $previous = null) {
        $this->content = $content;

        $message = is_array($content) ? json_encode($content) : $content;

        parent::__construct($message, $code, $previous);
    }

    public function getContent() {
        return $this->content;
    }
}
