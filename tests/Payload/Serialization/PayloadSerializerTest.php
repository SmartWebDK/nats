<?php
declare(strict_types = 1);


namespace SmartWeb\Nats\Tests\Payload\Serialization;

use PHPUnit\Framework\TestCase;
use SmartWeb\Nats\Message\Serialization\MessageDecoder;
use SmartWeb\Nats\Payload\Data\ArrayData;
use SmartWeb\Nats\Payload\Payload;
use SmartWeb\Nats\Payload\PayloadFields;
use SmartWeb\Nats\Payload\Serialization\PayloadDenormalizer;
use SmartWeb\Nats\Payload\Serialization\PayloadNormalizer;
use SmartWeb\Nats\Payload\Serialization\PayloadSerializer;
use SmartWeb\Nats\Payload\Serialization\PayloadSerializerInterface;
use Symfony\Component\Serializer\Encoder\JsonEncode;

/**
 * Tests of payload serialization.
 */
class PayloadSerializerTest extends TestCase
{
    
    /**
     * @var PayloadSerializerInterface
     */
    private $serializer;
    
    /**
     * @inheritDoc
     */
    protected function setUp()/* The :void return type declaration that should be here would cause a BC issue */
    {
        parent::setUp();
        
        $normalizer = new PayloadNormalizer();
        $denormalizer = new PayloadDenormalizer();
        $encoder = new JsonEncode();
        $decoder = new MessageDecoder();
        
        $this->serializer = new PayloadSerializer($normalizer, $denormalizer, $encoder, $decoder);
    }
    
    /**
     * @test
     */
    public function shouldDeserializeValidValues() : void
    {
        $rawMessageString = <<<'MESSAGE'
sequence: 17
subject: "some.channel"
data: "{"eventType":"some.event","eventTypeVersion":null,"cloudEventsVersion":"0.1.0","source":"some.source","eventId":"some.event.id","eventTime":null,"schemaURL":null,"contentType":null,"extensions":null,"data":{"foo":"bar"}}"
timestamp: 1532525124250055719
MESSAGE;
        
        $expectedPayloadData = [
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
        
        $expected = new Payload(...\array_values($expectedPayloadData));
        $actual = $this->serializer->deserialize($rawMessageString);
        
        $this->assertEquals($expected, $actual);
    }
}
