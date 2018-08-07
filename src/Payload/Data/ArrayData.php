<?php
declare(strict_types = 1);


namespace SmartWeb\Nats\Payload\Data;

/**
 * TODO: Missing class description.
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
