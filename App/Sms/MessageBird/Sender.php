<?php

namespace App\Sms\MessageBird;

use App\Dispatcher\Contracts\Queueable;
use App\Sms\Message;
use MessageBird\Client;
use MessageBird\Objects\Message as Sms;

class Sender implements Queueable {

    public $client;

    public function __construct($client = null) {
        $this->client = $client ?: new Client(getenv('KEY'));
    }

    /**
     * Send the sms to Message Bird
     *
     * @param  stdClass $data
     */
    public function handle($data) {
        $message = Message::find($data->id);

        if ( ! $message)
            return null;

        $sms = new Sms();

        $sms->originator = $data->originator;
        $sms->recipients = array($data->recipient);

        $sms->setBinarySms($data->udh, $data->body);

        try {
            $this->client->messages->create($sms);

            $message->sent = $message->sent + 1;

            $message->save();
        } catch(\Exception $e) {
            //LOG ERRORS
        }
    }
}
