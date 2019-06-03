<?php
declare(strict_types = 1);


namespace SmartWeb\Nats\Connection;

use Nats\Exception;
use SmartWeb\Events\EventInterface;
use SmartWeb\Nats\Error\InvalidEventException;
use SmartWeb\Nats\Error\InvalidTypeException;
use SmartWeb\Nats\Error\RequestFailedException;
use SmartWeb\Nats\Subscriber\NatsSubscriberInterface as Subscriber;

/**
 * Definition of a NATS connection enabling interaction using events compatible
 * with the SmartWeb application environment.
 *
 * @author Nicolai Agersbæk <na@smartweb.dk>
 *
 * @api
 */
interface NatsConnectionInterface
{
    
    /**
     * Publish an event to NATS.
     * The channel on which to publish the payload is inferred by the type of
     * the given event.
     *
     * @param EventInterface $event   Concrete event to publish.
     * @param string|null    $channel [Optional] channel to publish on. Default
     *                                will be determined from the type of the given event.
     *
     * @throws InvalidEventException Occurs when the given event is not a valid Protobuf message.
     * @throws Exception Occurs if subscription not found.
     */
    public function publish(EventInterface $event, ?string $channel = null) : void;
    
    /**
     * Register an event subscriber on the given channel.
     *
     * @param string     $channel
     * @param Subscriber $subscriber
     *
     * @return string The SID of the subscription.
     *
     * @throws InvalidTypeException Occurs if the expected type of the given
     *                              subscriber is not Protobuf-compatible.
     */
    public function subscribe(string $channel, Subscriber $subscriber) : string;
    
    /**
     * Register an event subscriber on the given channel in the given queue group.
     *
     * @param string     $channel
     * @param string     $group
     * @param Subscriber $subscriber
     *
     * @return string The SID of the subscription.
     *
     * @throws InvalidTypeException Occurs if the expected type of the given
     *                              subscriber is not Protobuf-compatible.
     */
    public function groupSubscribe(
        string $channel,
        string $group,
        Subscriber $subscriber
    ) : string;
    
    /**
     * Performs a synchronous request, which expects a reply.
     *
     * @param EventInterface $event
     * @param Subscriber     $responseHandler
     *
     * @throws RequestFailedException Occurs if the request could not be published to NATS.
     */
    public function request(EventInterface $event, Subscriber $responseHandler) : void;
}
