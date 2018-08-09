<?php
declare(strict_types = 1);


namespace SmartWeb\Nats\Connection;

use NatsStreaming\Connection;
use NatsStreaming\Subscription;
use NatsStreaming\SubscriptionOptions;
use NatsStreaming\TrackedNatsRequest;
use SmartWeb\Nats\Message\Message;
use SmartWeb\Nats\Message\MessageInterface;
use SmartWeb\Nats\Message\Serialization\MessageDecoder;
use SmartWeb\Nats\Payload\Payload;
use SmartWeb\Nats\Payload\PayloadInterface;
use SmartWeb\Nats\Payload\Serialization\PayloadDecoder;
use SmartWeb\Nats\Subscriber\SubscriberInterface;
use SmartWeb\Nats\Support\DeserializerInterface;
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Serializer\Exception\UnexpectedValueException;
use Symfony\Component\Serializer\SerializerInterface;

/**
 * Adapter for {@link NatsStreaming\Connection}, enabling interaction using CloudEvents payload specification.
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
     * @var DeserializerInterface
     */
    private $messageDeserializer;
    
    /**
     * @var SerializerInterface
     */
    private $payloadSerializer;
    
    /**
     * StreamingConnectionAdapter constructor.
     *
     * @param Connection            $connection
     * @param DeserializerInterface $messageDeserializer
     * @param SerializerInterface   $payloadSerializer
     */
    public function __construct(
        Connection $connection,
        DeserializerInterface $messageDeserializer,
        SerializerInterface $payloadSerializer
    ) {
        $this->connection = $connection;
        $this->messageDeserializer = $messageDeserializer;
        $this->payloadSerializer = $payloadSerializer;
    }
    
    /**
     * @inheritDoc
     */
    public function publish(string $channel, PayloadInterface $payload) : TrackedNatsRequest
    {
        return $this->connection->publish(
            $channel,
            $this->payloadSerializer->serialize($payload, JsonEncoder::FORMAT)
        );
    }
    
    /**
     * @inheritDoc
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
     * @inheritDoc
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
     * @return callable
     */
    private function createSubscriberCallback(SubscriberInterface $subscriber) : callable
    {
        return function (string $message) use ($subscriber): void {
            $subscriber->handle($this->deserializeMessage($message));
        };
    }
    
    /**
     * @param string $payload
     *
     * @return PayloadInterface
     */
    private function deserializeMessage(string $payload) : PayloadInterface
    {
        $msgObject = $this->messageDeserializer->deserialize(
            $payload,
            Message::class,
            MessageDecoder::FORMAT
        );
        
        if ($msgObject instanceof MessageInterface) {
            return $this->deserializePayload($msgObject->getData());
        }
        
        throw new UnexpectedValueException(
            'The deserialized message object must be an instance of ' . MessageInterface::class
        );
    }
    
    /**
     * @param string $payload
     *
     * @return PayloadInterface
     */
    private function deserializePayload(string $payload) : PayloadInterface
    {
        $payloadObject = $this->payloadSerializer->deserialize(
            $payload,
            Payload::class,
            PayloadDecoder::FORMAT
        );
        
        if ($payloadObject instanceof PayloadInterface) {
            return $payloadObject;
        }
        
        throw new UnexpectedValueException(
            'The deserialized payload object must be an instance of ' . PayloadInterface::class
        );
    }
}
