<?php
declare(strict_types = 1);


namespace SmartWeb\Nats\Error;

/**
 * Thrown when provided with a message type that is not compatible with Protobuf.
 *
 * @author Nicolai Agersbæk <na@zitcom.dk>
 *
 * @api
 */
class InvalidTypeException extends \DomainException implements ExceptionInterface
{
    
    /**
     * @var string
     */
    private $type;
    
    /**
     * @param string      $type
     * @param string|null $message
     */
    public function __construct(string $type, ?string $message = null)
    {
        parent::__construct($message ?? $message);
        
        $this->type = $type;
    }
    
    /**
     * @return string
     */
    public function getType() : string
    {
        return $this->type;
    }
}
