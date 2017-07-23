<?php

namespace App\Dispatcher;

class Job {

    private $class;
    private $data;

    public function __construct($class, $data) {
        $this->class = $class;
        $this->data = $data;
    }

    /**
     * Formats the data in order to be put into the queue
     *
     * @return array
     */
    public function getRawData() {
        $raw = [];

        foreach ($this->data as $data) {
            array_push($raw, [
                'class' => $this->class,
                'data' => $data
            ]);
        }

        return $raw;
    }
}
