<?php
declare(strict_types = 1);


namespace SmartWeb\Nats\Connection;

use NatsStreaming\Connection;
use NatsStreaming\Msg;
use NatsStreaming\Subscription;
use NatsStreaming\SubscriptionOptions;
use NatsStreaming\TrackedNatsRequest;
use SmartWeb\CloudEvents\Nats\Event\Event;
use SmartWeb\CloudEvents\Nats\Event\EventInterface;
use SmartWeb\Nats\Event\Serialization\EventDecoder;
use SmartWeb\Nats\Message\Acknowledge;
use SmartWeb\Nats\Message\Message;
use SmartWeb\Nats\Message\MessageInterface;
use SmartWeb\Nats\Subscriber\EventSubscriberInterface;
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Serializer\Exception\UnexpectedValueException;
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
     * @inheritDoc
     */
    public function publish(string $channel, EventInterface $event) : TrackedNatsRequest
    {
        return $this->connection->publish(
            $channel,
            $this->payloadSerializer->serialize($event, JsonEncoder::FORMAT)
        );
    }
    
    /**
     * @inheritDoc
     */
    public function subscribe(
        string $channel,
        EventSubscriberInterface $subscriber,
        SubscriptionOptions $subscriptionOptions
    ) : Subscription {
        return $this->connection->subscribe(
            $channel,
            $this->createSubscriberCallback($subscriber),
            $subscriptionOptions
        );
    }
    
    /**
     * @inheritDoc
     */
    public function groupSubscribe(
        string $channel,
        string $group,
        EventSubscriberInterface $subscriber,
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
     * @param EventSubscriberInterface $subscriber
     *
     * @return callable
     */
    private function createSubscriberCallback(EventSubscriberInterface $subscriber) : callable
    {
        return function (Msg $msg) use ($subscriber): void {
            $message = new Message($msg);
            
            if ($subscriber->acknowledge() === Acknowledge::before()) {
                $msg->ack();
            }
            
            $subscriber->handle($this->deserializeMessage($message));
            
            if ($subscriber->acknowledge() === Acknowledge::after()) {
                $msg->ack();
            }
        };
    }
    
    /**
     * @param MessageInterface $message
     *
     * @return EventInterface
     */
    private function deserializeMessage(MessageInterface $message) : EventInterface
    {
        return $this->deserializeEvent($message->getData());
    }
    
    /**
     * @param string $messageData
     *
     * @return EventInterface
     */
    private function deserializeEvent(string $messageData) : EventInterface
    {
        $event = $this->payloadSerializer->deserialize(
            $messageData,
            Event::class,
            EventDecoder::FORMAT
        );
        
        if ($event instanceof EventInterface) {
            return $event;
        }
        
        throw new UnexpectedValueException(
            'The deserialized payload object must be an instance of ' . EventInterface::class
        );
    }
}
