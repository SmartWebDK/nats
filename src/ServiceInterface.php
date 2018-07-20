<?php
declare(strict_types = 1);


namespace SmartWeb\NATS;

/**
 * Interface ServiceInterface
 */
interface ServiceInterface
{
    
    /**
     * Boot the service.
     */
    public function boot();
    
    /**
     * Run the service.
     */
    public function run();
}
