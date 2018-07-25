<?php
declare(strict_types = 1);


namespace SmartWeb\Nats;

use Nats\ConnectionOptions;
use NatsStreaming\Connection;
use NatsStreaming\ConnectionOptions as StreamingConnectionOptions;
use NatsStreaming\SubscriptionOptions;
use NatsStreamingProtos\StartPosition;
use SmartWeb\CloudEvents\Version;
use SmartWeb\Nats\Channel\Channel;
use SmartWeb\Nats\Connection\StreamingConnectionAdapter;
use SmartWeb\Nats\Encoding\PayloadDenormalizer;
use SmartWeb\Nats\Encoding\PayloadNormalizer;
use SmartWeb\Nats\Encoding\PayloadSerializer;
use SmartWeb\Nats\Encoding\SerializerInterface;
use SmartWeb\Nats\Payload\PayloadBuilder;
use SmartWeb\Nats\Subscriber\SubscriberTest;
use Symfony\Component\Serializer\Encoder\JsonDecode;
use Symfony\Component\Serializer\Encoder\JsonEncode;

/**
 * Class Service
 */
class Service implements ServiceInterface
{
    
    /**
     * @var ConnectionOptions
     */
    private $natsConnectionOptions;
    
    /**
     * Service constructor.
     *
     * @param ConnectionOptions $natsConnectionOptions
     */
    public function __construct(ConnectionOptions $natsConnectionOptions)
    {
        $this->natsConnectionOptions = $natsConnectionOptions;
    }
    
    public function run() : void
    {
        // TODO: Implement run() method.
        throw new \BadMethodCallException(__METHOD__ . ' not yet implemented!');
    }
    
    /**
     * @param string      $clusterID
     * @param null|string $clientID
     *
     * @throws \NatsStreaming\Exceptions\ConnectException
     * @throws \NatsStreaming\Exceptions\TimeoutException
     */
    public function runSimplePublishTest(string $clusterID, ?string $clientID = null) : void
    {
        $options = new StreamingConnectionOptions(
            [
                'natsOptions' => $this->natsConnectionOptions,
            ]
        );
        
        $clientID = $clientID ?? (string)\mt_rand();
        $options->setClientID($clientID);
        $options->setClusterID($clusterID);
        
        $connection = new Connection($options);
        
        $connection->connect();
        
        $subject = 'some.subject';
        $data = 'Foo!';
        
        $r = $connection->publish($subject, $data);
        
        $gotAck = $r->wait();
        
        $statusResponse = $gotAck
            ? 'Acknowledged'
            : 'Not acknowledged';
        
        \printf("$statusResponse\r\n");
        
        $connection->close();
    }
    
    /**
     * @param string      $clusterID
     * @param null|string $clientID
     *
     * @throws \NatsStreaming\Exceptions\ConnectException
     * @throws \NatsStreaming\Exceptions\TimeoutException
     */
    public function runPublishTest(string $clusterID, ?string $clientID = null) : void
    {
        $connection = $this->createConnection($clusterID, $clientID);
        $connection->connect();
        
        $adapter = new StreamingConnectionAdapter($connection, $this->getSerializer());
        
        $channel = new Channel('some.channel');
        
        $data = [
            'foo' => 'bar',
        ];
        $payload = PayloadBuilder::create()
                                 ->setEventType('some.event')
                                 ->setCloudEventsVersion(new Version(0, 1, 0))
                                 ->setSource('some.source')
                                 ->setEventId('some.event.id')
                                 ->setData($data)
                                 ->build();
        
        $request = $adapter->publish($channel, $payload);
        
        $gotAck = $request->wait();
        
        $statusResponse = $gotAck
            ? 'Acknowledged'
            : 'Not acknowledged';
        
        \printf("$statusResponse\r\n");
        
        $connection->close();
    }
    
    /**
     * @param string      $clusterID
     * @param null|string $clientID
     *
     * @throws \NatsStreaming\Exceptions\ConnectException
     * @throws \NatsStreaming\Exceptions\TimeoutException
     */
    public function runSimpleSubscribeTest(string $clusterID, ?string $clientID = null) : void
    {
        $options = new StreamingConnectionOptions(
            [
                'natsOptions' => $this->natsConnectionOptions,
            ]
        );
    
        $clientID = $clientID ?? (string)\mt_rand();
        $options->setClientID($clientID);
        $options->setClusterID($clusterID);
    
        $connection = new Connection($options);
    
        $connection->connect();
    
        $subOptions = new SubscriptionOptions();
        $subOptions->setStartAt(StartPosition::NewOnly());
    
        $subjects = 'some.channel';
        $callback = function ($message) {
            \printf($message);
        };
    
        $sub = $connection->subscribe($subjects, $callback, $subOptions);
    
        $sub->wait(2);
    
        // not explicitly needed
        $sub->unsubscribe(); // or $sub->close();
    
        $connection->close();
    }
    
    /**
     * @param string      $clusterID
     * @param null|string $clientID
     *
     * @throws \NatsStreaming\Exceptions\ConnectException
     * @throws \NatsStreaming\Exceptions\TimeoutException
     */
    public function runSubscribeTest(string $clusterID, ?string $clientID = null) : void
    {
        $connection = $this->createConnection($clusterID, $clientID);
        $connection->connect();
    
        $subOptions = new SubscriptionOptions();
        $subOptions->setStartAt(StartPosition::NewOnly());
    
        $adapter = new StreamingConnectionAdapter($connection, $this->getSerializer());
    
        $channel = new Channel('some.channel');
    
        $subscriber = new SubscriberTest();
    
        $subscription = $adapter->subscribe($channel, $subscriber, $subOptions);
        $subscription->wait(1);
    
        // not explicitly needed
        $subscription->unsubscribe(); // or $sub->close();
    
        $connection->close();
    }
    
    /**
     * @param string      $clusterID
     * @param null|string $clientID
     *
     * @throws \NatsStreaming\Exceptions\ConnectException
     * @throws \NatsStreaming\Exceptions\TimeoutException
     */
    public function runSimpleQueueGroupSubscribeTest(string $clusterID, ?string $clientID = null) : void
    {
        $options = new StreamingConnectionOptions(
            [
                'natsOptions' => $this->natsConnectionOptions,
            ]
        );
    
        $clientID = $clientID ?? (string)\mt_rand();
        $options->setClientID($clientID);
        $options->setClusterID($clusterID);
    
        $connection = new Connection($options);
    
        $connection->connect();
    
        $subOptions = new SubscriptionOptions();
        $subOptions->setStartAt(StartPosition::NewOnly());
    
        $subjects = 'some.channel';
        $queue = 'some.queue';
        $callback = function ($message) {
            \printf($message);
        };
    
        $sub = $connection->queueSubscribe($subjects, $queue, $callback, $subOptions);
    
    
        $sub->wait(2);

        // not explicitly needed
        $sub->close(); // or $sub->unsubscribe();
    
        $connection->close();
    
    }
    
    public function runQueueGroupSubscribeTest() : void
    {
        // TODO: Implement run() method.
        throw new \BadMethodCallException(__METHOD__ . ' not yet implemented!');
    }
    
    /**
     * @return SerializerInterface
     */
    private function getSerializer() : SerializerInterface
    {
        $normalizer = new PayloadNormalizer();
        $denormalizer = new PayloadDenormalizer();
        $encoder = new JsonEncode();
        $decoder = new JsonDecode();
        
        return new PayloadSerializer($normalizer, $denormalizer, $encoder, $decoder);
    }
    
    /**
     * @param string      $clusterID
     * @param string|null $clientID
     *
     * @return Connection
     */
    private function createConnection(string $clusterID, string $clientID = null) : Connection
    {
        $options = new StreamingConnectionOptions(
            [
                'natsOptions' => $this->natsConnectionOptions,
            ]
        );
        
        $clientID = $clientID ?? (string)\mt_rand();
        $options->setClientID($clientID);
        $options->setClusterID($clusterID);
        
        return new Connection($options);
    }
}
