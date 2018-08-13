<?php
declare(strict_types = 1);


namespace SmartWeb\Nats\Connection;

use NatsStreaming\Subscription;
use NatsStreaming\SubscriptionOptions;
use NatsStreaming\TrackedNatsRequest;
use SmartWeb\CloudEvents\Nats\Event\EventInterface;
use SmartWeb\Nats\Subscriber\EventSubscriberInterface;
use SmartWeb\Nats\Subscriber\MessageSubscriberInterface;

/**
 * Definition of a NATS streaming connection enabling interaction using CloudEvents event specification.
 *
 * @author Nicolai Agersbæk <na@smartweb.dk>
 *
 * @api
 */
interface StreamingConnectionInterface
{
    
    /**
     * Environment variable used for storing the cluster ID for connecting to the NATS server.
     */
    public const CLUSTER_ID_KEY = 'NATS_CLUSTER_ID';
    
    /**
     * Publish a payload on the given channel.
     *
     * @param string         $channel
     * @param EventInterface $event
     *
     * @return TrackedNatsRequest
     */
    public function publish(string $channel, EventInterface $event) : TrackedNatsRequest;
    
    /**
     * Register an event subscriber on the given channel.
     *
     * @param string                   $channel
     * @param EventSubscriberInterface $subscriber
     * @param SubscriptionOptions      $subscriptionOptions
     *
     * @return Subscription
     */
    public function subscribe(
        string $channel,
        EventSubscriberInterface $subscriber,
        SubscriptionOptions $subscriptionOptions
    ) : Subscription;
    
    /**
     * Register an event subscriber on the given channel in the given queue group.
     *
     * @param string                   $channel
     * @param string                   $group
     * @param EventSubscriberInterface $subscriber
     * @param SubscriptionOptions      $subscriptionOptions
     *
     * @return Subscription
     */
    public function groupSubscribe(
        string $channel,
        string $group,
        EventSubscriberInterface $subscriber,
        SubscriptionOptions $subscriptionOptions
    ) : Subscription;
    
    /**
     * Register a message subscriber on the given channel.
     *
     * @param string                     $channel
     * @param MessageSubscriberInterface $subscriber
     * @param SubscriptionOptions        $subscriptionOptions
     *
     * @return Subscription
     */
    public function messageSubscribe(
        string $channel,
        MessageSubscriberInterface $subscriber,
        SubscriptionOptions $subscriptionOptions
    ) : Subscription;
    
    /**
     * Register a message subscriber on the given channel in the given queue group.
     *
     * @param string                     $channel
     * @param string                     $group
     * @param MessageSubscriberInterface $subscriber
     * @param SubscriptionOptions        $subscriptionOptions
     *
     * @return Subscription
     */
    public function messageGroupSubscribe(
        string $channel,
        string $group,
        MessageSubscriberInterface $subscriber,
        SubscriptionOptions $subscriptionOptions
    ) : Subscription;
}
