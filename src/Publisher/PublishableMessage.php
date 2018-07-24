<?php
declare(strict_types = 1);


namespace SmartWeb\Nats\Publisher;

use SmartWeb\CloudEvents\Context\ContextInterface;

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
     * @var ContextInterface
     */
    private $context;
    
    /**
     * @var null|string
     */
    private $inbox;
    
    /**
     * PublishablePayload constructor.
     *
     * @param string           $subject
     * @param ContextInterface $context
     * @param null|string      $inbox
     */
    public function __construct(string $subject, ContextInterface $context, ?string $inbox)
    {
        $this->subject = $subject;
        $this->context = $context;
        $this->inbox = $inbox;
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
    public function getContext() : ContextInterface
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
