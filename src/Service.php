<?php
declare(strict_types = 1);


namespace SmartWeb\Nats;

use SmartWeb\Nats\Connection\ConnectionInterface;

/**
 * Class Service
 */
class Service implements ServiceInterface
{
    
    /**
     * @var ConnectionInterface
     */
    protected $connection;
    
    /**
     * Service constructor.
     *
     * @param ConnectionInterface $connection
     */
    public function __construct(ConnectionInterface $connection)
    {
        $this->connection = $connection;
    }
    
    /**
     * @inheritDoc
     */
    public function run() : void
    {
    }
}
