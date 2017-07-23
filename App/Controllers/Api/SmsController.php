<?php

namespace App\Controllers\Api;

use App\Sms\Message;
use App\Dispatcher\Job;
use App\Dispatcher\Queue;

class SmsController {

    /**
     * Store endpoint
     *
     * api/v1.0/sms
     *
     * @param  Framework\App $app
     * @param  Closure $reply
     * @return Framework\Replyer|array|string
     */
    public function store($app, $reply) {
        $request = $app->getRequest();

        $request->validate('payload', [
            'recipient' => 'is_numeric',
            'originator' => function ($val) {
                return is_string($val) and strlen($val) > 0;
            },
            'message' => function ($val) {
                return is_string($val) and strlen($val) > 0;
            }
        ]);

        $payload = $request->payload;

        $message = Message::create([
            'recipient' => $payload['recipient'],
            'originator' => $payload['originator'],
            'message' => $payload['message']
        ]);

        (new Queue())->dispatch(
            new Job('App\Sms\MessageBird\Sender', $message->setBinaryMessages())
        );

        return $reply([
            'location' => '/api/v1.0/sms/' . $message->id
        ])->header('X-Request-ID', $app->getRequest()->id);
    }

    /**
     * Retrive message by id
     *
     * api/v1.0/sms/{id}
     *
     * @param  Framework\App $app
     * @param  Closure $reply
     * @return Framework\Replyer|array|string
     */
    public function show($app, $reply) {
        $request = $app->getRequest();

        $components = explode('/', $request->router->path);
        $id = array_pop($components);

        $message = Message::find($id);

        if ( ! $message)
            return $reply('Not Found', 404);

        return $reply($message->toArray())
        ->header('X-Request-ID', $app->getRequest()->id);
    }
}
