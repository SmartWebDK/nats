<?php
declare(strict_types = 1);


namespace SmartWeb\Nats\Tests\Event\Factory;

use Mockery as m;
use PHPUnit\Framework\TestCase;
use SmartWeb\Events\EventInterface;
use SmartWeb\Events\Generator\EventIdGeneratorInterface;
use SmartWeb\Events\Generator\EventTimeGeneratorInterface;
use SmartWeb\Events\Generator\FixedEventIdGenerator;
use SmartWeb\Events\Generator\FixedEventTimeGenerator;
use SmartWeb\Events\Support\EventTime;
use SmartWeb\Nats\Event\Factory\ResponseInfoResolver;
use SmartWeb\Nats\Event\Factory\ResponseInfoResolverInterface;

/**
 * @coversDefaultClass \SmartWeb\Nats\Event\Factory\ResponseInfoResolver
 *
 * @author Nicolai Agersbæk <na@zitcom.dk>
 */
class ResponseInfoResolverTest extends TestCase
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
    
    public static function setUpBeforeClass()
    {
        parent::setUpBeforeClass();
        
        self::$idGenerator = new FixedEventIdGenerator('some-unique-id');
        self::$timeGenerator = new FixedEventTimeGenerator(EventTime::now());
        self::$resolver = new ResponseInfoResolver(self::$idGenerator, self::$timeGenerator);
        
        self::$request = m::mock(EventInterface::class);
        self::$request->expects('getEventId')->andReturn('request-id')->zeroOrMoreTimes();
        self::$request->expects('getEventType')->andReturn('test.event.type')->zeroOrMoreTimes();
    }
    
    /**
     * @covers ::getResponseEventType
     */
    public function testGetResponseEventType() : void
    {
        $expected = 'test.event.type.response';
        $actual = self::$resolver->getResponseEventType(self::$request);
        
        $this->assertSame($expected, $actual);
    }
    
    /**
     * @covers ::getResponseEventId
     */
    public function testGetResponseEventId() : void
    {
        $expected = self::$idGenerator->generate();
        $actual = self::$resolver->getResponseEventId(self::$request);
        
        $this->assertSame($expected, $actual);
    }
    
    /**
     * @covers ::getResponseEventTime
     */
    public function testGetResponseEventTime() : void
    {
        $expected = self::$timeGenerator->generate();
        $actual = self::$resolver->getResponseEventTime();
        
        $this->assertSame($expected, $actual);
    }
    
    /**
     * @covers ::getResponseChannel
     */
    public function testGetResponseChannel() : void
    {
        $expected = 'responses.test_event_type.request-id';
        $actual = self::$resolver->getResponseChannel(self::$request);
        
        $this->assertSame($expected, $actual);
    }
}
