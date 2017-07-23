<?php

require __DIR__ . '/vendor/autoload.php';

(new Dotenv\Dotenv(__DIR__))->load();

require __DIR__ . '/config/database.php';
include __DIR__ . '/routes.php';

use Framework\App;
use Framework\Request;
use Framework\Router;
use Framework\Replyer;

try {
    $parsed = parse_url($_SERVER['REQUEST_URI']);
    $method = $_SERVER['REQUEST_METHOD'];

    $path = trim($parsed['path'], '/') ?: '/';
    $query = [];

    if ($parsed['query']) {
        parse_str($parsed['query'], $query);
    }

    $request = new Request(
        new Router($method, $path),
        $query,
        json_decode(file_get_contents('php://input'), true),
        getallheaders()
    );

    $app = new App($request);

    $response = $app->run();

    $response->send();
} catch(\Exception $e) {
    $code = 500;
    $message = $e->getMessage();

    if ($e instanceof \Framework\HttpError) {
        $code =  $e->getCode();
        $message = $e->getContent();
    }

    (new Replyer($message, $code))->send();
}
