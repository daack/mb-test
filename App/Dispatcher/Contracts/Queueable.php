<?php

namespace App\Dispatcher\Contracts;

interface Queueable {
    public function handle($data);
}
