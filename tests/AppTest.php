<?php

require __DIR__ . '/../vendor/autoload.php';

use Mockery;
use PHPUnit\Framework\TestCase;
use Illuminate\Database\Eloquent\Model as Eloquent;
use App\Sms\Message;
use Predis\Client as Redis;
use App\Dispatcher\Queue;
use App\Dispatcher\Job;

/**
 * @covers Framework
 */
final class AppTest extends TestCase
{
    public function testMessageModel()
    {
        $this->assertInstanceOf(Eloquent::class, new Message());
    }

    public function testMessageBinary()
    {
        $message = new Message([
            'recipient' => 5557686996,
            'originator' => 'Test',
            'message' => 'message'
        ]);

        $bin = $message->setBinaryMessages(false);

        $this->assertCount(1, $bin);

        $this->assertArrayHasKey('id', $bin[0]);
        $this->assertArrayHasKey('recipient', $bin[0]);
        $this->assertArrayHasKey('originator', $bin[0]);
        $this->assertArrayHasKey('body', $bin[0]);
        $this->assertArrayHasKey('udh', $bin[0]);
    }

    public function testJobInterface()
    {
        $job = new Job('Class', ['one']);

        $this->assertCount(1, $job->getRawData());
    }

    public function testQueueInstance()
    {
        $double = Mockery::mock(Redis::class);

        $queue = new Queue($double, false);

        $this->assertInstanceOf(Queue::class, $queue);
    }

    public function testQueueDispatch()
    {
        $double = Mockery::mock(Redis::class);

        $queue = new Queue($double, false);

        $double->shouldReceive('rpush')->once();

        $this->assertTrue($queue->dispatch(
            new Job('App\Sms\MessageBird\Sender', [[ 'foo' => 'bar' ]])
        ));
    }
}
