<?php
declare(strict_types = 1);


namespace SmartWeb\Nats\Tests\Message\Serialization;

use PHPUnit\Framework\TestCase;
use SmartWeb\Nats\Message\Serialization\MessageDecoder;
use SmartWeb\Nats\Payload\PayloadFields;
use Symfony\Component\Serializer\Encoder\DecoderInterface;

/**
 * Class MessageDecoderTest
 */
class MessageDecoderTest extends TestCase
{
    
    /**
     * @var DecoderInterface
     */
    private $decoder;
    
    /**
     * @inheritDoc
     */
    protected function setUp()/* The :void return type declaration that should be here would cause a BC issue */
    {
        parent::setUp();
        
        $this->decoder = new MessageDecoder();
    }
    
    /**
     * @test
     */
    public function shouldDecodeValidValues() : void
    {
        $messageString = <<<'MESSAGE'
sequence: 17
subject: "some.channel"
data: "{\"eventType\":\"some.event\",\"eventTypeVersion\":null,\"cloudEventsVersion\":{\"major\":0,\"minor\":1,\"patch\":0},\"source\":\"some.source\",\"eventId\":\"some.event.id\",\"eventTime\":null,\"schemaURL\":null,\"contentType\":null,\"extensions\":null,\"data\":{\"foo\":\"bar\"}}"
timestamp: 1532525124250055719
MESSAGE;
        
        $sequence = 17;
        $subject = 'some.channel';
        $data = [
            PayloadFields::EVENT_TYPE           => 'some.event',
            PayloadFields::EVENT_TYPE_VERSION   => null,
            PayloadFields::CLOUD_EVENTS_VERSION => [
                'major' => 0,
                'minor' => 1,
                'patch' => 0,
            ],
            PayloadFields::SOURCE               => 'some.source',
            PayloadFields::EVENT_ID             => 'some.event.id',
            PayloadFields::EVENT_TIME           => null,
            PayloadFields::SCHEMA_URL           => null,
            PayloadFields::CONTENT_TYPE         => null,
            PayloadFields::EXTENSIONS           => null,
            PayloadFields::DATA                 => [
                'foo' => 'bar',
            ],
        ];
        $timestamp = 1532525124250055719;
        
        $expected = [
            'sequence'  => $sequence,
            'subject'   => $subject,
            'data'      => $data,
            'timestamp' => $timestamp,
        ];
        
        $actual = $this->decoder->decode($messageString, MessageDecoder::FORMAT);
        
        $this->assertEquals($expected, $actual, 'Should correctly decode valid message string');
    }
}
