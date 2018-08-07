<?php
declare(strict_types = 1);


namespace SmartWeb\Nats\Support;

/**
 * Definition of an object with a string representation.
 *
 * @api
 */
interface StringConvertibleInterface
{
    
    /**
     * The string representation of this object.
     *
     * @return string
     */
    public function __toString() : string;
}
