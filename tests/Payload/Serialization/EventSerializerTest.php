<?php
declare(strict_types = 1);


namespace SmartWeb\Nats\Tests\Payload\Serialization;

use PHPUnit\Framework\TestCase;
use SmartWeb\CloudEvents\Nats\Event\Data\ArrayData;
use SmartWeb\CloudEvents\Nats\Event\Event;
use SmartWeb\CloudEvents\Nats\Event\EventFields;
use SmartWeb\Nats\Payload\Serialization\EventDecoder;
use SmartWeb\Nats\Payload\Serialization\EventDenormalizer;
use SmartWeb\Nats\Payload\Serialization\EventNormalizer;
use Symfony\Component\Serializer\Encoder\JsonEncode;
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Serializer\Serializer;
use Symfony\Component\Serializer\SerializerInterface;

/**
 * @author Nicolai Agersbæk <na@smartweb.dk>
 */
class EventSerializerTest extends TestCase
{
    
    /**
     * @var SerializerInterface
     */
    private static $serializer;
    
    /**
     * @var EventProviderFactory
     */
    private static $provider;
    
    /**
     * @inheritDoc
     * The :void return type declaration that should be here would cause a BC issue
     */
    public static function setUpBeforeClass()
    {
        parent::setUpBeforeClass();
        
        $payloadNormalizer = new EventNormalizer();
        $payloadEncoder = new JsonEncode();
        $payloadDecoder = new EventDecoder();
        $payloadDenormalizer = new EventDenormalizer();
        
        self::$serializer = new Serializer(
            [$payloadNormalizer, $payloadDenormalizer],
            [$payloadEncoder, $payloadDecoder]
        );
        
        self::$provider = new EventProviderFactory();
    }
    
    /**
     * @test
     */
    public function checkSerialize() : void
    {
        $expected = '{"eventType":"some.event","eventTypeVersion":null,"cloudEventsVersion":"0.1.0","source":"some.source","eventId":"some.event.id","eventTime":null,"schemaURL":null,"contentType":null,"extensions":null,"data":{"foo":"bar"}}';
        
        $data = [
            EventFields::EVENT_TYPE           => 'some.event',
            EventFields::EVENT_TYPE_VERSION   => null,
            EventFields::CLOUD_EVENTS_VERSION => '0.1.0',
            EventFields::SOURCE               => 'some.source',
            EventFields::EVENT_ID             => 'some.event.id',
            EventFields::EVENT_TIME           => null,
            EventFields::SCHEMA_URL           => null,
            EventFields::CONTENT_TYPE         => null,
            EventFields::EXTENSIONS           => null,
            EventFields::DATA                 => new ArrayData(
                [
                    'foo' => 'bar',
                ]
            ),
        ];
        
        $payload = new Event(...\array_values($data));
        $actual = self::$serializer->serialize($payload, JsonEncoder::FORMAT);
        
        $this->assertSame($expected, $actual);
    }
    
    /**
     * @test
     */
    public function checkDeserialize() : void
    {
        $payloadString = self::$provider->complete()->eventString();
        
        $expected = self::$provider->complete()->event();
        $actual = self::$serializer->deserialize($payloadString, Event::class, EventDecoder::FORMAT);
        
        $this->assertEquals($expected, $actual);
    }
    
    /**
     * @test
     */
    public function checkSerializeDeserialize() : void
    {
        $data = [
            EventFields::EVENT_TYPE           => 'some.event',
            EventFields::EVENT_TYPE_VERSION   => null,
            EventFields::CLOUD_EVENTS_VERSION => '0.1.0',
            EventFields::SOURCE               => 'some.source',
            EventFields::EVENT_ID             => 'some.event.id',
            EventFields::EVENT_TIME           => null,
            EventFields::SCHEMA_URL           => null,
            EventFields::CONTENT_TYPE         => null,
            EventFields::EXTENSIONS           => null,
            EventFields::DATA                 => new ArrayData(
                [
                    'foo' => 'bar',
                ]
            ),
        ];
        
        $payload = new Event(...\array_values($data));
        
        $serialized = self::$serializer->serialize($payload, JsonEncoder::FORMAT);
        
        $deserialized = self::$serializer->deserialize($serialized, Event::class, EventDecoder::FORMAT);
        
        $this->assertEquals($payload, $deserialized);
    }
}
