<?php
declare(strict_types = 1);


namespace SmartWeb\Nats\Subscriber;

use SmartWeb\CloudEvents\Nats\Event\EventInterface;

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
     * @param EventInterface $event
     */
    public function handle(EventInterface $event) : void;
}
