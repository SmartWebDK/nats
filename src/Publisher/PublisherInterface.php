<?php
declare(strict_types = 1);


namespace SmartWeb\Nats\Publisher;

/**
 * Interface PublisherInterface
 *
 * @api
 */
interface PublisherInterface
{
    
    /**
     * @param PublishablePayloadInterface $payload
     */
    public function publish(PublishablePayloadInterface $payload) : void;
}
