<?php
declare(strict_types = 1);


namespace SmartWeb\Nats\Tests\Event\Serialization;

use PHPUnit\Framework\TestCase;
use SmartWeb\CloudEvents\Nats\Event\Event;
use SmartWeb\CloudEvents\Nats\Event\EventInterface;
use SmartWeb\Nats\Event\Serialization\EventNormalizer;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;

/**
 * @author Nicolai Agersbæk <na@smartweb.dk>
 */
class EventNormalizerTest extends TestCase
{
    
    use WithEventProviderFactory;
    
    /**
     * @var NormalizerInterface
     */
    private $normalizer;
    
    /**
     * @inheritDoc
     */
    protected function setUp()/* The :void return type declaration that should be here would cause a BC issue */
    {
        parent::setUp();
        
        $this->normalizer = new EventNormalizer();
    }
    
    /**
     * @test
     *
     * @param array $data
     * @param Event $event
     *
     * @dataProvider normalizeValidDataProvider
     */
    public function shouldNormalizeValidPayload(array $data, Event $event) : void
    {
        $actual = $this->normalizer->normalize($event);
        
        $this->assertEquals($data, $actual);
    }
    
    /**
     * @skip
     * @return array
     */
    public function normalizeValidDataProvider() : array
    {
        return [
            'minimal'  => [
                'data'  => self::factory()->minimal()->eventContents(true),
                'event' => self::factory()->minimal()->event(),
            ],
            'complete' => [
                'data'  => self::factory()->complete()->eventContents(true),
                'event' => self::factory()->complete()->event(),
            ],
        ];
    }
    
    /**
     * @test
     *
     * @param string      $data
     * @param null|string $format
     * @param bool        $expected
     *
     * @dataProvider supportsNormalizationDataProvider
     */
    public function shouldDetermineIfDataSupportsNormalization($data, ?string $format, bool $expected) : void
    {
        $actual = $this->normalizer->supportsNormalization($data, $format);
        $this->assertSame($expected, $actual);
    }
    
    /**
     * @skip
     * @return array
     */
    public function supportsNormalizationDataProvider() : array
    {
        return [
            'Payload'          => [
                'data'     => $this->createMock(Event::class),
                'format'   => null,
                'expected' => true,
            ],
            'PayloadInterface' => [
                'data'     => $this->createMock(EventInterface::class),
                'format'   => null,
                'expected' => true,
            ],
            'array'            => [
                'data'     => [],
                'format'   => null,
                'expected' => false,
            ],
        ];
    }
}
