<?php
/** @noinspection EfferentObjectCouplingInspection */
declare(strict_types = 1);


namespace SmartWeb\Nats\Tests\Connection;

use Google\Protobuf\Internal\Message;
use Mockery as m;
use NatsStreaming\Connection;
use NatsStreaming\Msg;
use NatsStreaming\Subscription;
use NatsStreaming\SubscriptionOptions;
use NatsStreaming\TrackedNatsRequest;
use NatsStreamingProtos\StartPosition;
use PHPUnit\Framework\TestCase;
use Protobuf\Stream;
use SmartWeb\Events\EventInterface;
use SmartWeb\Events\Generator\FixedEventIdGenerator;
use SmartWeb\Events\Generator\FixedEventTimeGenerator;
use SmartWeb\Events\Support\EventTime;
use SmartWeb\Nats\Connection\StreamingConnection;
use SmartWeb\Nats\Error\InvalidEventException;
use SmartWeb\Nats\Error\RequestFailedException;
use SmartWeb\Nats\Event\Factory\ResponseInfoResolver;
use SmartWeb\Nats\Event\Factory\ResponseInfoResolverInterface;
use SmartWeb\Nats\Message\DeserializerInterface;
use SmartWeb\Nats\Subscriber\MessageInitializerInterface;
use SmartWeb\Nats\Tests\Fixtures\DummySubscriber;
use SmartWeb\Nats\Tests\Fixtures\DummySubscriberUsesProtobufAny;

/**
 * @coversDefaultClass \SmartWeb\Nats\Connection\StreamingConnection
 *
 * @author Nicolai Agersbæk <na@zitcom.dk>
 */
class StreamingConnectionTest extends TestCase
{
    
    /**
     * @var m\MockInterface|Connection
     */
    private $conn;
    
    /**
     * @var m\MockInterface|MessageInitializerInterface
     */
    private $initializer;
    
    /**
     * @var m\MockInterface|ResponseInfoResolverInterface
     */
    private $resolver;
    
    /**
     * @var StreamingConnection
     */
    private $connection;
    
    /**
     * @var m\MockInterface|DeserializerInterface
     */
    private $deserializer;
    
    /**
     * @var FixedEventIdGenerator
     */
    private $idGen;
    
    /**
     * @var FixedEventTimeGenerator
     */
    private $timeGen;
    
    protected function setUp()
    {
        parent::setUp();
        
        $this->conn = m::mock(Connection::class);
        
        $this->deserializer = m::mock(DeserializerInterface::class);
        
        $this->initializer = m::mock(MessageInitializerInterface::class);
        
        $this->idGen = $this->idGen ?? new FixedEventIdGenerator('test-event-id');
        $this->timeGen = $this->timeGen ?? new FixedEventTimeGenerator(EventTime::now());
        $this->resolver = $this->resolver ?? new ResponseInfoResolver($this->idGen, $this->timeGen);
        
        $this->connection = $this->connection ?? new StreamingConnection(
            $this->conn,
            $this->deserializer,
            $this->initializer,
            $this->resolver
        );
    }
    
    /**
     * @covers ::publish
     */
    public function testPublish() : void
    {
        $event = m::mock(EventInterface::class);
        $event->shouldReceive('getEventType')
              ->once()
              ->andReturn('test-event-type');
        
        /** @var StreamingConnection|m\MockInterface $connection */
        $connection = m::mock(StreamingConnection::class)->makePartial();
        
        $connection->shouldReceive('publish')
                   ->set('connection', $this->conn);
        
        $connection->shouldReceive('serializeEvent')
                   ->once()
                   ->with($event)
                   ->andReturn('test-serialization');
        
        $request = m::mock(TrackedNatsRequest::class);
        
        $this->conn->shouldReceive('publish')
                   ->once()
                   ->with('test-event-type', 'test-event-serialization')
                   ->andReturn($request);
        
        $this->assertEquals($request, $connection->publish($event));
    }
    
    /**
     * @covers ::serializeEvent
     */
    public function testSerializeEvent() : void
    {
        $serialization = 'foo/bar';
        $msg = m::mock(EventInterface::class, Message::class);
        $msg->shouldReceive('serializeToString')
            ->andReturn($serialization)
            ->once();
        
        $this->assertSame($serialization, $this->connection->serializeEvent($msg));
    }
    
    /**
     * @covers ::serializeEvent
     */
    public function testSerializeEventInvalid() : void
    {
        $msg = m::mock(EventInterface::class, self::class);
        
        $this->expectException(InvalidEventException::class);
        $this->connection->serializeEvent($msg);
    }
    
    /**
     * @covers ::request
     */
    public function testRequest() : void
    {
        $event = m::mock(EventInterface::class, Message::class);
        $event->shouldReceive('getEventType')
              ->once()
              ->andReturn('test-event-type');
        $event->shouldReceive('getEventId')
              ->once()
              ->andReturn($this->idGen->generate());
        $event->shouldReceive('serializeToString')
              ->once()
              ->andReturn('test-event-serialization');
        
        $subscriber = new DummySubscriber();
        
        /** @var StreamingConnection|m\MockInterface $connection */
        $connection = m::mock(
            StreamingConnection::class,
            [$this->conn, $this->deserializer, $this->initializer, $this->resolver]
        )
                       ->makePartial()
                       ->shouldAllowMockingProtectedMethods();
        
        // Subscription options for request
        $subOptions = new SubscriptionOptions();
        $subOptions->setStartAt(StartPosition::NewOnly());
        $subOptions->setAckWaitSecs(5);
        $subOptions->setManualAck(true);
        
        $connection->shouldReceive('getSubOptionsForRequest')
                   ->once()
                   ->andReturn($subOptions);
        
        // 'subscribe' expectations
        $subscription = m::mock(Subscription::class);
        
        $connection->shouldReceive('subscribe')
                   ->once()
                   ->with('test-response-channel', $subscriber, $subOptions)
                   ->andReturn($subscription);
        
        $this->conn->shouldReceive('subscribe')
                   ->once()
                   ->andReturn($subscription);
        
        // 'publish' expectations
        $request = m::mock(TrackedNatsRequest::class);
        
        $connection->shouldReceive('publish')
                   ->once()
                   ->with($event)
                   ->andReturn($request);
        
        $this->conn->shouldReceive('publish')
                   ->once()
                   ->with('test-event-type', 'test-event-serialization')
                   ->andReturn($request);
        
        $request->shouldReceive('wait')
                ->once()
                ->andReturn(true);
        
        $subscription->shouldReceive('wait')
                     ->once()
                     ->with(1);
        
        $connection->request($event, $subscriber);
        
        $this->expectNotToPerformAssertions();
    }
    
    /**
     * @covers ::request
     */
    public function testRequestThrows() : void
    {
        $event = m::mock(EventInterface::class, Message::class);
        $event->shouldReceive('getEventType')
              ->once()
              ->andReturn('test-event-type');
        $event->shouldReceive('getEventId')
              ->once()
              ->andReturn($this->idGen->generate());
        $event->shouldReceive('serializeToString')
              ->once()
              ->andReturn('test-event-serialization');
        
        $subscriber = new DummySubscriber();
        
        /** @var StreamingConnection|m\MockInterface $connection */
        $connection = m::mock(
            StreamingConnection::class,
            [$this->conn, $this->deserializer, $this->initializer, $this->resolver]
        )
                       ->makePartial()
                       ->shouldAllowMockingProtectedMethods();
        
        // Subscription options for request
        $subOptions = new SubscriptionOptions();
        $subOptions->setStartAt(StartPosition::NewOnly());
        $subOptions->setAckWaitSecs(5);
        $subOptions->setManualAck(true);
        
        $connection->shouldReceive('getSubOptionsForRequest')
                   ->once()
                   ->andReturn($subOptions);
        
        // 'subscribe' expectations
        $subscription = m::mock(Subscription::class);
        
        $connection->shouldReceive('subscribe')
                   ->once()
                   ->with('test-response-channel', $subscriber, $subOptions)
                   ->andReturn($subscription);
        
        $this->conn->shouldReceive('subscribe')
                   ->once()
                   ->andReturn($subscription);
        
        // 'publish' expectations
        $request = m::mock(TrackedNatsRequest::class);
        
        $connection->shouldReceive('publish')
                   ->once()
                   ->with($event)
                   ->andReturn($request);
        
        $this->conn->shouldReceive('publish')
                   ->once()
                   ->with('test-event-type', 'test-event-serialization')
                   ->andReturn($request);
        
        $request->shouldReceive('wait')
                ->once()
                ->andReturn(false);
        
        // failure expectations
        $subscription->shouldReceive('unsubscribe')
                     ->once();
        
        $this->expectException(RequestFailedException::class);
        
        $connection->request($event, $subscriber);
    }
    
    /**
     * @covers ::deserializeMessage
     *
     * @throws \Exception
     */
    public function testDeserializeMessage() : void
    {
        $msg = m::mock(Msg::class);
        $subscriber = new DummySubscriber(['expects' => 'expected-type']);
        
        /** @var StreamingConnection|m\MockInterface $connection */
        $connection = m::mock(
            StreamingConnection::class,
            [$this->conn, $this->deserializer, $this->initializer, $this->resolver]
        )
                       ->makePartial()
                       ->shouldAllowMockingProtectedMethods();
        
        $connection->shouldReceive('validateMessageType')
                   ->once()
                   ->with('expected-type');
        
        $data = m::mock(Stream::class);
        
        $msg->shouldReceive('getData')
            ->andReturn($data);
        
        $data->shouldReceive('getContents')
             ->andReturn('msg-data');
        
        $connection->shouldReceive('initializeUses')
                   ->with($subscriber);
        
        $protobufMsg = m::mock(Message::class);
        
        $this->deserializer->shouldReceive('deserialize')
                           ->with('msg-data', 'expected-type')
                           ->andReturn($protobufMsg);
        
        $this->assertEquals($protobufMsg, $connection->deserializeMessage($msg, $subscriber));
    }
    
    /**
     * @covers ::initializeUses
     */
    public function testInitializeUses() : void
    {
        $uses = ['foo', 'bar'];
        
        $sub = new DummySubscriberUsesProtobufAny(['uses' => $uses]);
        
        $this->initializer->shouldReceive('initialize')
                          ->with(...$uses)
                          ->once();
        
        $this->connection->initializeUses($sub);
        $this->expectNotToPerformAssertions();
    }
    
    /**
     * @covers ::initializeUses
     */
    public function testInitializeUsesWithNone() : void
    {
        $sub = new DummySubscriber();
        
        $this->initializer->shouldReceive('initialize')
                          ->withNoArgs()
                          ->once();
        
        $this->connection->initializeUses($sub);
        $this->expectNotToPerformAssertions();
    }
}
