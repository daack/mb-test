<?php

namespace App\Sms;

use Illuminate\Database\Eloquent\Model as Eloquent;

class Message extends Eloquent {

    public $fillable = [
        'recipient',
        'originator',
        'message'
    ];

    /**
     * Format the message in order to be put into the queue
     *
     * @return array
     */
    public function setBinaryMessages($update = true)
    {
        $hex_message = unpack('H*', $this->message)[1];
        $splitted = str_split($hex_message, 160);
        $this->chunks = count($splitted);
        $this->udh_uid = formatHex(dechex(rand(0, 255)));

        if ($update)
            $this->save();

        $bin = [];

        $udh_total = formatHex((string) $this->chunks);

        $i = 0;

        foreach ($splitted as $chunk) {
            $i++;

            array_push($bin, [
                'id' => $this->id,
                'recipient' => $this->recipient,
                'originator' => $this->originator,
                'body' => $chunk,
                'udh' => '050003' . $this->udh_uid . $udh_total . formatHex($i)
            ]);
        }

        return $bin;
    }
}

function formatHex($data) {
    $data = strtoupper((string) $data);

    return strlen($data) == 1 ? '0' . $data : $data;
}
