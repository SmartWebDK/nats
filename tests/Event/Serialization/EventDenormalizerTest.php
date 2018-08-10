<?php
declare(strict_types = 1);


namespace SmartWeb\Nats\Tests\Event\Serialization;

use PHPUnit\Framework\TestCase;
use SmartWeb\CloudEvents\Nats\Event\Event;
use SmartWeb\CloudEvents\Nats\Event\EventFields;
use SmartWeb\CloudEvents\Nats\Event\EventInterface;
use SmartWeb\Nats\Event\Serialization\EventDecoder;
use SmartWeb\Nats\Event\Serialization\EventDenormalizer;
use Symfony\Component\Serializer\Normalizer\DenormalizerInterface;

/**
 * @author Nicolai Agersbæk <na@smartweb.dk>
 */
class EventDenormalizerTest extends TestCase
{
    
    use WithEventProviderFactory;
    
    /**
     * @var DenormalizerInterface
     */
    private $denormalizer;
    
    /**
     * @inheritDoc
     */
    protected function setUp()/* The :void return type declaration that should be here would cause a BC issue */
    {
        parent::setUp();
        
        $this->denormalizer = new EventDenormalizer();
    }
    
    /**
     * @test
     *
     * @param array          $data
     * @param EventInterface $expected
     *
     * @dataProvider denormalizeValidDataProvider
     */
    public function shouldDenormalizeValidValues(array $data, EventInterface $expected) : void
    {
        $actual = $this->denormalizer->denormalize($data, Event::class);
        
        $this->assertEquals($expected, $actual, 'Should correctly denormalize event');
    }
    
    /**
     * @skip
     * @return array
     */
    public function denormalizeValidDataProvider() : array
    {
        return [
            'minimal, with null entries'    => [
                'data'     => self::factory()->minimal()->eventContents(true),
                'expected' => self::factory()->minimal()->event(),
            ],
            'minimal, without null entries' => [
                'data'     => self::factory()->minimal()->eventContents(false),
                'expected' => self::factory()->minimal()->event(),
            ],
            'complete'                      => [
                'data'     => self::factory()->complete()->eventContents(true),
                'expected' => self::factory()->complete()->event(),
            ],
        ];
    }
    
    /**
     * @test
     *
     * @param string      $data
     * @param string      $type
     * @param null|string $format
     * @param bool        $expected
     *
     * @dataProvider supportsDenormalizationDataProvider
     */
    public function checkSupportsDenormalization($data, string $type, ?string $format, bool $expected) : void
    {
        $actual = $this->denormalizer->supportsDenormalization($data, $type, $format);
        $this->assertSame($expected, $actual, 'Should determine if data supports denormalization');
    }
    
    /**
     * @skip
     * @return array
     */
    public function supportsDenormalizationDataProvider() : array
    {
        return [
            'array, minimal'                     => [
                'data'     => self::factory()->minimal()->eventContents(true),
                'type'     => Event::class,
                'format'   => EventDecoder::FORMAT,
                'expected' => true,
            ],
            'array, complete'                    => [
                'data'     => self::factory()->complete()->eventContents(true),
                'type'     => Event::class,
                'format'   => EventDecoder::FORMAT,
                'expected' => true,
            ],
            'array, minimal, invalid format'     => [
                'data'     => self::factory()->minimal()->eventContents(true),
                'type'     => Event::class,
                'format'   => null,
                'expected' => false,
            ],
            'array, incomplete'                  => [
                'data'     => [
                    EventFields::EVENT_TYPE => '',
                    EventFields::SOURCE     => '',
                    EventFields::EVENT_ID   => '',
                ],
                'type'     => Event::class,
                'format'   => null,
                'expected' => false,
            ],
            'array, empty'                       => [
                'data'     => [],
                'type'     => Event::class,
                'format'   => null,
                'expected' => false,
            ],
            'array, minimal, invalid data entry' => [
                'data'     => [
                    EventFields::EVENT_TYPE           => '',
                    EventFields::CLOUD_EVENTS_VERSION => 1,
                    EventFields::SOURCE               => '',
                    EventFields::EVENT_ID             => '',
                ],
                'type'     => EventInterface::class,
                'format'   => null,
                'expected' => false,
            ],
            'array, minimal, invalid type'       => [
                'data'     => [
                    EventFields::EVENT_TYPE           => '',
                    EventFields::CLOUD_EVENTS_VERSION => '0.1.0',
                    EventFields::SOURCE               => '',
                    EventFields::EVENT_ID             => '',
                ],
                'type'     => EventInterface::class,
                'format'   => null,
                'expected' => false,
            ],
            'Payload'                            => [
                'data'     => $this->createMock(Event::class),
                'type'     => Event::class,
                'format'   => null,
                'expected' => false,
            ],
        ];
    }
}
