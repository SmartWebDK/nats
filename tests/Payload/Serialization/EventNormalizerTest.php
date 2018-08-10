<?php
declare(strict_types = 1);


namespace SmartWeb\Nats\Tests\Payload\Serialization;

use PHPUnit\Framework\TestCase;
use SmartWeb\CloudEvents\Nats\Event\Data\ArrayData;
use SmartWeb\CloudEvents\Nats\Event\Event;
use SmartWeb\CloudEvents\Nats\Event\EventFields;
use SmartWeb\CloudEvents\Nats\Event\EventInterface;
use SmartWeb\Nats\Payload\Serialization\EventNormalizer;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;

/**
 * @author Nicolai Agersbæk <na@smartweb.dk>
 */
class EventNormalizerTest extends TestCase
{
    
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
     *
     * @dataProvider normalizeValidDataProvider
     */
    public function shouldNormalizeValidPayload(array $data) : void
    {
        $expected = $data;
        
        $payload = new Event(...\array_values($expected));
        
        $actual = $this->normalizer->normalize($payload);
        
        $this->assertEquals($expected, $actual);
    }
    
    /**
     * @skip
     * @return array
     */
    public function normalizeValidDataProvider() : array
    {
        return [
            'minimal'  => [
                'data' => [
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
                'data' => [
                    EventFields::EVENT_TYPE           => '',
                    EventFields::EVENT_TYPE_VERSION   => '1.0.0',
                    EventFields::CLOUD_EVENTS_VERSION => '0.1.0',
                    EventFields::SOURCE               => '',
                    EventFields::EVENT_ID             => '',
                    EventFields::EVENT_TIME           => new \DateTime(),
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
