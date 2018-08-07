<?php
declare(strict_types = 1);


namespace SmartWeb\Nats\Connection;

use NatsStreaming\Connection;
use NatsStreaming\Subscription;
use NatsStreaming\SubscriptionOptions;
use NatsStreaming\TrackedNatsRequest;
use SmartWeb\Nats\Channel\ChannelInterface;
use SmartWeb\Nats\Payload\PayloadInterface;
use SmartWeb\Nats\Payload\Serialization\PayloadSerializerInterface;
use SmartWeb\Nats\Subscriber\SubscriberInterface;

/**
 * Adapter for {@link NatsStreaming\Connection}, enabling interaction using CloudEvents payload specification.
 *
 * @api
 */
class StreamingConnection implements ConnectionInterface
{
    
    /**
     * @var Connection
     */
    private $connection;
    
    /**
     * @var PayloadSerializerInterface
     */
    private $serializer;
    
    /**
     * StreamingConnectionAdapter constructor.
     *
     * @param Connection                 $connection
     * @param PayloadSerializerInterface $serializer
     */
    public function __construct(Connection $connection, PayloadSerializerInterface $serializer)
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
