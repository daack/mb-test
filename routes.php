<?php

use Framework\Router;

Router::route([
    'method' => 'POST',
    'path' => 'api/v1.0/sms',
    'handler' => 'App\Controllers\Api\SmsController::store'
]);

Router::route([
    'method' => 'GET',
    'path' => 'api/v1.0/sms/\w+',
    'handler' => 'App\Controllers\Api\SmsController::show'
]);
