<?php
declare(strict_types = 1);


namespace SmartWeb\Nats\Connection;

use NatsStreaming\Connection;
use NatsStreaming\Subscription;
use NatsStreaming\SubscriptionOptions;
use NatsStreaming\TrackedNatsRequest;
use SmartWeb\Nats\Channel\ChannelGroupInterface;
use SmartWeb\Nats\Channel\ChannelInterface;
use SmartWeb\Nats\Encoding\DecoderInterface;
use SmartWeb\Nats\Encoding\EncoderInterface;
use SmartWeb\Nats\PayloadInterface;
use SmartWeb\Nats\SubscriberInterface;

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
     * @var EncoderInterface
     */
    private $encoder;
    
    /**
     * @var DecoderInterface
     */
    private $decoder;
    
    /**
     * StreamingConnectionAdapter constructor.
     *
     * @param Connection       $connection
     * @param EncoderInterface $encoder
     * @param DecoderInterface $decoder
     */
    public function __construct(Connection $connection, EncoderInterface $encoder, DecoderInterface $decoder)
    {
        $this->connection = $connection;
        $this->encoder = $encoder;
        $this->decoder = $decoder;
    }
    
    /**
     * @inheritDoc
     */
    public function publish(ChannelInterface $channel, PayloadInterface $payload) : TrackedNatsRequest
    {
        return $this->connection->publish($channel->getName(), $this->encoder->encode($payload));
    }
    
    /**
     * @inheritDoc
     */
    public function subscribe(
        ChannelGroupInterface $channels,
        SubscriberInterface $subscriber,
        SubscriptionOptions $subscriptionOptions
    ) : Subscription {
        return $this->connection->subscribe($channels, $this->getSubscriberCallback($subscriber), $subscriptionOptions);
    }
    
    /**
     * @param SubscriberInterface $subscriber
     *
     * @return callable
     */
    private function getSubscriberCallback(SubscriberInterface $subscriber) : callable
    {
        return function (string $payload) use ($subscriber): void {
            $subscriber->handle($this->decoder->decode($payload));
        };
    }
}
