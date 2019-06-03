<?php
declare(strict_types = 1);


namespace SmartWeb\Nats\Subscriber;

use SmartWeb\Nats\Message\Acknowledge;

/**
 * Definition of a subscriber of events from NATS Streaming.
 *
 * @author Nicolai Agersbæk <na@smartweb.dk>
 *
 * @api
 */
interface SubscriberInterface extends NatsSubscriberInterface
{
    
    /**
     * Get the acknowledge behavior for this subscriber.
     * This determines when the subscriber will acknowledge a message from NATS.
     *
     * @return Acknowledge
     */
    public function acknowledge() : Acknowledge;
}
