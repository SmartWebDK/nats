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
    public function initialize(array $types) : void
    {
        $uninitialized = \array_diff($types, self::$initialized);
        
        foreach ($uninitialized as $type) {
            new $type();
            self::$initialized[$type] = $type;
        }
    }
}
