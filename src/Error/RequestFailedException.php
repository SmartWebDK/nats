<?php
declare(strict_types = 1);


namespace SmartWeb\Nats\Error;

use SmartWeb\Events\EventInterface;

/**
 * Thrown when a simulated request/reply operation over NATS failed.
 *
 * @author Nicolai Agersbæk <na@zitcom.dk>
 *
 * @api
 */
class RequestFailedException extends \RuntimeException implements ExceptionInterface
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
