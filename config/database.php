<?php

use Illuminate\Database\Capsule\Manager;

$manager = new Manager;

$manager->addConnection([
    'driver' => 'mysql',
    'host' => getenv('DB_HOST'),
    'database' => getenv('DB_NAME'),
    'username' => getenv('DB_USER'),
    'password' => getenv('DB_PASSWORD')
]);

$manager->setAsGlobal();
$manager->bootEloquent();
