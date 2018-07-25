<?php
declare(strict_types = 1);


namespace SmartWeb\Nats\Subscriber;

use SmartWeb\Nats\Payload\PayloadInterface;

/**
 * Interface SubscriberInterface
 *
 * @api
 */
interface SubscriberInterface
{
    
    /**
     * @param PayloadInterface $payload
     */
    public function handle(PayloadInterface $payload) : void;
}
