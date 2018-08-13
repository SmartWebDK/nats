<?php
declare(strict_types = 1);


namespace SmartWeb\Nats\Subscriber;

use NatsStreaming\Msg;

/**
 * Definition of a class capable of subscribing to raw NATS messages.
 *
 * @author Nicolai Agersbæk <na@smartweb.dk>
 *
 * @api
 */
interface ManualSubscriberInterface
{
    
    /**
     * @param Msg $message
     */
    public function handle(Msg $message) : void;
}
