<?php
declare(strict_types = 1);


namespace SmartWeb\Nats\Publisher;

use SmartWeb\Nats\Message\MessageType;

/**
 * Class PublishableMessage
 */
class PublishableMessage implements PublishableMessageInterface
{
    
    /**
     * @var string
     */
    private $subject;
    
    /**
     * @var array
     */
    private $context;
    
    /**
     * @var null|string
     */
    private $inbox;
    
    /**
     * PublishablePayload constructor.
     *
     * @param string      $subject
     * @param array       $context
     * @param null|string $inbox
     */
    public function __construct(string $subject, array $context, string $inbox = null)
    {
        $this->subject = $subject;
        $this->context = $context;
        $this->inbox = $inbox;
    }
    
    /**
     * @inheritDoc
     */
    public function getType() : MessageType
    {
        return MessageType::PUB();
    }
    
    /**
     * @inheritDoc
     */
    public function getSubject() : string
    {
        return $this->subject;
    }
    
    /**
     * @inheritDoc
     */
    public function getContext() : array
    {
        return $this->context;
    }
    
    /**
     * @inheritDoc
     */
    public function getInbox() : ?string
    {
        return $this->inbox;
    }
}
