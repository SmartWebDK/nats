<?php
/** @noinspection EfferentObjectCouplingInspection */
declare(strict_types = 1);


namespace SmartWeb\Nats\Connection;

use Google\Protobuf\Internal\Message as ProtobufMessage;
use NatsStreaming\Connection;
use NatsStreaming\Msg;
use NatsStreaming\Subscription;
use NatsStreaming\SubscriptionOptions;
use NatsStreaming\TrackedNatsRequest;
use NatsStreamingProtos\StartPosition;
use SmartWeb\Events\EventInterface;
use SmartWeb\Nats\Error\InvalidEventException;
use SmartWeb\Nats\Error\InvalidTypeException;
use SmartWeb\Nats\Error\RequestFailedException;
use SmartWeb\Nats\Event\Factory\ResponseInfoResolver;
use SmartWeb\Nats\Event\Factory\ResponseInfoResolverInterface;
use SmartWeb\Nats\Message\Acknowledge;
use SmartWeb\Nats\Message\Message;
use SmartWeb\Nats\Message\MessageInterface;
use SmartWeb\Nats\Subscriber\MessageInitializer;
use SmartWeb\Nats\Subscriber\MessageInitializerInterface;
use SmartWeb\Nats\Subscriber\SubscriberInterface;
use SmartWeb\Nats\Subscriber\UsesProtobufAnyInterface;

/**
 * Adapter for {@link NatsStreaming\Connection}, which makes interaction with NATS
 * using CloudEvents or ProtoBuf event specifications easier.
 *
 * @author Nicolai Agersbæk <na@smartweb.dk>
 *
 * @api
 */
class StreamingConnection implements StreamingConnectionInterface
{
    
    /**
     * Format used to create error message when receiving invalid event class.
     */
    private const INVALID_EVENT_CLS_MSG = 'Expected instance of %s; was %s';
    
    /**
     * Format used to create error message when publishing and event fails.
     */
    private const PUBLISH_FAILED_MSG = 'Failed to publish event: %s (%s)';
    
    /**
     * Error message used when receiving an event not compatible with Protobuf.
     */
    private const INVALID_MSG_TYPE_MSG = 'Invalid message type; expected instance of ' . ProtobufMessage::class;
    
    /**
     * @var Connection
     */
    private $connection;
    
    /**
     * @var MessageInitializerInterface
     */
    private $initializer;
    
    /**
     * @var ResponseInfoResolverInterface
     */
    private $responseInfoResolver;
    
    /**
     * @param Connection                         $connection
     * @param MessageInitializerInterface|null   $initializer
     * @param ResponseInfoResolverInterface|null $responseInfoResolver
     */
    public function __construct(
        Connection $connection,
        ?MessageInitializerInterface $initializer = null,
        ?ResponseInfoResolverInterface $responseInfoResolver = null
    ) {
        $this->connection = $connection;
        $this->initializer = $initializer ?? new MessageInitializer();
        $this->responseInfoResolver = $responseInfoResolver ?? ResponseInfoResolver::default();
    }
    
    /**
     * Publish a payload on the given channel.
     *
     * @param EventInterface $event Concrete event to publish.
     *
     * @return TrackedNatsRequest
     *
     * @throws InvalidEventException Occurs when the given event is not a valid Protobuf message.
     */
    public function publish(EventInterface $event) : TrackedNatsRequest
    {
        // FIXME: Missing tests!
        return $this->connection->publish(
            $event->getEventType(),
            $this->serializeEvent($event)
        );
    }
    
    /**
     * @param EventInterface $event
     *
     * @return string
     *
     * @throws InvalidEventException
     */
    private function serializeEvent(EventInterface $event) : string
    {
        // FIXME: Missing tests!
        if ($event instanceof ProtobufMessage) {
            return $event->serializeToString();
        }
    
        throw $this->invalidEventException($event);
    }
    
    /**
     * @param EventInterface $event
     *
     * @return InvalidEventException
     */
    private function invalidEventException(EventInterface $event) : InvalidEventException
    {
        $msg = \sprintf(
            self::INVALID_EVENT_CLS_MSG,
            ProtobufMessage::class,
            \get_class($event)
        );
        
        return new InvalidEventException($event, $msg);
    }
    
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
    ) : Subscription {
        return $this->connection->subscribe(
            $channel,
            $this->createSubscriberCallback($subscriber),
            $subscriptionOptions
        );
    }
    
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
    ) : Subscription {
        return $this->connection->queueSubscribe(
            $channel,
            $group,
            $this->createSubscriberCallback($subscriber),
            $subscriptionOptions
        );
    }
    
    /**
     * Performs a synchronous request, which expects a reply.
     *
     * @param EventInterface      $event
     * @param SubscriberInterface $responseHandler
     *
     * @throws RequestFailedException Occurs if the request could not be published to NATS.
     */
    public function request(EventInterface $event, SubscriberInterface $responseHandler) : void
    {
        // FIXME: Missing tests!
        // Set appropriate subscription options for a request/reply operation
        $subOptions = new SubscriptionOptions();
        $subOptions->setStartAt(StartPosition::NewOnly());
        $subOptions->setAckWaitSecs(5);
        $subOptions->setManualAck(true);
        
        // Register response handler
        $sub = $this->subscribe(
            $this->responseInfoResolver->getResponseChannel($event),
            $responseHandler,
            $subOptions
        );
        
        // Perform request
        $request = $this->publish($event);
        
        // If publishing the request fails, we unregister the response handler
        // and throw to ensure client code is made aware of the issue.
        if (!$request->wait()) {
            $sub->unsubscribe();
    
            throw $this->publishFailedException($event);
        }
        
        // To guard against possible future updates to the underlying NATS
        // package, we explicitly provide the number of message to wait for.
        /** @noinspection ArgumentEqualsDefaultValueInspection */
        $sub->wait(1);
    }
    
    /**
     * @param EventInterface $event
     *
     * @return RequestFailedException
     */
    private function publishFailedException(EventInterface $event) : RequestFailedException
    {
        $msg = \sprintf(
            self::PUBLISH_FAILED_MSG,
            $event->getEventType(),
            $event->getEventId()
        );
        
        return new RequestFailedException($event, $msg);
    }
    
    /**
     * @param SubscriberInterface $subscriber
     *
     * @return \Closure
     *
     * @throws InvalidTypeException Occurs if the given type is not Protobuf-compatible.
     */
    public function createSubscriberCallback(SubscriberInterface $subscriber) : \Closure
    {
        return function (Msg $msg) use ($subscriber): void {
            $message = new Message($msg);
            
            if ($subscriber->acknowledge() === Acknowledge::before()) {
                $msg->ack();
            }
            
            $event = $this->deserializeMessage($message, $subscriber);
            $subscriber->handle($event);
            
            if ($subscriber->acknowledge() === Acknowledge::after()) {
                $msg->ack();
            }
        };
    }
    
    /**
     * @param MessageInterface    $message
     * @param SubscriberInterface $subscriber
     *
     * @return object
     *
     * @throws InvalidTypeException Occurs if the given type is not Protobuf-compatible.
     */
    public function deserializeMessage(MessageInterface $message, SubscriberInterface $subscriber)
    {
        // FIXME: Missing tests!
        $type = $subscriber->expects();
    
        $this->validateMessageType($type);
        
        $messageData = $message->getData();
    
        $this->initializeUses($subscriber);
    
        return $this->deserializeProtobufMessage($messageData, $type);
    }
    
    /**
     * @param SubscriberInterface $subscriber
     */
    public function initializeUses(SubscriberInterface $subscriber) : void
    {
        // FIXME: Missing tests!
        $uses = $subscriber instanceof UsesProtobufAnyInterface
            ? $subscriber->uses()
            : [];
        
        $this->initializer->initialize($uses);
    }
    
    /**
     * @param string $type
     *
     * @throws InvalidTypeException Occurs if the given type is not Protobuf-compatible.
     */
    public function validateMessageType(string $type) : void
    {
        // FIXME: Missing tests!
        if (!$this->typeIsProtobufCompatible($type)) {
            throw new InvalidTypeException($type, self::INVALID_MSG_TYPE_MSG);
        }
    }
    
    /**
     * @param string $type
     *
     * @return bool
     */
    public function typeIsProtobufCompatible(string $type) : bool
    {
        // FIXME: Missing tests!
        return \is_a($type, ProtobufMessage::class, true);
    }
    
    /**
     * @param string $messageData
     * @param string $type
     *
     * @return object
     */
    public function deserializeProtobufMessage(string $messageData, string $type)
    {
        /** @var ProtobufMessage $msg */
        $msg = new $type();
    
        $msg->mergeFromString($messageData);
        
        return $msg;
    }
}
