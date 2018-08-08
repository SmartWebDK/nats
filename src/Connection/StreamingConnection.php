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
use SmartWeb\Nats\Payload\Serialization\PayloadDenormalizer;
use SmartWeb\Nats\Payload\Serialization\PayloadNormalizer;
use SmartWeb\Nats\Subscriber\SubscriberInterface;
use Symfony\Component\Serializer\Encoder\JsonEncode;
use Symfony\Component\Serializer\Encoder\JsonEncoder;

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
     * @var PayloadNormalizer
     */
    private $payloadNormalizer;
    
    /**
     * @var JsonEncode
     */
    private $payloadEncoder;
    
    /**
     * @var MessageDecoder
     */
    private $messageDecoder;
    
    /**
     * @var MessageDenormalizer
     */
    private $messageDenormalizer;
    
    /**
     * @var PayloadDecoder
     */
    private $payloadDecoder;
    
    /**
     * @var PayloadDenormalizer
     */
    private $payloadDenormalizer;
    
    /**
     * StreamingConnectionAdapter constructor.
     *
     * @param Connection          $connection
     * @param PayloadNormalizer   $payloadNormalizer
     * @param JsonEncode          $payloadEncoder
     * @param MessageDecoder      $messageDecoder
     * @param MessageDenormalizer $messageDenormalizer
     * @param PayloadDecoder      $payloadDecoder
     * @param PayloadDenormalizer $payloadDenormalizer
     */
    public function __construct(
        Connection $connection,
        PayloadNormalizer $payloadNormalizer,
        JsonEncode $payloadEncoder,
        MessageDecoder $messageDecoder,
        MessageDenormalizer $messageDenormalizer,
        PayloadDecoder $payloadDecoder,
        PayloadDenormalizer $payloadDenormalizer
    ) {
        $this->connection = $connection;
        $this->payloadNormalizer = $payloadNormalizer;
        $this->payloadEncoder = $payloadEncoder;
        $this->messageDecoder = $messageDecoder;
        $this->messageDenormalizer = $messageDenormalizer;
        $this->payloadDecoder = $payloadDecoder;
        $this->payloadDenormalizer = $payloadDenormalizer;
    }

//    /**
//     * StreamingConnectionAdapter constructor.
//     *
//     * @param Connection $connection
//     * @param DeserializerInterface $messageDeserializer
//     * @param SerializerInterface $messageSerializer
//     * @param SerializerInterface $payloadSerializer
//     */
//    public function __construct(
//        Connection $connection,
//        DeserializerInterface $messageDeserializer,
//        SerializerInterface $messageSerializer,
//        SerializerInterface $payloadSerializer
//    ) {
//        $this->connection = $connection;
//        $this->messageDeserializer = $messageDeserializer;
//        $this->messageSerializer = $messageSerializer;
//        $this->payloadSerializer = $payloadSerializer;
//    }
    
    /**
     * @inheritDoc
     */
    public function publish(ChannelInterface $channel, PayloadInterface $payload) : TrackedNatsRequest
    {
        return $this->connection->publish(
            $channel->getName(),
            $this->serializePayload($payload)
        );
    }
    
    /**
     * @param PayloadInterface $payload
     *
     * @return string
     */
    private function serializePayload(PayloadInterface $payload) : string
    {
        return $this->payloadEncoder->encode($this->payloadNormalizer->normalize($payload), JsonEncoder::FORMAT);
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
        
        $payloadString = $msgObject->getData();
        
        $payloadArray = $this->payloadDecoder->decode($payloadString, PayloadDecoder::FORMAT);
        $payloadObject = $this->payloadDenormalizer->denormalize($payloadArray, Payload::class);
        
        return $payloadObject;
    }
}
