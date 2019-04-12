<?php
declare(strict_types = 1);


namespace SmartWeb\Nats\Tests\Message;

use Google\Protobuf\Internal\Message;
use PHPUnit\Framework\TestCase;
use SmartWeb\Nats\Error\InvalidTypeException;
use SmartWeb\Nats\Message\Deserializer;

/**
 * @coversDefaultClass \SmartWeb\Nats\Message\Deserializer
 *
 * @author Nicolai Agersbæk <na@zitcom.dk>
 */
class DeserializerTest extends TestCase
{
    
    /**
     * @var Deserializer
     */
    private $deserializer;
    
    protected function setUp()
    {
        parent::setUp();
        
        $this->deserializer = $this->deserializer ?? new Deserializer();
    }
    
    /**
     * @covers ::deserialize
     */
    public function testDeserialize() : void
    {
        $this->markTestSkipped();
        $this->markTestIncomplete('Missing tests!');
    }
    
    /**
     * @covers ::validateMessageType
     */
    public function testValidateMessageType() : void
    {
        $valid = Message::class;
        
        $this->deserializer->validateMessageType($valid);
        $this->expectNotToPerformAssertions();
    }
    
    /**
     * @covers ::validateMessageType
     */
    public function testValidateMessageTypeInvalid() : void
    {
        $invalid = 'Foo\\Bar\\Class';
        
        $this->expectException(InvalidTypeException::class);
        $this->deserializer->validateMessageType($invalid);
    }
    
    /**
     * @covers ::typeIsProtobufCompatible
     */
    public function testTypeIsProtobufCompatible() : void
    {
        $valid = Message::class;
        $invalid = 'Foo\\Bar\\Class';
        
        $this->assertTrue($this->deserializer->typeIsProtobufCompatible($valid));
        $this->assertFalse($this->deserializer->typeIsProtobufCompatible($invalid));
    }
}
