<?php
declare(strict_types = 1);


namespace SmartWeb\Nats\Tests\Message\Serialization;

use PHPUnit\Framework\TestCase;
use SmartWeb\Nats\Payload\PayloadFields;
use SmartWeb\Nats\Payload\Serialization\PayloadDecoder;
use Symfony\Component\Serializer\Encoder\DecoderInterface;

/**
 * Class PayloadDecoderTest
 */
class PayloadDecoderTest extends TestCase
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
        
        $this->decoder = new PayloadDecoder();
    }
    
    /**
     * @test
     *
     * @param string $payload
     * @param array  $expected
     *
     * @dataProvider decodeValidDataProvider
     */
    public function shouldDecodeValidValues(string $payload, array $expected) : void
    {
        $actual = $this->decoder->decode($payload, PayloadDecoder::FORMAT);
        
        $this->assertEquals($expected, $actual, 'Should correctly decode valid message string');
    }
    
    /**
     * @skip
     * @return array
     */
    public function decodeValidDataProvider() : array
    {
        return [
            'minimal, with null entries' => [
                'payload'  => '{"eventType":"some.event","eventTypeVersion":null,"cloudEventsVersion":{"major":0,"minor":1,"patch":0},"source":"some.source","eventId":"some.event.id","eventTime":null,"schemaURL":null,"contentType":null,"extensions":null,"data":null}',
                'expected' => [
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
                    PayloadFields::DATA                 => null,
                ],
            ],
            'minimal, without entries'   => [
                'payload'  => '{"eventType":"some.event","cloudEventsVersion":{"major":0,"minor":1,"patch":0},"source":"some.source","eventId":"some.event.id"}',
                'expected' => [
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
                    PayloadFields::DATA                 => null,
                ],
            ],
            'complete'                   => [
                'payload'  => '{"eventType":"some.event","eventTypeVersion":{"major":1,"minor":2,"patch":3},"cloudEventsVersion":{"major":0,"minor":1,"patch":0},"source":"some.source","eventId":"some.event.id","eventTime":"946816496","schemaURL":"https://www.test.com/schemas/schema.json","contentType":"application/json;charset=utf-8","extensions":{"comExampleExtension":"value"},"data":{"foo":"bar"}}',
                'expected' => [
                    PayloadFields::EVENT_TYPE           => 'some.event',
                    PayloadFields::EVENT_TYPE_VERSION   => [
                        'major' => 1,
                        'minor' => 2,
                        'patch' => 3,
                    ],
                    PayloadFields::CLOUD_EVENTS_VERSION => [
                        'major' => 0,
                        'minor' => 1,
                        'patch' => 0,
                    ],
                    PayloadFields::SOURCE               => 'some.source',
                    PayloadFields::EVENT_ID             => 'some.event.id',
                    PayloadFields::EVENT_TIME           => 946816496,
                    PayloadFields::SCHEMA_URL           => 'https://www.test.com/schemas/schema.json',
                    PayloadFields::CONTENT_TYPE         => 'application/json;charset=utf-8',
                    PayloadFields::EXTENSIONS           => [
                        'comExampleExtension' => 'value',
                    ],
                    PayloadFields::DATA                 => [
                        'foo' => 'bar',
                    ],
                ],
            ],
        ];
    }
    
    /**
     * @test
     *
     * @param mixed $format
     * @param bool  $expected
     *
     * @dataProvider supportsDecodingDataProvider
     */
    public function checkSupportsDecoding($format, bool $expected) : void
    {
        $actual = $this->decoder->supportsDecoding($format);
        $this->assertSame($expected, $actual, 'Should determine if the format is supported for decoding');
    }
    
    /**
     * @skip
     * @return array
     */
    public function supportsDecodingDataProvider() : array
    {
        return [
            'string, supported'     => [
                'format'   => PayloadDecoder::FORMAT,
                'expected' => true,
            ],
            'string, not supported' => [
                'format'   => 'unsupportedFormat',
                'expected' => false,
            ],
            'int'                   => [
                'format'   => 1,
                'expected' => false,
            ],
        ];
    }
}
