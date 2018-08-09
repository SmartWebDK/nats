<?php
declare(strict_types = 1);


namespace SmartWeb\Nats\Tests\Payload\Serialization;

use PHPUnit\Framework\TestCase;
use SmartWeb\CloudEvents\Nats\Payload\Data\ArrayData;
use SmartWeb\CloudEvents\Nats\Payload\Payload;
use SmartWeb\CloudEvents\Nats\Payload\PayloadFields;
use SmartWeb\CloudEvents\Nats\Payload\PayloadInterface;
use SmartWeb\Nats\Payload\Serialization\PayloadDecoder;
use SmartWeb\Nats\Payload\Serialization\PayloadDenormalizer;
use SmartWeb\Nats\Payload\Serialization\PayloadNormalizer;
use Symfony\Component\Serializer\Encoder\EncoderInterface;
use Symfony\Component\Serializer\Encoder\JsonEncode;
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Serializer\Normalizer\DateTimeNormalizer;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;
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
     * @var EncoderInterface
     */
    private static $jsonEncoder;
    
    /**
     * @var string
     */
    private static $dateTimeFormat;
    
    /**
     * @var NormalizerInterface
     */
    private static $dateTimeNormalizer;
    
    /**
     * @var array
     */
    private static $payloadDataArray;
    
    /**
     * @var string
     */
    private static $payloadString;
    
    /**
     * @var array
     */
    private static $payloadData;
    
    /**
     * @var PayloadInterface
     */
    private static $payload;
    
    /**
     * @var PayloadProvider
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
        
        self::$jsonEncoder = new JsonEncode();
        self::$dateTimeFormat = \DateTime::RFC3339;
        self::$dateTimeNormalizer = new DateTimeNormalizer(self::$dateTimeFormat);
        
        self::$payloadDataArray = self::getFixedPayloadDataArray();
        self::$payloadString = self::getFixedPayloadString();
        self::$payloadData = self::getFixedPayloadData();
        self::$payload = new Payload(...\array_values(self::$payloadData));
        
        self::$provider = new PayloadProvider();
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
        $payloadString = self::$provider->getCompletePayloadString();
        
        $expected = self::$provider->getCompletePayload();
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
    
    /**
     * @return string
     */
    private static function getFixedPayloadString() : string
    {
        $dataArray = self::$payloadDataArray;
        
        $dataArray[PayloadFields::EVENT_TIME] = self::$dateTimeNormalizer->normalize(
            $dataArray[PayloadFields::EVENT_TIME]
        );
        
        return self::$jsonEncoder->encode($dataArray, JsonEncoder::FORMAT);
    }
    
    /**
     * @return array
     */
    private static function getFixedPayloadData() : array
    {
        $dataArray = self::$payloadDataArray;
        
        $dataArray[PayloadFields::DATA] = new ArrayData($dataArray[PayloadFields::DATA]);
        
        return $dataArray;
    }
    
    /**
     * @return array
     */
    private static function getFixedPayloadDataArray() : array
    {
        return [
            PayloadFields::EVENT_TYPE           => 'some.event',
            PayloadFields::EVENT_TYPE_VERSION   => '1.0.0',
            PayloadFields::CLOUD_EVENTS_VERSION => '0.1.0',
            PayloadFields::SOURCE               => 'some.source',
            PayloadFields::EVENT_ID             => 'some.event.id',
            PayloadFields::EVENT_TIME           => self::getFixedTime(),
            PayloadFields::SCHEMA_URL           => 'https://www.some-schema.org/cloud-events/test.schema?version=2.3.4',
            PayloadFields::CONTENT_TYPE         => 'application/json',
            PayloadFields::EXTENSIONS           => [
                'com.foo.extension' => 'barExtension',
            ],
            PayloadFields::DATA                 => [
                'foo' => 'bar',
            ],
        ];
    }
    
    /**
     * @return \DateTimeInterface
     */
    private static function getFixedTime() : \DateTimeInterface
    {
        $time = new \DateTime();
        $time->setDate(2000, 1, 2);
        $time->setTime(12, 34, 56);
        
        $timeZone = new \DateTimeZone('Europe/Copenhagen');
        $time->setTimezone($timeZone);
        
        return $time;
    }
}
