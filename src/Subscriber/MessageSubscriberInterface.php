<?php
declare(strict_types = 1);


namespace SmartWeb\Nats\Subscriber;

use SmartWeb\Nats\Message\MessageInterface;

/**
 * Definition of a class capable of subscribing to raw NATS messages.
 *
 * @author Nicolai Agersbæk <na@smartweb.dk>
 *
 * @api
 */
interface MessageSubscriberInterface
{
    
    /**
     * @param MessageInterface $message
     */
    public function handle(MessageInterface $message) : void;
}
