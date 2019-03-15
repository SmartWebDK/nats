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
use SmartWeb\Nats\Event\Factory\ResponseInfoResolver;
use SmartWeb\Nats\Event\Factory\ResponseInfoResolverInterface;
use SmartWeb\Nats\Event\Serialization\EventDecoder;
use SmartWeb\Nats\Message\Acknowledge;
use SmartWeb\Nats\Message\Message;
use SmartWeb\Nats\Message\MessageInterface;
use SmartWeb\Nats\Subscriber\MessageInitializer;
use SmartWeb\Nats\Subscriber\MessageInitializerInterface;
use SmartWeb\Nats\Subscriber\SubscriberInterface;
use SmartWeb\Nats\Subscriber\UsesProtobufAnyInterface;
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Serializer\SerializerInterface;

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
     * @var Connection
     */
    private $connection;
    
    /**
     * @var SerializerInterface
     */
    private $payloadSerializer;
    
    /**
     * @var MessageInitializerInterface
     */
    private $initializer;
    
    /**
     * @var ResponseInfoResolverInterface
     */
    private $responseInfoResolver;
    
    /**
     * StreamingConnectionAdapter constructor.
     *
     * @param Connection                         $connection
     * @param SerializerInterface                $payloadSerializer
     * @param MessageInitializerInterface|null   $initializer
     * @param ResponseInfoResolverInterface|null $responseInfoResolver
     */
    public function __construct(
        Connection $connection,
        SerializerInterface $payloadSerializer,
        ?MessageInitializerInterface $initializer = null,
        ?ResponseInfoResolverInterface $responseInfoResolver = null
    ) {
        $this->connection = $connection;
        $this->payloadSerializer = $payloadSerializer;
        $this->initializer = $initializer ?? new MessageInitializer();
        $this->responseInfoResolver = $responseInfoResolver ?? ResponseInfoResolver::default();
    }
    
    /**
     * Publish a payload on the given channel.
     *
     * @param string $channel Channel to publish the payload on.
     * @param object $event   Concrete event to use as payload for the message.
     *
     * @return TrackedNatsRequest
     */
    public function publish(string $channel, $event) : TrackedNatsRequest
    {
        $payload = $this->serializeEvent($event);
        
        return $this->connection->publish($channel, $payload);
    }
    
    /**
     * @param object $event
     *
     * @return string
     */
    private function serializeEvent($event) : string
    {
        return $event instanceof ProtobufMessage
            ? $event->serializeToString()
            : $this->payloadSerializer->serialize($event, JsonEncoder::FORMAT);
    }
    
    /**
     * Register an event subscriber on the given channel.
     *
     * @param string              $channel
     * @param SubscriberInterface $subscriber
     * @param SubscriptionOptions $subscriptionOptions
     *
     * @return Subscription
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
     * @throws \RuntimeException Occurs if the request could not be published to NATS
     */
    public function request(EventInterface $event, SubscriberInterface $responseHandler) : void
    {
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
        $trR = $this->publish($event->getEventType(), $event);
        
        // If publishing the request fails, we unregister the response handler
        // and throw to ensure client code is made aware of the issue.
        if (!$trR->wait()) {
            $sub->unsubscribe();
            
            $errMsg = \sprintf('Failed to publish event: %s (%s)', $event->getEventType(), $event->getEventId());
            throw new \RuntimeException($errMsg);
        }
        
        // To guard against possible future updates to the underlying NATS
        // package, we explicitly provide the number of message to wait for.
        /** @noinspection ArgumentEqualsDefaultValueInspection */
        $sub->wait(1);
    }
    
    /**
     * @param SubscriberInterface $subscriber
     *
     * @return \Closure
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
     */
    public function deserializeMessage(MessageInterface $message, SubscriberInterface $subscriber)
    {
        $messageData = $message->getData();
        $type = $subscriber->expects();
        
        if ($this->shouldDeserializeAsProtobuf($type)) {
            $this->initializeUses($subscriber);
            
            return $this->deserializeProtobufMessage($messageData, $type);
        }
        
        return $this->payloadSerializer->deserialize(
            $messageData,
            $type,
            EventDecoder::FORMAT
        );
    }
    
    /**
     * @param SubscriberInterface $subscriber
     */
    private function initializeUses(SubscriberInterface $subscriber) : void
    {
        $uses = $subscriber instanceof UsesProtobufAnyInterface
            ? $subscriber->uses()
            : [];
        
        $this->initializer->initialize($uses);
    }
    
    /**
     * @param string $type
     *
     * @return bool
     */
    public function shouldDeserializeAsProtobuf(string $type) : bool
    {
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
