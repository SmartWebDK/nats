<?php
declare(strict_types = 1);


namespace SmartWeb\Nats\Subscriber;

use SmartWeb\Nats\Payload\PayloadInterface;

/**
 * Definition of a class capable of subscribing to NATS messages.
 *
 * @author Nicolai Agersbæk <na@smartweb.dk>
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
