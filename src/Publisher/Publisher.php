<?php
declare(strict_types = 1);


namespace SmartWeb\Nats\Publisher;

use Nats\Encoders\Encoder;
use SmartWeb\Nats\Connection\ConnectionInterface;
use SmartWeb\Nats\Message\Message;
use SmartWeb\Nats\Message\MessageInterface;
use Symfony\Component\Serializer\Encoder\EncoderInterface;
use Symfony\Component\Serializer\Encoder\JsonEncoder;

/**
 * Class Publisher
 *
 * @api
 */
class Publisher implements PublisherInterface
{
    
    /**
     * @var ConnectionInterface
     */
    private $connection;
    
    /**
     * @var EncoderInterface
     */
    private $encoder;
    
    /**
     * Publisher constructor.
     *
     * @param ConnectionInterface $connection
     * @param EncoderInterface    $encoder
     */
    public function __construct(ConnectionInterface $connection, EncoderInterface $encoder)
    {
        $this->connection = $connection;
        $this->encoder = $encoder;
    }
    
    /**
     * @inheritDoc
     */
    public function publish(PublishableMessageInterface $message) : PublisherInterface
    {
        $this->connection->send($this->createMessage($message));
        
        return $this;
    }
    
    /**
     * @param PublishableMessageInterface $message
     *
     * @return MessageInterface
     */
    private function createMessage(PublishableMessageInterface $message) : MessageInterface
    {
        return new Message(
            PublishableMessageInterface::MESSAGE_TYPE,
            $this->createMessageContent($message)
        );
    }
    
    /**
     * @param PublishableMessageInterface $message
     *
     * @return string
     */
    private function createMessageContent(PublishableMessageInterface $message) : string
    {
        $serializedContext = $this->encoder->encode($message->getContext(), JsonEncoder::FORMAT);
        
        $content = [
            // $message::MESSAGE_TYPE,
            $message->getSubject(),
            $message->getInbox(),
            \strlen($serializedContext),
        ];
        
        $headers = \implode(' ', \array_filter($content));
        
        return "{$headers}\r\n{$serializedContext}";
    }
}
