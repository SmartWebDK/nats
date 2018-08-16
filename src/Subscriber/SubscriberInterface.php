<?php
declare(strict_types = 1);


namespace SmartWeb\Nats\Subscriber;

use SmartWeb\CloudEvents\Nats\Event\EventInterface;
use SmartWeb\Nats\Message\Acknowledge;

/**
 * Definition of a subscriber of CloudEvents events from NATS.
 *
 * @author Nicolai Agersbæk <na@smartweb.dk>
 *
 * @api
 */
interface SubscriberInterface
{
    
    /**
     * @param EventInterface $event
     */
    public function handle(EventInterface $event) : void;
    
    /**
     * @return Acknowledge
     */
    public function acknowledge() : Acknowledge;
}
