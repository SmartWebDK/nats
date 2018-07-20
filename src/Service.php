<?php
declare(strict_types = 1);


namespace SmartWeb\NATS;

use Nats\Connection;
use Nats\ConnectionOptions;

/**
 * Class Service
 */
class Service implements ServiceInterface
{
    
    /**
     * @var Connection
     */
    private $client;
    
    /**
     * @inheritDoc
     */
    public function boot()
    {
        $options = new ConnectionOptions(
            [
                'port' => 4222,
            ]
        );
        $this->client = new Connection();
    }
    
    /**
     * @inheritDoc
     * @throws \Nats\Exception
     * @throws \Exception
     */
    public function run()
    {
        $this->client->connect();
        
        // Publish Subscribe
        
        // Simple Subscriber.
        $this->client->subscribe(
            'foo',
            function ($message) {
                printf("Data: %s\r\n", $message->getBody());
            }
        );
        
        // Simple Publisher.
        $this->client->publish('foo', 'Marty McFly');
        
        // Wait for 1 message.
        $this->client->wait(1);
        
        // Request Response
        
        // Responding to requests.
        $sid = $this->client->subscribe(
            'sayhello',
            function ($message) {
                $message->reply('Reply: Hello, ' . $message->getBody() . ' !!!');
            }
        );
        
        // Request.
        $this->client->request(
            'sayhello',
            'Marty McFly',
            function ($message) {
                echo $message->getBody();
            }
        );
        
        // Wait for 1 message.
        $this->client->wait(1);
        
        $this->client->close();
    }
}
