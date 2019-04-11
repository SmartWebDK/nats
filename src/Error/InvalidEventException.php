<?php
declare(strict_types = 1);


namespace SmartWeb\Nats\Error;

use SmartWeb\Events\EventInterface;

/**
 * Thrown when an invalid event is provided as input to a method.
 *
 * @author Nicolai Agersbæk <na@zitcom.dk>
 *
 * @api
 */
class InvalidEventException extends \DomainException implements ExceptionInterface
{
    
    /**
     * @var EventInterface
     */
    private $event;
    
    /**
     * @param EventInterface $event
     * @param string|null    $message
     */
    public function __construct(EventInterface $event, ?string $message = null)
    {
        parent::__construct($message ?? $message);
        
        $this->event = $event;
    }
    
    /**
     * @return EventInterface
     */
    public function getEvent() : EventInterface
    {
        return $this->event;
    }
}
