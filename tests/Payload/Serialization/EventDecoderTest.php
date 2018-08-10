<?php
declare(strict_types = 1);


namespace SmartWeb\Nats\Tests\Payload\Serialization;

use PHPUnit\Framework\TestCase;
use SmartWeb\Nats\Payload\Serialization\EventDecoder;
use Symfony\Component\Serializer\Encoder\DecoderInterface;

/**
 * @author Nicolai Agersbæk <na@smartweb.dk>
 */
class EventDecoderTest extends TestCase
{
    
    /**
     * @var EventProviderFactory
     */
    private static $provider;
    
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
        
        $this->decoder = new EventDecoder();
    }
    
    /**
     * @test
     *
     * @param string $event
     * @param array  $expected
     *
     * @dataProvider decodeValidDataProvider
     */
    public function shouldDecodeValidValues(string $event, array $expected) : void
    {
        $actual = $this->decoder->decode($event, EventDecoder::FORMAT);
        
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
                'event'    => self::provider()->minimal()->eventString(true),
                'expected' => self::provider()->minimal()->eventContents(true),
            ],
            'minimal, without entries'   => [
                'event'    => self::provider()->minimal()->eventString(),
                'expected' => self::provider()->minimal()->eventContents(),
            ],
            'complete'                   => [
                'event'    => self::provider()->complete()->eventString(),
                'expected' => self::provider()->complete()->eventContents(),
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
                'format'   => EventDecoder::FORMAT,
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
    
    /**
     * @return EventProviderFactory
     */
    private static function provider() : EventProviderFactory
    {
        return self::$provider ?? self::$provider = new EventProviderFactory();
    }
}
