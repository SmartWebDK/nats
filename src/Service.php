<?php
declare(strict_types = 1);


namespace SmartWeb\Nats;

use SmartWeb\Nats\Connection\ConnectionInterface;
use SmartWeb\Nats\Publisher\PublishableMessage;
use SmartWeb\Nats\Publisher\PublishableMessageInterface;
use SmartWeb\Nats\Publisher\Publisher;
use SmartWeb\Nats\Publisher\PublisherInterface;
use Symfony\Component\Serializer\Encoder\JsonEncode;

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
     * @var PublisherInterface
     */
    private $publisher;
    
    /**
     * Service constructor.
     *
     * @param ConnectionInterface $connection
     */
    public function __construct(ConnectionInterface $connection)
    {
        $this->connection = $connection;
        $this->publisher = new Publisher($connection, new JsonEncode());
    }
    
    /**
     * @inheritDoc
     * @throws \Nats\Exception
     * @throws \Exception
     */
    public function run()
    {
        $this->connection->connect();
        
        $payload = [
            'someField' => 'someValue'
        ];
        
        $message = new PublishableMessage('someSubject', $payload);
        
        $this->runPublisherTest($message, 3);
        
        // Publish Subscribe
//        $this->runPublishSubscribeExample();
        
        // Request Response
//        $this->runRequestResponseExample();
        
        $this->connection->close();
    }

//    private function runRequestResponseExample()
//    {
//        $this->subscribe();
//        $this->request();
//        $this->connection->wait(1);
//    }
//
//    /**
//     * @return string
//     */
//    private function subscribe() : string
//    {
//        return $this->connection->subscribe(
//            'sayhello',
//            function ($message) {
//                $message->reply('Reply: Hello, ' . $message->getBody() . ' !!!');
//            }
//        );
//    }
//
//    private function request()
//    {
//        $this->connection->request(
//            'sayhello',
//            'Marty McFly',
//            function ($message) {
//                echo $message->getBody();
//            }
//        );
//    }
//
//    /**
//     * @throws \Nats\Exception
//     */
//    private function runPublishSubscribeExample()
//    {
//        $this->runSubscriberTest();
//        $this->runPublisherTest();
//
//        $this->connection->wait(1);
//    }
//
//    private function runSubscriberTest()
//    {
//        $this->connection->subscribe(
//            'foo',
//            function ($message) {
//                printf("Data: %s\r\n", $message->getBody());
//            }
//        );
//    }
    
    /**
     * @param PublishableMessageInterface $message
     * @param int|null                    $count
     *
     * @return self
     */
    private function runPublisherTest(PublishableMessageInterface $message, int $count = null) : self
    {
        $count = $count ?? 1;
        
        for ($i = 0; $i < $count; $i++) {
            $this->publisher->publish($message);
        }
        
        return $this;
    }
}
