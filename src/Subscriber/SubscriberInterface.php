<?php
declare(strict_types = 1);


namespace SmartWeb\Nats\Subscriber;

use SmartWeb\Nats\Message\Acknowledge;

/**
 * Definition of a subscriber of events from NATS.
 *
 * @author Nicolai Agersbæk <na@smartweb.dk>
 *
 * @api
 */
interface SubscriberInterface
{
    
    /**
     * Handle the event.
     * The class of the provided event **MUST** be an instance of the expected
     * event class for this subscriber, given by `SubscriberInterface::expects()`.
     *
     * @param object $event Event to handle.
     *
     * @see \SmartWeb\Nats\Subscriber\SubscriberInterface::expects()
     */
    public function handle($event) : void;
    
    /**
     * Get the fully-qualified class name of events expected by this subscriber.
     * This **MUST** be an instantiable class, which means that interfaces or
     * abstract classes are never considered valid.
     *
     * @return string
     */
    public function expects() : string;
    
    /**
     * Get the acknowledge behavior for this subscriber.
     * This determines when the subscriber will acknowledge a message from NATS.
     *
     * @return Acknowledge
     */
    public function acknowledge() : Acknowledge;
}
