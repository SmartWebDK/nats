<?php
declare(strict_types = 1);


namespace SmartWeb\Nats\Payload\Serialization;

use SmartWeb\Nats\Payload\PayloadInterface;

/**
 * Interface SerializerInterface
 */
interface PayloadSerializerInterface
{
    
    /**
     * @param PayloadInterface $payload
     *
     * @return string
     */
    public function serialize(PayloadInterface $payload) : string;
    
    /**
     * @param string $serialized
     *
     * @return PayloadInterface
     */
    public function deserialize(string $serialized) : PayloadInterface;
}
