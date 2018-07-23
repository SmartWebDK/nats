<?php
declare(strict_types = 1);


namespace SmartWeb\Nats\Publisher;

use SmartWeb\Nats\Connection\ConnectionInterface;

/**
 * Class Publisher
 *
 * @api
 */
class Publisher implements PublisherInterface
{
    
    /**
     * @var ConnectionInterface
     */
    private $connection;
    
    /**
     * Publisher constructor.
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
    public function publish(PublishablePayloadInterface $payload) : void
    {
        $this->connection->send($payload);
    }
}
