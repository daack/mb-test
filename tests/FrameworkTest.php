<?php

require __DIR__ . '/../vendor/autoload.php';

use PHPUnit\Framework\TestCase;
use Framework\App;
use Framework\Request;
use Framework\Router;
use Framework\Replyer;
use Framework\HttpError;

/**
 * @covers Framework
 */
final class FrameworkTest extends TestCase
{
    public function testRouterInstance()
    {
        $router = new Router('POST', 'test');

        $this->assertInstanceOf(Router::class, $router);
        $this->assertFalse($router->getCurrentHandler());
    }

    public function testRouterRoute()
    {
        Router::route([
            'method' => 'GET',
            'path' => 'test',
            'handler' => function ($app, $reply) {}
        ]);

        $router = new Router('POST', 'test');

        $this->assertFalse($router->getCurrentHandler());

        $router = new Router('GET', 'test');

        $this->assertCount(1, $router->getCurrentHandler());

        Router::route([
            'method' => 'GET',
            'path' => 'test',
            'handler' => [function ($app, $reply) {}, function ($app, $reply) {}]
        ]);

        $this->assertCount(2, $router->getCurrentHandler());
    }

    public function testRouterRegex()
    {
        Router::route([
            'method' => 'GET',
            'path' => 'foo/bar/\w+',
            'handler' => 'Class'
        ]);

        $router = new Router('GET', 'foo/bar');

        $this->assertFalse($router->getCurrentHandler());

        $router = new Router('GET', 'foo/bar/1');

        $this->assertCount(1, $router->getCurrentHandler());

        $router = new Router('GET', 'foo/bar/1/bar');

        $this->assertFalse($router->getCurrentHandler());
    }

    public function testRequestInstance()
    {
        $router = new Router('GET', 'foo/bar');

        $request = new Request($router, [], [], []);

        $this->assertInstanceOf(Request::class, $request);
        $this->assertClassHasAttribute('id', Request::class);
    }

    public function testRequestBadValidate()
    {
        $this->expectException(HttpError::class);

        $router = new Router('GET', 'foo/bar');

        $request = new Request($router, [], [ 'foo' => 55 ], []);

        $request->validate('payload', [ 'foo' => 'is_string' ]);
    }

    public function testRequestGoodValidate()
    {
        $router = new Router('GET', 'foo/bar');

        $request = new Request($router, [], [ 'foo' => 'sting' ], []);

        $this->assertTrue($request->validate('payload', [ 'foo' => 'is_string' ]));
    }

    public function testAppInstance()
    {
        $router = new Router('GET', 'foo/bar');

        $request = new Request($router, [], [], []);

        $app = new App($request);

        $this->assertInstanceOf(App::class, $app);
    }

    public function testAppRunNotFound()
    {
        $this->expectException(HttpError::class);

        $router = new Router('GET', 'foo/bar');

        $request = new Request($router, [], [], []);

        $app = new App($request);

        $app->run();
    }

    public function testAppRunFound()
    {
        Router::route([
            'method' => 'GET',
            'path' => 'foo/bar',
            'handler' => function ($app, $reply) {
                return $reply();
            }
        ]);

        $router = new Router('GET', 'foo/bar');

        $request = new Request($router, [], [], []);

        $app = new App($request);

        $this->assertInstanceOf(Replyer::class, $app->run());
    }

    public function testReplyerInstance()
    {
        $reply = new Replyer([ 'foo' => 'bar' ]);

        $this->assertInstanceOf(Replyer::class, $reply);
        $this->assertArrayHasKey('Content-Type', $reply->headers);
    }

    public function testReplyerSetHeaders()
    {
        $reply = new Replyer([ 'foo' => 'bar' ]);

        $this->assertInstanceOf(Replyer::class, $reply->header('Foo', 'Bar'));
        $this->assertArrayHasKey('Foo', $reply->headers);
    }
}
