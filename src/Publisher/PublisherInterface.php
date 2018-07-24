<?php
declare(strict_types = 1);


namespace SmartWeb\Nats\Publisher;

/**
 * Interface PublisherInterface
 *
 * @api
 */
interface PublisherInterface
{
    
    /**
     * @param PublishableMessageInterface $message
     *
     * @return self
     */
    public function publish(PublishableMessageInterface $message) : self;
}
