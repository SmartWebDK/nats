<?php
declare(strict_types = 1);


namespace SmartWeb\Nats\Tests\Payload\Serialization;

use PHPUnit\Framework\TestCase;
use SmartWeb\CloudEvents\Nats\Event\Event;
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
    
    use WithEventProviderFactory;
    
    /**
     * @var SerializerInterface
     */
    private static $serializer;
    
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
    }
    
    /**
     * @test
     *
     * @param Event  $event
     * @param string $eventString
     *
     * @dataProvider serializeDataProvider
     */
    public function checkSerialize(Event $event, string $eventString) : void
    {
        $actual = self::$serializer->serialize($event, JsonEncoder::FORMAT);
        
        $this->assertSame($eventString, $actual);
    }
    
    /**
     * @test
     *
     * @param Event  $event
     * @param string $eventString
     *
     * @dataProvider serializeDataProvider
     */
    public function checkDeserialize(Event $event, string $eventString) : void
    {
        $actual = self::$serializer->deserialize($eventString, Event::class, EventDecoder::FORMAT);
        
        $this->assertEquals($event, $actual);
    }
    
    /**
     * @test
     *
     * @param Event  $event
     * @param string $eventString
     *
     * @dataProvider serializeDataProvider
     */
    public function checkSerializeDeserialize(Event $event, string $eventString) : void
    {
        $serialized = self::$serializer->serialize($event, JsonEncoder::FORMAT);
        
        $deserialized = self::$serializer->deserialize($serialized, Event::class, EventDecoder::FORMAT);
        
        $this->assertEquals($event, $deserialized);
    }
    
    /**
     * @skip
     * @return array
     */
    public function serializeDataProvider() : array
    {
        return [
            'minimal' => [
                'event'       => self::factory()->minimal()->event(),
                'eventString' => self::factory()->minimal()->eventString(),
            ],
        ];
    }
}
