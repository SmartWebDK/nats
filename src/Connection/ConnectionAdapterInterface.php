<?php
declare(strict_types = 1);


namespace SmartWeb\Nats\Connection;

use NatsStreaming\Subscription;
use NatsStreaming\SubscriptionOptions;
use NatsStreaming\TrackedNatsRequest;
use SmartWeb\Nats\Channel\ChannelInterface;
use SmartWeb\Nats\Payload\PayloadInterface;
use SmartWeb\Nats\Subscriber\SubscriberInterface;

/**
 * Interface ConnectionAdapterInterface
 *
 * @api
 */
interface ConnectionAdapterInterface
{
    
    /**
     * @param ChannelInterface $channel
     * @param PayloadInterface $payload
     *
     * @return TrackedNatsRequest
     */
    public function publish(ChannelInterface $channel, PayloadInterface $payload) : TrackedNatsRequest;
    
    /**
     * @param ChannelInterface    $channels
     * @param SubscriberInterface $subscriber
     * @param SubscriptionOptions $subscriptionOptions
     *
     * @return Subscription
     */
    public function subscribe(
        ChannelInterface $channels,
        SubscriberInterface $subscriber,
        SubscriptionOptions $subscriptionOptions
    ) : Subscription;
}
