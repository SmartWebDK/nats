<?php
declare(strict_types = 1);


namespace SmartWeb\Nats\Support;

/**
 * Definition of an object that can be represented as an array.
 */
interface ArrayableInterface
{
    
    /**
     * The array representation of this object.
     *
     * @return array
     */
    public function toArray() : array;
}
