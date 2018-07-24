<?php
declare(strict_types = 1);


namespace SmartWeb\Nats\Message;

/**
 * Class Message
 */
class Message implements MessageInterface
{
    
    /**
     * @var MessageType
     */
    private $type;
    
    /**
     * @var string
     */
    private $content;
    
    /**
     * Message constructor.
     *
     * @param MessageType $type
     * @param string      $content
     */
    public function __construct(MessageType $type, string $content)
    {
        $this->type = $type;
        $this->content = $content;
    }
    
    /**
     * @inheritDoc
     */
    public function getType() : MessageType
    {
        return $this->type;
    }
    
    /**
     * @inheritDoc
     */
    public function getContent() : string
    {
        return $this->content;
    }
}
