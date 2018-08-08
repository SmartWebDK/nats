<?php
declare(strict_types = 1);


namespace SmartWeb\Nats\Connection;

use NatsStreaming\Connection;
use NatsStreaming\Subscription;
use NatsStreaming\SubscriptionOptions;
use NatsStreaming\TrackedNatsRequest;
use SmartWeb\Nats\Channel\ChannelInterface;
use SmartWeb\Nats\Message\Message;
use SmartWeb\Nats\Message\Serialization\MessageDecoder;
use SmartWeb\Nats\Message\Serialization\MessageDenormalizer;
use SmartWeb\Nats\Payload\Payload;
use SmartWeb\Nats\Payload\PayloadInterface;
use SmartWeb\Nats\Payload\Serialization\PayloadDecoder;
use SmartWeb\Nats\Subscriber\SubscriberInterface;
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Serializer\SerializerInterface;

/**
 * Adapter for {@link NatsStreaming\Connection}, enabling interaction using CloudEvents payload specification.
 *
 * @api
 */
class StreamingConnection implements ConnectionInterface
{
    
    /**
     * @var Connection
     */
    private $connection;
    
    /**
     * @var MessageDecoder
     */
    private $messageDecoder;
    
    /**
     * @var MessageDenormalizer
     */
    private $messageDenormalizer;
    
    /**
     * @var SerializerInterface
     */
    private $payloadSerializer;
    
    /**
     * StreamingConnectionAdapter constructor.
     *
     * @param Connection          $connection
     * @param MessageDecoder      $messageDecoder
     * @param MessageDenormalizer $messageDenormalizer
     * @param SerializerInterface $payloadSerializer
     */
    public function __construct(
        Connection $connection,
        MessageDecoder $messageDecoder,
        MessageDenormalizer $messageDenormalizer,
        SerializerInterface $payloadSerializer
    ) {
        $this->connection = $connection;
        $this->messageDecoder = $messageDecoder;
        $this->messageDenormalizer = $messageDenormalizer;
        $this->payloadSerializer = $payloadSerializer;
    }
    
    /**
     * @inheritDoc
     */
    public function publish(ChannelInterface $channel, PayloadInterface $payload) : TrackedNatsRequest
    {
        return $this->connection->publish(
            $channel->getName(),
            $this->payloadSerializer->serialize($payload, JsonEncoder::FORMAT)
        );
    }
    
    /**
     * @inheritDoc
     */
    public function subscribe(
        ChannelInterface $channel,
        SubscriberInterface $subscriber,
        SubscriptionOptions $subscriptionOptions
    ) : Subscription {
        return $this->connection->subscribe(
            $channel->getName(),
            $this->createSubscriberCallback($subscriber),
            $subscriptionOptions
        );
    }
    
    /**
     * @inheritDoc
     */
    public function groupSubscribe(
        ChannelInterface $channel,
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
        $msgArray = $this->messageDecoder->decode($payload, MessageDecoder::FORMAT);
        $msgObject = $this->messageDenormalizer->denormalize($msgArray, Message::class);
        
        return $this->deserializePayload($msgObject->getData());
    }
    
    /**
     * @param string $payload
     *
     * @return PayloadInterface
     */
    private function deserializePayload(string $payload) : PayloadInterface
    {
        $payloadObject = $this->payloadSerializer->deserialize($payload, Payload::class, PayloadDecoder::FORMAT);
        
        if ($payloadObject instanceof PayloadInterface) {
            return $payloadObject;
        }
        
        throw new \LogicException('The deserialized payload object must be an instance of ' . PayloadInterface::class);
    }
}
