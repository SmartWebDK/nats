<?php
declare(strict_types = 1);


namespace SmartWeb\Nats;

/**
 * Interface ServiceInterface
 */
interface ServiceInterface
{
    
    /**
     * Run the service.
     */
    public function run() : void;
    
    /**
     * @return string
     */
    public function getName() : string;
}
