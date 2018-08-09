<?php
declare(strict_types = 1);


namespace SmartWeb\Nats\Tests\Payload\Serialization;

use PHPUnit\Framework\TestCase;
use SmartWeb\CloudEvents\Nats\Payload\Data\ArrayData;
use SmartWeb\CloudEvents\Nats\Payload\Payload;
use SmartWeb\CloudEvents\Nats\Payload\PayloadFields;
use SmartWeb\CloudEvents\Nats\Payload\PayloadInterface;
use SmartWeb\Nats\Payload\Serialization\PayloadDecoder;
use SmartWeb\Nats\Payload\Serialization\PayloadDenormalizer;
use Symfony\Component\Serializer\Normalizer\DenormalizerInterface;

/**
 * Class PayloadDenormalizerTest
 */
class PayloadDenormalizerTest extends TestCase
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
        
        $this->denormalizer = new PayloadDenormalizer();
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
        $expected = new Payload(...\array_values($payload));
        $actual = $this->denormalizer->denormalize($data, Payload::class);
        
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
                    PayloadFields::EVENT_TYPE           => '',
                    PayloadFields::EVENT_TYPE_VERSION   => null,
                    PayloadFields::CLOUD_EVENTS_VERSION => '0.1.0',
                    PayloadFields::SOURCE               => '',
                    PayloadFields::EVENT_ID             => '',
                    PayloadFields::EVENT_TIME           => null,
                    PayloadFields::SCHEMA_URL           => null,
                    PayloadFields::CONTENT_TYPE         => null,
                    PayloadFields::EXTENSIONS           => null,
                    PayloadFields::DATA                 => null,
                ],
                'payload' => [
                    PayloadFields::EVENT_TYPE           => '',
                    PayloadFields::EVENT_TYPE_VERSION   => null,
                    PayloadFields::CLOUD_EVENTS_VERSION => '0.1.0',
                    PayloadFields::SOURCE               => '',
                    PayloadFields::EVENT_ID             => '',
                    PayloadFields::EVENT_TIME           => null,
                    PayloadFields::SCHEMA_URL           => null,
                    PayloadFields::CONTENT_TYPE         => null,
                    PayloadFields::EXTENSIONS           => null,
                    PayloadFields::DATA                 => null,
                ],
            ],
            'complete' => [
                'data'    => [
                    PayloadFields::EVENT_TYPE           => '',
                    PayloadFields::EVENT_TYPE_VERSION   => '1.0.0',
                    PayloadFields::CLOUD_EVENTS_VERSION => '0.1.0',
                    PayloadFields::SOURCE               => '',
                    PayloadFields::EVENT_ID             => '',
                    PayloadFields::EVENT_TIME           => $eventTime,
                    PayloadFields::SCHEMA_URL           => 'schemaURL', // Invalid
                    PayloadFields::CONTENT_TYPE         => 'contentType', // Invalid
                    PayloadFields::EXTENSIONS           => [
                        'extKey_1' => 'extVal_1',
                        'extKey_2' => 'extVal_2',
                    ],
                    PayloadFields::DATA                 => [
                        'dataKey_1' => 'dataVal_1',
                        'dataKey_2' => [
                            'dataKey_2.1' => 'dataVal_2.1',
                            'dataKey_2.2' => 'dataVal_2.2',
                        ],
                    ],
                ],
                'payload' => [
                    PayloadFields::EVENT_TYPE           => '',
                    PayloadFields::EVENT_TYPE_VERSION   => '1.0.0',
                    PayloadFields::CLOUD_EVENTS_VERSION => '0.1.0',
                    PayloadFields::SOURCE               => '',
                    PayloadFields::EVENT_ID             => '',
                    PayloadFields::EVENT_TIME           => $eventTime,
                    PayloadFields::SCHEMA_URL           => 'schemaURL', // Invalid
                    PayloadFields::CONTENT_TYPE         => 'contentType', // Invalid
                    PayloadFields::EXTENSIONS           => [
                        'extKey_1' => 'extVal_1',
                        'extKey_2' => 'extVal_2',
                    ],
                    PayloadFields::DATA                 => new ArrayData(
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
                    PayloadFields::EVENT_TYPE           => '',
                    PayloadFields::CLOUD_EVENTS_VERSION => '0.1.0',
                    PayloadFields::SOURCE               => '',
                    PayloadFields::EVENT_ID             => '',
                ],
                'type'     => Payload::class,
                'format'   => PayloadDecoder::FORMAT,
                'expected' => true,
            ],
            'array, complete'                    => [
                'data'     => [
                    PayloadFields::EVENT_TYPE           => '',
                    PayloadFields::EVENT_TYPE_VERSION   => '1.0.0',
                    PayloadFields::CLOUD_EVENTS_VERSION => '0.1.0',
                    PayloadFields::SOURCE               => '',
                    PayloadFields::EVENT_ID             => '',
                    PayloadFields::EVENT_TIME           => $eventTime,
                    PayloadFields::SCHEMA_URL           => 'schemaURL', // Invalid
                    PayloadFields::CONTENT_TYPE         => 'contentType', // Invalid
                    PayloadFields::EXTENSIONS           => [],
                    PayloadFields::DATA                 => new ArrayData([]),
                ],
                'type'     => Payload::class,
                'format'   => PayloadDecoder::FORMAT,
                'expected' => true,
            ],
            'array, minimal, invalid format'     => [
                'data'     => [
                    PayloadFields::EVENT_TYPE           => '',
                    PayloadFields::CLOUD_EVENTS_VERSION => '0.1.0',
                    PayloadFields::SOURCE               => '',
                    PayloadFields::EVENT_ID             => '',
                ],
                'type'     => Payload::class,
                'format'   => null,
                'expected' => false,
            ],
            'array, incomplete'                  => [
                'data'     => [
                    PayloadFields::EVENT_TYPE => '',
                    PayloadFields::SOURCE     => '',
                    PayloadFields::EVENT_ID   => '',
                ],
                'type'     => Payload::class,
                'format'   => null,
                'expected' => false,
            ],
            'array, empty'                       => [
                'data'     => [],
                'type'     => Payload::class,
                'format'   => null,
                'expected' => false,
            ],
            'array, minimal, invalid data entry' => [
                'data'     => [
                    PayloadFields::EVENT_TYPE           => '',
                    PayloadFields::CLOUD_EVENTS_VERSION => 1,
                    PayloadFields::SOURCE               => '',
                    PayloadFields::EVENT_ID             => '',
                ],
                'type'     => PayloadInterface::class,
                'format'   => null,
                'expected' => false,
            ],
            'array, minimal, invalid type'       => [
                'data'     => [
                    PayloadFields::EVENT_TYPE           => '',
                    PayloadFields::CLOUD_EVENTS_VERSION => '0.1.0',
                    PayloadFields::SOURCE               => '',
                    PayloadFields::EVENT_ID             => '',
                ],
                'type'     => PayloadInterface::class,
                'format'   => null,
                'expected' => false,
            ],
            'Payload'                            => [
                'data'     => $this->createMock(Payload::class),
                'type'     => Payload::class,
                'format'   => null,
                'expected' => false,
            ],
        ];
    }
}
