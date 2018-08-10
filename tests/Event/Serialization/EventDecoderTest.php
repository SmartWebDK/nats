<?php
declare(strict_types = 1);


namespace SmartWeb\Nats\Tests\Event\Serialization;

use PHPUnit\Framework\TestCase;
use SmartWeb\Nats\Event\Serialization\EventDecoder;
use Symfony\Component\Serializer\Encoder\DecoderInterface;

/**
 * @author Nicolai Agersbæk <na@smartweb.dk>
 */
class EventDecoderTest extends TestCase
{
    
    use WithEventProviderFactory;
    
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
        
        $this->assertEquals($expected, $actual, 'Should correctly decode valid event string');
    }
    
    /**
     * @skip
     * @return array
     */
    public function decodeValidDataProvider() : array
    {
        return [
            'minimal, with null entries' => [
                'event'    => self::factory()->minimal()->eventString(true),
                'expected' => self::factory()->minimal()->eventContents(true),
            ],
            'minimal, without entries'   => [
                'event'    => self::factory()->minimal()->eventString(),
                'expected' => self::factory()->minimal()->eventContents(),
            ],
            'complete'                   => [
                'event'    => self::factory()->complete()->eventString(),
                'expected' => self::factory()->complete()->eventContents(),
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
}
