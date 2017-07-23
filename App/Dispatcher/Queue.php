<?php

namespace App\Dispatcher;

use Predis\Client as Redis;

class Queue {

    private $queue = 'jobs';
    private $client;
    private $worker;

    public function __construct($client = null, $worker = true) {
        $this->client = $client ?: new Redis(getenv('REDIS'));

        $this->worker = $worker;
    }

    /**
     * Check if worker is alive
     * If the worker is not alive it will be started
     */
    private function checkWorker() {
        if ( ! $this->client->get('worker'))
            exec('php ' . __DIR__ . '/worker.php > /dev/null &');
    }

    /**
     * Puts another job into the queue
     *
     * @param  Job    $job
     */
    public function dispatch(Job $job) {
        $payload = $job->getRawData();

        foreach ($payload as $data) {
            $this->client->rpush($this->queue, json_encode($data));
        }

        if ($this->worker)
            $this->checkWorker();

        return true;
    }

    /**
     * Gets the first element on the queue
     *
     * @return mixed
     */
    public function pop() {
        $data = $this->client->lpop($this->queue);

        if ( ! $data)
            return null;

        try {
            return json_decode($data);
        } catch (\Exception $e) {
            return null;
        }
    }
}
