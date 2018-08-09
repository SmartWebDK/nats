<?php
declare(strict_types = 1);


namespace SmartWeb\Nats\Payload\Data;

/**
 * Implementation of payload data using an underlying array.
 *
 * @author Nicolai Agersbæk <na@smartweb.dk>
 *
 * @internal
 */
class ArrayData implements PayloadDataInterface
{
    
    /**
     * @var array
     */
    private $data;
    
    /**
     * @param array $data
     */
    public function __construct(array $data)
    {
        $this->data = $data;
    }
    
    /**
     * @inheritDoc
     */
    public function jsonSerialize() : array
    {
        return $this->data;
    }
}
