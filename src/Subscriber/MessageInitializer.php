<?php
declare(strict_types = 1);


namespace SmartWeb\Nats\Subscriber;

/**
 * Initializes messages for use in generic protobuf.Any context.
 *
 * @author Nicolai Agersbæk <na@zitcom.dk>
 */
class MessageInitializer implements MessageInitializerInterface
{
    
    /**
     * @var string[]
     */
    private static $initialized = [];
    
    /**
     * @param string[] $types
     */
    public function initialize(string ...$types) : void
    {
        // FIXME: Missing tests!
        $uninitialized = \array_diff($types, self::$initialized);
        
        foreach ($uninitialized as $type) {
            $this->initializeType($type);
        }
    }
    
    /**
     * @param string $type
     */
    private function initializeType(string $type) : void
    {
        new $type();
        self::$initialized[$type] = $type;
    }
}
