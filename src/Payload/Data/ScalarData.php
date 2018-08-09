<?php
declare(strict_types = 1);


namespace SmartWeb\Nats\Payload\Data;

use SmartWeb\Nats\Exception\InvalidArgumentException;

/**
 * Implementation of payload data using an scalar value.
 *
 * @author Nicolai Agersbæk <na@smartweb.dk>
 *
 * @internal
 */
class ScalarData implements PayloadDataInterface
{
    
    /**
     * @var bool|float|int|string
     */
    private $data;
    
    /**
     * @param bool|float|int|string $data
     */
    public function __construct($data)
    {
        $this->validateData($data);
        
        $this->data = $data;
    }
    
    /**
     * @param mixed $data
     */
    private function validateData($data) : void
    {
        if (!\is_scalar($data)) {
            $actualType = \gettype($data);
            throw new InvalidArgumentException("Data must be a scalar value; was '{$actualType}'");
        }
    }
    
    /**
     * @inheritDoc
     */
    public function jsonSerialize()
    {
        return $this->data;
    }
}
