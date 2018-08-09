<?php
declare(strict_types = 1);


namespace SmartWeb\Nats\Tests\Payload\Serialization;

use PHPUnit\Framework\TestCase;
use SmartWeb\CloudEvents\Nats\Payload\Data\ArrayData;
use SmartWeb\CloudEvents\Nats\Payload\Payload;
use SmartWeb\CloudEvents\Nats\Payload\PayloadFields;
use SmartWeb\Nats\Payload\Serialization\PayloadDecoder;
use SmartWeb\Nats\Payload\Serialization\PayloadDenormalizer;
use SmartWeb\Nats\Payload\Serialization\PayloadNormalizer;
use Symfony\Component\Serializer\Encoder\JsonEncode;
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Serializer\Serializer;
use Symfony\Component\Serializer\SerializerInterface;

/**
 * Tests of payload serialization.
 */
class PayloadSerializerTest extends TestCase
{
    
    /**
     * @var SerializerInterface
     */
    private static $serializer;
    
    /**
     * @var PayloadProviderFactory
     */
    private static $provider;
    
    /**
     * @inheritDoc
     * The :void return type declaration that should be here would cause a BC issue
     */
    public static function setUpBeforeClass()
    {
        parent::setUpBeforeClass();
        
        $payloadNormalizer = new PayloadNormalizer();
        $payloadEncoder = new JsonEncode();
        $payloadDecoder = new PayloadDecoder();
        $payloadDenormalizer = new PayloadDenormalizer();
        
        self::$serializer = new Serializer(
            [$payloadNormalizer, $payloadDenormalizer],
            [$payloadEncoder, $payloadDecoder]
        );
        
        self::$provider = new PayloadProviderFactory();
    }
    
    /**
     * @test
     */
    public function checkSerialize() : void
    {
        $expected = '{"eventType":"some.event","eventTypeVersion":null,"cloudEventsVersion":"0.1.0","source":"some.source","eventId":"some.event.id","eventTime":null,"schemaURL":null,"contentType":null,"extensions":null,"data":{"foo":"bar"}}';
        
        $data = [
            PayloadFields::EVENT_TYPE           => 'some.event',
            PayloadFields::EVENT_TYPE_VERSION   => null,
            PayloadFields::CLOUD_EVENTS_VERSION => '0.1.0',
            PayloadFields::SOURCE               => 'some.source',
            PayloadFields::EVENT_ID             => 'some.event.id',
            PayloadFields::EVENT_TIME           => null,
            PayloadFields::SCHEMA_URL           => null,
            PayloadFields::CONTENT_TYPE         => null,
            PayloadFields::EXTENSIONS           => null,
            PayloadFields::DATA                 => new ArrayData(
                [
                    'foo' => 'bar',
                ]
            ),
        ];
        
        $payload = new Payload(...\array_values($data));
        $actual = self::$serializer->serialize($payload, JsonEncoder::FORMAT);
        
        $this->assertSame($expected, $actual);
    }
    
    /**
     * @test
     */
    public function checkDeserialize() : void
    {
        $payloadString = self::$provider->complete()->payloadString();
        
        $expected = self::$provider->complete()->payload();
        $actual = self::$serializer->deserialize($payloadString, Payload::class, PayloadDecoder::FORMAT);
        
        $this->assertEquals($expected, $actual);
    }
    
    /**
     * @test
     */
    public function checkSerializeDeserialize() : void
    {
        $data = [
            PayloadFields::EVENT_TYPE           => 'some.event',
            PayloadFields::EVENT_TYPE_VERSION   => null,
            PayloadFields::CLOUD_EVENTS_VERSION => '0.1.0',
            PayloadFields::SOURCE               => 'some.source',
            PayloadFields::EVENT_ID             => 'some.event.id',
            PayloadFields::EVENT_TIME           => null,
            PayloadFields::SCHEMA_URL           => null,
            PayloadFields::CONTENT_TYPE         => null,
            PayloadFields::EXTENSIONS           => null,
            PayloadFields::DATA                 => new ArrayData(
                [
                    'foo' => 'bar',
                ]
            ),
        ];
        
        $payload = new Payload(...\array_values($data));
        
        $serialized = self::$serializer->serialize($payload, JsonEncoder::FORMAT);
        
        $deserialized = self::$serializer->deserialize($serialized, Payload::class, PayloadDecoder::FORMAT);
        
        $this->assertEquals($payload, $deserialized);
    }
}
