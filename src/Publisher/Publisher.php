<?php
declare(strict_types = 1);


namespace SmartWeb\Nats\Publisher;

use SmartWeb\Nats\Connection\ConnectionInterface;
use SmartWeb\Nats\Message\Message;
use SmartWeb\Nats\Message\MessageInterface;

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
     * Publisher constructor.
     *
     * @param ConnectionInterface $connection
     */
    public function __construct(ConnectionInterface $connection)
    {
        $this->connection = $connection;
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
        $serializedContext = $message->getContext()->serialize();
        
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
