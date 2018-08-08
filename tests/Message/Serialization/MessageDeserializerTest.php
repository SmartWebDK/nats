<?php
declare(strict_types = 1);


namespace SmartWeb\Nats\Tests\Message\Serialization;

use PHPUnit\Framework\TestCase;
use SmartWeb\Nats\Message\Message;
use SmartWeb\Nats\Message\Serialization\MessageDecoder;
use SmartWeb\Nats\Message\Serialization\MessageDenormalizer;
use SmartWeb\Nats\Message\Serialization\MessageDeserializer;
use SmartWeb\Nats\Support\DeserializerInterface;
use Symfony\Component\Serializer\Exception\NotEncodableValueException;
use Symfony\Component\Serializer\Exception\NotNormalizableValueException;

/**
 * @author Nicolai Agersbæk <na@smartweb.dk>
 */
class MessageDeserializerTest extends TestCase
{
    
    /**
     * The message deserializer to be tested.
     *
     * @var DeserializerInterface
     */
    private static $deserializer;
    
    /**
     * Sample (valid) message string.
     *
     * @var string
     */
    private static $messageString;
    
    /**
     * @inheritDoc
     */
    public static function setUpBeforeClass(
    ) /* The :void return type declaration that should be here would cause a BC issue */
    {
        parent::setUpBeforeClass();
        
        $decoder = new MessageDecoder();
        $denormalizer = new MessageDenormalizer();
        
        self::$deserializer = new MessageDeserializer($decoder, $denormalizer);
        self::$messageString = <<<'MESSAGE'
sequence: 17
subject: "some.channel"
data: "{"eventType":"some.event","eventTypeVersion":null,"cloudEventsVersion":{"major":0,"minor":1,"patch":0},"source":"some.source","eventId":"some.event.id","eventTime":null,"schemaURL":null,"contentType":null,"extensions":null,"data":{"foo":"bar"}}"
timestamp: 1532525124250055719
MESSAGE;
    }
    
    /**
     * @test
     */
    public function shouldDeserializeValidValues() : void
    {
        $sequence = 17;
        $subject = 'some.channel';
        $messageData = '{"eventType":"some.event","eventTypeVersion":null,"cloudEventsVersion":{"major":0,"minor":1,"patch":0},"source":"some.source","eventId":"some.event.id","eventTime":null,"schemaURL":null,"contentType":null,"extensions":null,"data":{"foo":"bar"}}';
        $timestamp = 1532525124250055719;
        
        $expected = new Message($sequence, $subject, $messageData, $timestamp);
        
        $data = self::$messageString;
        $type = Message::class;
        $format = MessageDecoder::FORMAT;
        $context = null;
        
        $actual = self::$deserializer->deserialize($data, $type, $format, $context);
        $this->assertEquals($expected, $actual);
    }
    
    /**
     * @test
     */
    public function shouldThrowExceptionForInvalidDecodingFormat() : void
    {
        $data = self::$messageString;
        $type = Message::class;
        $format = 'unsupportedFormat';
        $context = null;
        
        $this->expectException(NotEncodableValueException::class);
        
        self::$deserializer->deserialize($data, $type, $format, $context);
    }
    
    /**
     * @test
     */
    public function shouldThrowExceptionForInvalidDenormalizationType() : void
    {
        $data = self::$messageString;
        $type = 'unsupportedType';
        $format = MessageDecoder::FORMAT;
        $context = null;
        
        $this->expectException(NotNormalizableValueException::class);
        
        self::$deserializer->deserialize($data, $type, $format, $context);
    }
}
