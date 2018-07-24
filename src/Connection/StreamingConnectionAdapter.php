<?php
declare(strict_types = 1);


namespace SmartWeb\Nats\Connection;

use NatsStreaming\Connection;
use NatsStreaming\TrackedNatsRequest;
use SmartWeb\Nats\Channel\ChannelInterface;
use SmartWeb\Nats\Encoding\EncoderInterface;
use SmartWeb\Nats\PayloadInterface;

/**
 * Class StreamingConnectionAdapter
 */
class StreamingConnectionAdapter implements ConnectionAdapterInterface
{
    
    /**
     * @var Connection
     */
    private $connection;
    
    /**
     * @var EncoderInterface
     */
    private $encoder;
    
    /**
     * StreamingConnectionAdapter constructor.
     *
     * @param Connection       $connection
     * @param EncoderInterface $encoder
     */
    public function __construct(Connection $connection, EncoderInterface $encoder)
    {
        $this->connection = $connection;
        $this->encoder = $encoder;
    }
    
    /**
     * @param ChannelInterface $channel
     * @param PayloadInterface $payload
     *
     * @return TrackedNatsRequest
     */
    public function publish(ChannelInterface $channel, PayloadInterface $payload) : TrackedNatsRequest
    {
        return $this->connection->publish($channel->getName(), $this->encoder->encode($payload));
    }
}
