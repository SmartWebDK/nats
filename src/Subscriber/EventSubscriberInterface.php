<?php
declare(strict_types = 1);


namespace SmartWeb\Nats\Subscriber;

use SmartWeb\CloudEvents\Nats\Event\EventInterface;

/**
 * Definition of a class capable of subscribing to CloudEvents events from NATS.
 *
 * @author Nicolai Agersbæk <na@smartweb.dk>
 *
 * @api
 */
interface EventSubscriberInterface
{
    
    /**
     * @param EventInterface $event
     */
    public function handle(EventInterface $event) : void;
}
