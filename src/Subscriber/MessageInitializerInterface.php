<?php
declare(strict_types = 1);


namespace SmartWeb\Nats\Subscriber;

/**
 * Initializes messages for use in generic protobuf.Any context.
 *
 * @author Nicolai Agersbæk <na@zitcom.dk>
 *
 * @api
 */
interface MessageInitializerInterface
{
    
    /**
     * @param string[] $types
     */
    public function initialize(array $types) : void;
}
