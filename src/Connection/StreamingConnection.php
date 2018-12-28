<?php
declare(strict_types = 1);


namespace SmartWeb\Nats\Connection;

use NatsStreaming\Connection;
use NatsStreaming\Msg;
use NatsStreaming\Subscription;
use NatsStreaming\SubscriptionOptions;
use NatsStreaming\TrackedNatsRequest;
use SmartWeb\Nats\Event\Serialization\EventDecoder;
use SmartWeb\Nats\Message\Acknowledge;
use SmartWeb\Nats\Message\Message;
use SmartWeb\Nats\Message\MessageInterface;
use SmartWeb\Nats\Subscriber\SubscriberInterface;
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Serializer\SerializerInterface;

/**
 * Adapter for {@link NatsStreaming\Connection}, enabling interaction using CloudEvents event specification.
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
     * StreamingConnectionAdapter constructor.
     *
     * @param Connection          $connection
     * @param SerializerInterface $payloadSerializer
     */
    public function __construct(
        Connection $connection,
        SerializerInterface $payloadSerializer
    ) {
        $this->connection = $connection;
        $this->payloadSerializer = $payloadSerializer;
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
        $payload = $this->payloadSerializer->serialize($event, JsonEncoder::FORMAT);
        
        return $this->connection->publish($channel, $payload);
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
     * @param SubscriberInterface $subscriber
     *
     * @return \Closure
     */
    private function createSubscriberCallback(SubscriberInterface $subscriber) : \Closure
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
        return $this->deserializeEvent($message->getData(), $subscriber->expects());
    }
    
    /**
     * @param string $messageData
     * @param string $type
     *
     * @return object
     */
    public function deserializeEvent(string $messageData, string $type)
    {
        return $this->payloadSerializer->deserialize(
            $messageData,
            $type,
            EventDecoder::FORMAT
        );

//        if ($event instanceof EventInterface) {
//            return $event;
//        }
//
//        throw new UnexpectedValueException(
//            'The deserialized payload object must be an instance of ' . EventInterface::class
//        );
    }
}
