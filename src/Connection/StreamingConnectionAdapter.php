<?php
declare(strict_types = 1);


namespace SmartWeb\Nats\Connection;

use NatsStreaming\Connection;
use NatsStreaming\Subscription;
use NatsStreaming\SubscriptionOptions;
use NatsStreaming\TrackedNatsRequest;
use SmartWeb\Nats\Channel\ChannelInterface;
use SmartWeb\Nats\Payload\PayloadInterface;
use SmartWeb\Nats\Payload\Serialization\SerializerInterface;
use SmartWeb\Nats\Subscriber\SubscriberInterface;

/**
 * Class StreamingConnectionAdapter
 *
 * @api
 */
class StreamingConnectionAdapter implements ConnectionAdapterInterface
{
    
    /**
     * @var Connection
     */
    private $connection;
    
    /**
     * @var SerializerInterface
     */
    private $serializer;
    
    /**
     * StreamingConnectionAdapter constructor.
     *
     * @param Connection $connection
     * @param SerializerInterface $serializer
     */
    public function __construct(Connection $connection, SerializerInterface $serializer)
    {
        $this->connection = $connection;
        $this->serializer = $serializer;
    }
    
    /**
     * @inheritDoc
     */
    public function publish(ChannelInterface $channel, PayloadInterface $payload) : TrackedNatsRequest
    {
        return $this->connection->publish($channel->getName(), $this->serializer->serialize($payload));
    }
    
    /**
     * @inheritDoc
     */
    public function subscribe(
        ChannelInterface $channels,
        SubscriberInterface $subscriber,
        SubscriptionOptions $subscriptionOptions
    ) : Subscription {
        return $this->connection->subscribe(
            $channels->getName(),
            $this->getSubscriberCallback($subscriber),
            $subscriptionOptions
        );
    }
    
    /**
     * @param SubscriberInterface $subscriber
     *
     * @return callable
     */
    private function getSubscriberCallback(SubscriberInterface $subscriber) : callable
    {
        return function (string $payload) use ($subscriber): void {
            $subscriber->handle($this->serializer->deserialize($payload));
        };
    }
}
