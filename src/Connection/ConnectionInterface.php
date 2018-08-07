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
 * Definition of a NATS streaming connection enabling interaction using CloudEvents payload specification.
 *
 * @api
 */
interface ConnectionInterface
{
    
    /**
     * Environment variable used for storing the cluster ID for connecting to the NATS server.
     */
    public const CLUSTER_ID_KEY = 'NATS_CLUSTER_ID';
    
    /**
     * Publish a payload on the given channel.
     *
     * @param ChannelInterface $channel
     * @param PayloadInterface $payload
     *
     * @return TrackedNatsRequest
     */
    public function publish(ChannelInterface $channel, PayloadInterface $payload) : TrackedNatsRequest;
    
    /**
     * Register a subscriber on the given channel.
     *
     * @param ChannelInterface    $channel
     * @param SubscriberInterface $subscriber
     * @param SubscriptionOptions $subscriptionOptions
     *
     * @return Subscription
     */
    public function subscribe(
        ChannelInterface $channel,
        SubscriberInterface $subscriber,
        SubscriptionOptions $subscriptionOptions
    ) : Subscription;
    
    /**
     * Register a subscriber on the given channel in the given queue group.
     *
     * @param ChannelInterface    $channel
     * @param string              $group
     * @param SubscriberInterface $subscriber
     * @param SubscriptionOptions $subscriptionOptions
     *
     * @return Subscription
     */
    public function groupSubscribe(
        ChannelInterface $channel,
        string $group,
        SubscriberInterface $subscriber,
        SubscriptionOptions $subscriptionOptions
    ) : Subscription;
}
