<?php
declare(strict_types = 1);


namespace SmartWeb\Nats\Tests\Subscriber;

use Mockery as m;
use PHPUnit\Framework\TestCase;
use SmartWeb\Nats\Subscriber\MessageInitializer;

/**
 * @coversDefaultClass \SmartWeb\Nats\Subscriber\MessageInitializer
 *
 * @author Nicolai Agersbæk <na@zitcom.dk>
 */
class MessageInitializerTest extends TestCase
{
    
    /**
     * @covers ::initialize
     * @covers ::initializeType
     */
    public function testInitialize() : void
    {
        /** @var MessageInitializer|m\MockInterface $initializer */
        $initializer = m::mock(MessageInitializer::class)
                        ->makePartial()
                        ->shouldAllowMockingProtectedMethods();
        
        $types = ['foo', 'bar'];
        
        $initializer->shouldReceive('initializeType')
                    ->with('foo')
                    ->once();
        
        $initializer->shouldReceive('initializeType')
                    ->with('bar')
                    ->once();
        
        $initializer->initialize(...$types);
        $initializer->initialize(...$types);
        
        $this->expectNotToPerformAssertions();
    }
}
