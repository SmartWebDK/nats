<?php
declare(strict_types = 1);


namespace SmartWeb\Nats\Tests\Message\Serialization;

use PHPUnit\Framework\TestCase;
use SmartWeb\Nats\Message\Message;
use SmartWeb\Nats\Message\MessageFields;
use SmartWeb\Nats\Message\Serialization\MessageDenormalizer;
use Symfony\Component\Serializer\Normalizer\DenormalizerInterface;

/**
 * Class MessageDenormalizerTest
 */
class MessageDenormalizerTest extends TestCase
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
        
        $this->denormalizer = new MessageDenormalizer();
    }
    
    /**
     * @test
     *
     * @param mixed  $data
     * @param string $type
     * @param bool   $isValid
     *
     * @dataProvider supportsDenormalizationDataProvider
     */
    public function checkSupportsDenormalization($data, string $type, bool $isValid) : void
    {
        $this->assertSame(
            $isValid,
            $this->denormalizer->supportsDenormalization($data, $type),
            'Should correctly determine if the given data and type supports denormalization'
        );
    }
    
    /**
     * @skip
     * @return array
     */
    public function supportsDenormalizationDataProvider() : array
    {
        return [
            'valid'                       => [
                'data'    => [
                    MessageFields::SEQUENCE  => 1,
                    MessageFields::SUBJECT   => 'myChannel',
                    MessageFields::DATA      => '"{\"foo\":\"bar\"}"',
                    MessageFields::TIMESTAMP => 1234,
                ],
                'type'    => Message::class,
                'isValid' => true,
            ],
            'invalid: invalid data type'  => [
                'data'    => 'not an array',
                'type'    => Message::class,
                'isValid' => false,
            ],
            'invalid: invalid type'       => [
                'data'    => [
                    MessageFields::SEQUENCE  => 1,
                    MessageFields::SUBJECT   => 'myChannel',
                    MessageFields::DATA      => '"{\"foo\":\"bar\"}"',
                    MessageFields::TIMESTAMP => 1234,
                ],
                'type'    => 'someIncorrectType',
                'isValid' => false,
            ],
            'invalid: missing fields'     => [
                'data'    => [
                    MessageFields::SEQUENCE  => 1,
                    MessageFields::SUBJECT   => 'myChannel',
                    MessageFields::TIMESTAMP => 1234,
                ],
                'type'    => Message::class,
                'isValid' => false,
            ],
            'invalid: too many fields'    => [
                'data'    => [
                    MessageFields::SEQUENCE  => 1,
                    MessageFields::SUBJECT   => 'myChannel',
                    MessageFields::DATA      => '"{\"foo\":\"bar\"}"',
                    MessageFields::TIMESTAMP => 1234,
                    'other'                  => 'value',
                ],
                'type'    => Message::class,
                'isValid' => false,
            ],
            'invalid: invalid field type' => [
                'data'    => [
                    MessageFields::SEQUENCE  => 'not an int',
                    MessageFields::SUBJECT   => 'myChannel',
                    MessageFields::DATA      => '"{\"foo\":\"bar\"}"',
                    MessageFields::TIMESTAMP => 1234,
                ],
                'type'    => Message::class,
                'isValid' => false,
            ],
        ];
    }
    
    /**
     * @test
     */
    public function shouldDenormalizeValidValues() : void
    {
        $data = [
            MessageFields::SEQUENCE  => 1,
            MessageFields::SUBJECT   => 'myChannel',
            MessageFields::DATA      => '"{\"foo\":\"bar\"}"',
            MessageFields::TIMESTAMP => 1234,
        ];
        
        $expected = new Message(...\array_values($data));
        $actual = $this->denormalizer->denormalize($data, Message::class);
        
        $this->assertEquals($expected, $actual, 'Should denormalize valid data');
    }
}
