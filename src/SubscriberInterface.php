<?php
declare(strict_types = 1);


namespace SmartWeb\Nats;

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
