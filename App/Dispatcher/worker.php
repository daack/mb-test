<?php

require __DIR__ . '/../../vendor/autoload.php';

(new Dotenv\Dotenv(__DIR__ . '/../../'))->load();

require __DIR__ . '/../../config/database.php';

use App\Dispatcher\Queue;
use Predis\Client as Redis;

$redis = new Redis(getenv('REDIS'));
$q = new Queue();

$delay = (int) getenv('DELAY');

function getTime() {
    return round(microtime(true) * 1000);
}

// Set worker alive
$redis->set('worker', '1');

/**
 * Executes the job on the queue with a preset delay
 */

try {
    while(true) {
        // Get the time of last call
        $time = $redis->get('time');

        // Check if 'wait time' is passed
        if (!$time or (getTime() - $time) >= $delay) {
            $job = $q->pop();

            if ( ! $job)
                break;

            echo $job->class;

            (new $job->class)->handle($job->data);

            // Set the time of the last call
            $redis->set('time', getTime());
        } else {
            $wait = ($time + $delay) - getTime();

            usleep($wait <= 0 ? 0 : $wait);
        }
    }

    // Set worker offline
    $redis->set('worker', '0');
} catch(\Exception $e) {
    echo $e->getMessage();

    // Set worker alive
    $redis->set('worker', '0');
}

echo 'DONE';
