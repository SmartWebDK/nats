<?php
declare(strict_types = 1);


namespace SmartWeb\Nats\Subscriber;

/**
 * Interface implemented by subscribers that depend on specific message types
 * when handling events containing protobuf.Any data.
 *
 * @author Nicolai Agersbæk <na@zitcom.dk>
 *
 * @api
 */
interface UsesProtobufAnyInterface
{
    
    /**
     * Complete list of all message classes used by this subscriber, which may
     * be embedded as generic protobuf.Any data in the expected event.
     *
     * @return string[]
     */
    public function uses() : array;
}
