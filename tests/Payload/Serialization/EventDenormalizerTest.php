<?php
declare(strict_types = 1);


namespace SmartWeb\Nats\Tests\Payload\Serialization;

use PHPUnit\Framework\TestCase;
use SmartWeb\CloudEvents\Nats\Event\Data\ArrayData;
use SmartWeb\CloudEvents\Nats\Event\Event;
use SmartWeb\CloudEvents\Nats\Event\EventFields;
use SmartWeb\CloudEvents\Nats\Event\EventInterface;
use SmartWeb\Nats\Payload\Serialization\EventDenormalizer;
use SmartWeb\Nats\Payload\Serialization\EventDecoder;
use Symfony\Component\Serializer\Normalizer\DenormalizerInterface;

/**
 * @author Nicolai Agersbæk <na@smartweb.dk>
 */
class EventDenormalizerTest extends TestCase
{
    
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
     * @param array $data
     * @param array $payload
     *
     * @dataProvider denormalizeValidDataProvider
     */
    public function shouldDenormalizeValidPayload(array $data, array $payload) : void
    {
        $expected = new Event(...\array_values($payload));
        $actual = $this->denormalizer->denormalize($data, Event::class);
        
        $this->assertEquals($expected, $actual, 'Should correctly denormalize payload');
    }
    
    /**
     * @skip
     * @return array
     */
    public function denormalizeValidDataProvider() : array
    {
        $eventTime = new \DateTime();
        
        return [
            'minimal'  => [
                'data'    => [
                    EventFields::EVENT_TYPE           => '',
                    EventFields::EVENT_TYPE_VERSION   => null,
                    EventFields::CLOUD_EVENTS_VERSION => '0.1.0',
                    EventFields::SOURCE               => '',
                    EventFields::EVENT_ID             => '',
                    EventFields::EVENT_TIME           => null,
                    EventFields::SCHEMA_URL           => null,
                    EventFields::CONTENT_TYPE         => null,
                    EventFields::EXTENSIONS           => null,
                    EventFields::DATA                 => null,
                ],
                'payload' => [
                    EventFields::EVENT_TYPE           => '',
                    EventFields::EVENT_TYPE_VERSION   => null,
                    EventFields::CLOUD_EVENTS_VERSION => '0.1.0',
                    EventFields::SOURCE               => '',
                    EventFields::EVENT_ID             => '',
                    EventFields::EVENT_TIME           => null,
                    EventFields::SCHEMA_URL           => null,
                    EventFields::CONTENT_TYPE         => null,
                    EventFields::EXTENSIONS           => null,
                    EventFields::DATA                 => null,
                ],
            ],
            'complete' => [
                'data'    => [
                    EventFields::EVENT_TYPE           => '',
                    EventFields::EVENT_TYPE_VERSION   => '1.0.0',
                    EventFields::CLOUD_EVENTS_VERSION => '0.1.0',
                    EventFields::SOURCE               => '',
                    EventFields::EVENT_ID             => '',
                    EventFields::EVENT_TIME           => $eventTime,
                    EventFields::SCHEMA_URL           => 'schemaURL', // Invalid
                    EventFields::CONTENT_TYPE         => 'contentType', // Invalid
                    EventFields::EXTENSIONS           => [
                        'extKey_1' => 'extVal_1',
                        'extKey_2' => 'extVal_2',
                    ],
                    EventFields::DATA                 => [
                        'dataKey_1' => 'dataVal_1',
                        'dataKey_2' => [
                            'dataKey_2.1' => 'dataVal_2.1',
                            'dataKey_2.2' => 'dataVal_2.2',
                        ],
                    ],
                ],
                'payload' => [
                    EventFields::EVENT_TYPE           => '',
                    EventFields::EVENT_TYPE_VERSION   => '1.0.0',
                    EventFields::CLOUD_EVENTS_VERSION => '0.1.0',
                    EventFields::SOURCE               => '',
                    EventFields::EVENT_ID             => '',
                    EventFields::EVENT_TIME           => $eventTime,
                    EventFields::SCHEMA_URL           => 'schemaURL', // Invalid
                    EventFields::CONTENT_TYPE         => 'contentType', // Invalid
                    EventFields::EXTENSIONS           => [
                        'extKey_1' => 'extVal_1',
                        'extKey_2' => 'extVal_2',
                    ],
                    EventFields::DATA                 => new ArrayData(
                        [
                            'dataKey_1' => 'dataVal_1',
                            'dataKey_2' => [
                                'dataKey_2.1' => 'dataVal_2.1',
                                'dataKey_2.2' => 'dataVal_2.2',
                            ],
                        ]
                    ),
                ],
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
        $eventTime = new \DateTime();
        
        return [
            'array, minimal'                     => [
                'data'     => [
                    EventFields::EVENT_TYPE           => '',
                    EventFields::CLOUD_EVENTS_VERSION => '0.1.0',
                    EventFields::SOURCE               => '',
                    EventFields::EVENT_ID             => '',
                ],
                'type'     => Event::class,
                'format'   => EventDecoder::FORMAT,
                'expected' => true,
            ],
            'array, complete'                    => [
                'data'     => [
                    EventFields::EVENT_TYPE           => '',
                    EventFields::EVENT_TYPE_VERSION   => '1.0.0',
                    EventFields::CLOUD_EVENTS_VERSION => '0.1.0',
                    EventFields::SOURCE               => '',
                    EventFields::EVENT_ID             => '',
                    EventFields::EVENT_TIME           => $eventTime,
                    EventFields::SCHEMA_URL           => 'schemaURL', // Invalid
                    EventFields::CONTENT_TYPE         => 'contentType', // Invalid
                    EventFields::EXTENSIONS           => [],
                    EventFields::DATA                 => new ArrayData([]),
                ],
                'type'     => Event::class,
                'format'   => EventDecoder::FORMAT,
                'expected' => true,
            ],
            'array, minimal, invalid format'     => [
                'data'     => [
                    EventFields::EVENT_TYPE           => '',
                    EventFields::CLOUD_EVENTS_VERSION => '0.1.0',
                    EventFields::SOURCE               => '',
                    EventFields::EVENT_ID             => '',
                ],
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
