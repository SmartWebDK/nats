<?php
declare(strict_types = 1);


namespace SmartWeb\Nats\Tests\Event\Factory;

use Google\Protobuf\Internal\Message;
use Mockery as m;
use PHPUnit\Framework\TestCase;
use SmartWeb\Events\EventInterface;
use SmartWeb\Events\Generator\EventIdGeneratorInterface;
use SmartWeb\Events\Generator\EventTimeGeneratorInterface;
use SmartWeb\Events\Generator\FixedEventIdGenerator;
use SmartWeb\Events\Generator\FixedEventTimeGenerator;
use SmartWeb\Events\Support\EventTime;
use SmartWeb\Nats\Event\Factory\ResponseEventFactory;
use SmartWeb\Nats\Event\Factory\ResponseEventFactoryInterface;
use SmartWeb\Nats\Event\Factory\ResponseInfoResolver;
use SmartWeb\Nats\Event\Factory\ResponseInfoResolverInterface;

/**
 * @coversDefaultClass \SmartWeb\Nats\Event\Factory\ResponseEventFactory
 *
 * @author Nicolai Agersbæk <na@zitcom.dk>
 */
class ResponseEventFactoryTest extends TestCase
{
    
    /**
     * @var ResponseInfoResolverInterface
     */
    private static $resolver;
    
    /**
     * @var EventIdGeneratorInterface
     */
    private static $idGenerator;
    
    /**
     * @var EventTimeGeneratorInterface
     */
    private static $timeGenerator;
    
    /**
     * @var m\MockInterface|EventInterface
     */
    private static $request;
    
    /**
     * @var string
     */
    private static $eventSource;
    
    /**
     * @var ResponseEventFactoryInterface
     */
    private static $factory;
    
    /**
     * @var m\MockInterface|Message
     */
    private static $responseData;
    
    public static function setUpBeforeClass()
    {
        parent::setUpBeforeClass();
        
        self::$idGenerator = new FixedEventIdGenerator('some-unique-id');
        self::$timeGenerator = new FixedEventTimeGenerator(EventTime::now());
        self::$resolver = new ResponseInfoResolver(self::$idGenerator, self::$timeGenerator);
        
        self::$request = m::mock(EventInterface::class);
        self::$request->expects('getEventId')->andReturn('request-id')->zeroOrMoreTimes();
        self::$request->expects('getEventType')->andReturn('test.event.type')->zeroOrMoreTimes();
        
        self::$responseData = m::mock(Message::class);
        
        self::$eventSource = 'test.source';
        
        self::$factory = new ResponseEventFactory(
            DummyResponseEvent::class,
            self::$eventSource,
            self::$resolver
        );
    }
    
    /**
     * @covers ::createResponse
     */
    public function testCreateResponse() : void
    {
        $expected = new DummyResponseEvent(
            [
                'eventType' => self::$resolver->getResponseEventType(self::$request),
                'eventId'   => self::$resolver->getResponseEventId(self::$request),
                'source'    => self::$eventSource,
                'eventTime' => self::$resolver->getResponseEventTime(),
                'data'      => self::$responseData,
            ]
        );
        $actual = self::$factory->createResponse(self::$request, self::$responseData);
        
        $this->assertEquals($expected, $actual);
    }
}
