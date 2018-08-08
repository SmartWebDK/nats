<?php
declare(strict_types = 1);


namespace SmartWeb\Nats\Tests\Fixtures\Payload\Data;

use SmartWeb\Nats\Payload\Data\PayloadDataInterface;

/**
 * @author Nicolai Agersbæk <na@smartweb.dk>
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
