<?php
declare(strict_types = 1);


namespace SmartWeb\Nats\Tests\Fixtures;

use SmartWeb\Nats\Subscriber\UsesProtobufAnyInterface;

/**
 * @author Nicolai Agersbæk <na@zitcom.dk>
 */
class DummySubscriberUsesProtobufAny extends DummySubscriber implements UsesProtobufAnyInterface
{
    
    /**
     * @var array|null
     */
    private $uses;
    
    /**
     * @param array|null $config
     */
    public function __construct(?array $config = null)
    {
        parent::__construct($config);
        
        $this->uses = $this->resolveConfigValue($config, 'uses', 'array');
    }
    
    /**
     * Complete list of all message classes used by this subscriber, which may
     * be embedded as generic protobuf.Any data in the expected event.
     *
     * @return string[]
     */
    public function uses() : array
    {
        if ($this->uses === null) {
            throw new \BadMethodCallException(__METHOD__ . ' not yet implemented!');
        }
        
        return $this->uses;
    }
}
