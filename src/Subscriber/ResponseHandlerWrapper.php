<?php
declare(strict_types = 1);


namespace SmartWeb\Nats\Subscriber;

use SmartWeb\Events\EventInterface;
use SmartWeb\Nats\Message\Acknowledge;

/**
 * Wraps an existing subscriber for use in response handling.
 *
 * @author Nicolai Agersbæk <na@zitcom.dk>
 */
class ResponseHandlerWrapper implements SubscriberInterface
{
    
    /**
     * @var EventInterface
     */
    private $event;
    
    /**
     * @var SubscriberInterface
     */
    private $subscriber;
    
    /**
     * @param EventInterface      $event
     * @param SubscriberInterface $subscriber
     */
    public function __construct(EventInterface $event, SubscriberInterface $subscriber)
    {
        $this->event = $event;
        $this->subscriber = $subscriber;
    }
    
    /**
     * @param EventInterface $event
     *
     * @return string
     */
    private function getExpectedResponseId(EventInterface $event) : string
    {
        return "response.{$event->getEventId()}";
    }
    
    /**
     * Handle the event.
     * The class of the provided event **MUST** be an instance of the expected
     * event class for this subscriber, given by {@see \SmartWeb\Nats\Subscriber\SubscriberInterface::expects() expects}.
     *
     * @param object $event Event to handle.
     */
    public function handle($event) : void
    {
        if ($this->shouldHandle($event)) {
            $this->subscriber->handle($event);
        }
    }
    
    /**
     * @param object $response
     *
     * @return bool
     */
    private function shouldHandle($response) : bool
    {
        return ($response instanceof EventInterface)
               && $response->getEventId() === $this->getExpectedResponseId($this->event);
    }
    
    /**
     * Get the fully-qualified class name of events expected by this subscriber.
     * This **MUST** be an instantiable class, which means that interfaces or
     * abstract classes are never considered valid.
     *
     * @return string
     */
    public function expects() : string
    {
        return $this->subscriber->expects();
    }
    
    /**
     * Get the acknowledge behavior for this subscriber.
     * This determines when the subscriber will acknowledge a message from NATS.
     *
     * @return Acknowledge
     */
    public function acknowledge() : Acknowledge
    {
        return $this->subscriber->acknowledge();
    }
}
