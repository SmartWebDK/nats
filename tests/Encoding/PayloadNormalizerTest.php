<?php
declare(strict_types = 1);


namespace SmartWeb\Nats\Tests\Encoding;

use PHPUnit\Framework\TestCase;
use SmartWeb\CloudEvents\Version;
use SmartWeb\Nats\Encoding\PayloadNormalizer;
use SmartWeb\Nats\Payload\Payload;
use SmartWeb\Nats\Payload\PayloadField;
use SmartWeb\Nats\Payload\PayloadInterface;
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
                    PayloadField::EVENT_TYPE           => '',
                    PayloadField::EVENT_TYPE_VERSION   => null,
                    PayloadField::CLOUD_EVENTS_VERSION => new Version(0, 1, 0),
                    PayloadField::SOURCE               => '',
                    PayloadField::EVENT_ID             => '',
                    PayloadField::EVENT_TIME           => null,
                    PayloadField::SCHEMA_URL           => null,
                    PayloadField::CONTENT_TYPE         => null,
                    PayloadField::EXTENSIONS           => null,
                    PayloadField::DATA                 => null,
                ],
            ],
            'complete' => [
                'data' => [
                    PayloadField::EVENT_TYPE           => '',
                    PayloadField::EVENT_TYPE_VERSION   => new Version(1, 0, 0),
                    PayloadField::CLOUD_EVENTS_VERSION => new Version(0, 1, 0),
                    PayloadField::SOURCE               => '',
                    PayloadField::EVENT_ID             => '',
                    PayloadField::EVENT_TIME           => new \DateTime(),
                    PayloadField::SCHEMA_URL           => 'schemaURL', // Invalid
                    PayloadField::CONTENT_TYPE         => 'contentType', // Invalid
                    PayloadField::EXTENSIONS           => [
                        'extKey_1' => 'extVal_1',
                        'extKey_2' => 'extVal_2',
                    ],
                    PayloadField::DATA                 => [
                        'dataKey_1' => 'dataVal_1',
                        'dataKey_2' => [
                            'dataKey_2.1' => 'dataVal_2.1',
                            'dataKey_2.2' => 'dataVal_2.2',
                        ],
                    ],
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
