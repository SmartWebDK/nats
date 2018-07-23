<?php
declare(strict_types = 1);


namespace SmartWeb\Nats;

use Nats\Connection;

/**
 * Class Service
 */
class Service implements ServiceInterface
{
    
    /**
     * @var Connection
     */
    protected $connection;
    
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
        $this->runPublishSubscribeExample();
        
        // Request Response
        $this->runRequestResponseExample();
        
        $this->connection->close();
        
    }
    
    private function runRequestResponseExample()
    {
        $this->subscribe();
        $this->request();
        $this->connection->wait(1);
    }
    
    /**
     * @return string
     */
    private function subscribe() : string
    {
        return $this->connection->subscribe(
            'sayhello',
            function ($message) {
                $message->reply('Reply: Hello, ' . $message->getBody() . ' !!!');
            }
        );
    }
    
    private function request()
    {
        $this->connection->request(
            'sayhello',
            'Marty McFly',
            function ($message) {
                echo $message->getBody();
            }
        );
    }
    
    /**
     * @throws \Nats\Exception
     */
    private function runPublishSubscribeExample()
    {
        $this->runSubscriberTest();
        $this->runPublisherTest();
        
        $this->connection->wait(1);
    }
    
    private function runSubscriberTest()
    {
        $this->connection->subscribe(
            'foo',
            function ($message) {
                printf("Data: %s\r\n", $message->getBody());
            }
        );
    }
    
    /**
     * @throws \Nats\Exception
     */
    private function runPublisherTest()
    {
        $this->connection->publish('foo', 'Marty McFly');
    }
}
