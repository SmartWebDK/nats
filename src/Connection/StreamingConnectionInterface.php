<?php
declare(strict_types = 1);


namespace SmartWeb\Nats\Connection;

use NatsStreaming\Subscription;
use NatsStreaming\SubscriptionOptions;
use NatsStreaming\TrackedNatsRequest;
use SmartWeb\Events\EventInterface;
use SmartWeb\Nats\Error\InvalidEventException;
use SmartWeb\Nats\Error\InvalidTypeException;
use SmartWeb\Nats\Error\RequestFailedException;
use SmartWeb\Nats\Subscriber\SubscriberInterface;

/**
 * Definition of a NATS streaming connection enabling interaction using events
 * compatible with the SmartWeb application environment.
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
     * Publish an event to NATS.
     * The channel on which to publish the payload is inferred by the type of
     * the given event.
     *
     * @param EventInterface $event Concrete event to publish.
     *
     * @return TrackedNatsRequest
     *
     * @throws InvalidEventException Occurs when the given event is not a valid Protobuf message.
     */
    public function publish(EventInterface $event) : TrackedNatsRequest;
    
    /**
     * Register an event subscriber on the given channel.
     *
     * @param string              $channel
     * @param SubscriberInterface $subscriber
     * @param SubscriptionOptions $subscriptionOptions
     *
     * @return Subscription
     *
     * @throws InvalidTypeException Occurs if the expected type of the given
     *                              subscriber is not Protobuf-compatible.
     */
    public function subscribe(
        string $channel,
        SubscriberInterface $subscriber,
        SubscriptionOptions $subscriptionOptions
    ) : Subscription;
    
    /**
     * Register an event subscriber on the given channel in the given queue group.
     *
     * @param string              $channel
     * @param string              $group
     * @param SubscriberInterface $subscriber
     * @param SubscriptionOptions $subscriptionOptions
     *
     * @return Subscription
     *
     * @throws InvalidTypeException Occurs if the expected type of the given
     *                              subscriber is not Protobuf-compatible.
     */
    public function groupSubscribe(
        string $channel,
        string $group,
        SubscriberInterface $subscriber,
        SubscriptionOptions $subscriptionOptions
    ) : Subscription;
    
    /**
     * Performs a synchronous request, which expects a reply.
     *
     * @param EventInterface      $event
     * @param SubscriberInterface $responseHandler
     *
     * @throws RequestFailedException Occurs if the request could not be published to NATS.
     */
    public function request(EventInterface $event, SubscriberInterface $responseHandler) : void;
}
