<?php
declare(strict_types = 1);


namespace SmartWeb\NATS;

use Nats\Connection;

/**
 * Class Service
 */
class Service implements ServiceInterface
{
    
    /**
     * @var Connection
     */
    private $connection;
    
    /**
     * Service constructor.
     *
     * @param Connection $connection
     */
    public function __construct(Connection $connection)
    {
        $this->connection = $connection;
    }
    
    /**
     * @inheritDoc
     * @throws \Nats\Exception
     * @throws \Exception
     */
    public function run()
    {
        $this->connection->connect();
        
        // Publish Subscribe
        
        // Simple Subscriber.
        $this->connection->subscribe(
            'foo',
            function ($message) {
                printf("Data: %s\r\n", $message->getBody());
            }
        );
        
        // Simple Publisher.
        $this->connection->publish('foo', 'Marty McFly');
        
        // Wait for 1 message.
        $this->connection->wait(1);
        
        // Request Response
        
        // Responding to requests.
        $sid = $this->connection->subscribe(
            'sayhello',
            function ($message) {
                $message->reply('Reply: Hello, ' . $message->getBody() . ' !!!');
            }
        );
        
        // Request.
        $this->connection->request(
            'sayhello',
            'Marty McFly',
            function ($message) {
                echo $message->getBody();
            }
        );
        
        // Wait for 1 message.
        $this->connection->wait(1);
        
        $this->connection->close();
    }
}
