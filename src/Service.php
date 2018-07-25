<?php
declare(strict_types = 1);


namespace SmartWeb\Nats;

use Nats\ConnectionOptions;
use NatsStreaming\Connection;
use NatsStreaming\ConnectionOptions as StreamingConnectionOptions;
use NatsStreaming\Subscription;
use NatsStreaming\SubscriptionOptions;
use NatsStreamingProtos\StartPosition;
use SmartWeb\CloudEvents\Version;
use SmartWeb\Nats\Channel\Channel;
use SmartWeb\Nats\Connection\ConnectionAdapterInterface;
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
     * @var string
     */
    private static $defaultChannelName = 'some.channel';
    
    /**
     * @var ConnectionOptions
     */
    private $natsConnectionOptions;
    
    /**
     * @var string
     */
    private $name;
    
    /**
     * @inheritDoc
     */
    public function getName() : string
    {
        return $this->name;
    }
    
    /**
     * Service constructor.
     *
     * @param string            $name
     * @param ConnectionOptions $natsConnectionOptions
     */
    public function __construct(string $name, ConnectionOptions $natsConnectionOptions)
    {
        $this->natsConnectionOptions = $natsConnectionOptions;
        $this->name = $name;
    }
    
    public function run() : void
    {
        // TODO: Implement run() method.
        throw new \BadMethodCallException(__METHOD__ . ' not yet implemented!');
    }
    
    /**
     * @return string
     */
    private function getClusterID() : string
    {
        return \getenv(ConnectionAdapterInterface::CLUSTER_ID_KEY);
    }
    
    /**
     * @return string
     */
    private function getClientID() : string
    {
        return \str_replace('.', '', \uniqid($this->getName(), true));
    }
    
    /**
     * @param string|null $clientID
     *
     * @throws \NatsStreaming\Exceptions\ConnectException
     * @throws \NatsStreaming\Exceptions\TimeoutException
     */
    public function runSimplePublishTest(string $clientID = null) : void
    {
        $connection = $this->createConnection($clientID);
        $connection->connect();
        
        $subject = self::$defaultChannelName;
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
     * @param string|null $channelName
     *
     * @throws \NatsStreaming\Exceptions\ConnectException
     * @throws \NatsStreaming\Exceptions\TimeoutException
     */
    public function runPublishTest(string $channelName = null) : void
    {
        $connection = $this->createConnection();
        $connection->connect();
        
        $adapter = new StreamingConnectionAdapter($connection, $this->getSerializer());
        
        $channel = new Channel($channelName ?? self::$defaultChannelName);
        
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
     * @param string $channel
     *
     * @throws \NatsStreaming\Exceptions\ConnectException
     * @throws \NatsStreaming\Exceptions\TimeoutException
     */
    public function runSimpleSubscribeTest(string $channel = null) : void
    {
        $connection = $this->createConnection();
        $connection->connect();
        
        $subOptions = new SubscriptionOptions();
        $subOptions->setStartAt(StartPosition::NewOnly());
        
        $subject = $channel ?? self::$defaultChannelName;
        $callback = function ($message) {
            \printf($message);
        };
        
        $sub = $connection->subscribe($subject, $callback, $subOptions);
        $sub->wait(1);
        
        $connection->close();
    }
    
    /**
     * @param string[] $channels
     *
     * @throws \NatsStreaming\Exceptions\ConnectException
     * @throws \NatsStreaming\Exceptions\TimeoutException
     */
    public function runMultipleChannelSimpleSubscribeTest(string ...$channels) : void
    {
        $connection = $this->createConnection();
        $connection->connect();
        
        $subOptions = new SubscriptionOptions();
        $subOptions->setStartAt(StartPosition::NewOnly());
        
        $callback = function ($message) {
            \printf($message);
        };
        
        $subjects = empty($channels)
            ? [self::$defaultChannelName]
            : $channels;
        
        /** @var Subscription[] $subs */
        $subs = [];
        
        foreach ($subjects as $subject) {
            $sub = $connection->subscribe($subject, $callback, $subOptions);
            $subs[] = $sub;
        }
        
        foreach ($subs as $sub) {
            $sub->wait(1);
        }
        
        $connection->close();
    }
    
    /**
     * @throws \NatsStreaming\Exceptions\ConnectException
     * @throws \NatsStreaming\Exceptions\TimeoutException
     */
    public function runSubscribeTest() : void
    {
        $connection = $this->createConnection();
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
     * @param string|null $clientID
     *
     * @throws \NatsStreaming\Exceptions\ConnectException
     * @throws \NatsStreaming\Exceptions\TimeoutException
     */
    public function runSimpleQueueGroupSubscribeTest(string $clientID = null) : void
    {
        $connection = $this->createConnection($clientID);
        
        $connection->connect();
        
        $subOptions = new SubscriptionOptions();
        $subOptions->setStartAt(StartPosition::NewOnly());
        
        $subjects = 'some.channel';
        $queue = 'some.queue';
        $callback = function ($message) {
            \printf($message);
        };
        
        $sub = $connection->queueSubscribe($subjects, $queue, $callback, $subOptions);
        
        $sub->wait(1);
        
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
     * @param string|null $clientID
     *
     * @return Connection
     */
    private function createConnection(string $clientID = null) : Connection
    {
        $options = new StreamingConnectionOptions();
        
        $options->setNatsOptions($this->natsConnectionOptions);
        $options->setClientID($clientID ?? $this->getClientID());
        $options->setClusterID($this->getClusterID());
        
        return new Connection($options);
    }
}
