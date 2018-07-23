<?php
declare(strict_types = 1);


namespace SmartWeb\Nats;

/**
 * Interface ArrayableInterface
 *
 * @api
 */
interface ArrayableInterface
{
    
    /**
     * @return array
     */
    public function toArray() : array;
}
