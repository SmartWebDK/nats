<?php
declare(strict_types = 1);


namespace SmartWeb\Nats\Tests\Payload\Serialization;

use PHPUnit\Framework\TestCase;
use SmartWeb\Nats\Payload\Data\ArrayData;
use SmartWeb\Nats\Payload\Payload;
use SmartWeb\Nats\Payload\PayloadFields;
use SmartWeb\Nats\Payload\PayloadInterface;
use SmartWeb\Nats\Payload\Serialization\PayloadNormalizer;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;

/**
 * Class PayloadNormalizerTest
 */
class PayloadNormalizerTest extends TestCase
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
        
        $this->normalizer = new PayloadNormalizer();
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
        
        $payload = new Payload(...\array_values($expected));
        
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
                'data' => [
                    PayloadFields::EVENT_TYPE           => '',
                    PayloadFields::EVENT_TYPE_VERSION   => '1.0.0',
                    PayloadFields::CLOUD_EVENTS_VERSION => '0.1.0',
                    PayloadFields::SOURCE               => '',
                    PayloadFields::EVENT_ID             => '',
                    PayloadFields::EVENT_TIME           => new \DateTime(),
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
                'data'     => $this->createMock(Payload::class),
                'format'   => null,
                'expected' => true,
            ],
            'PayloadInterface' => [
                'data'     => $this->createMock(PayloadInterface::class),
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
