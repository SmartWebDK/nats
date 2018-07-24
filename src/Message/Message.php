<?php
declare(strict_types = 1);


namespace SmartWeb\Nats\Message;

/**
 * Class Message
 */
class Message implements MessageInterface
{
    
    /**
     * @var string
     */
    private $type;
    
    /**
     * @var string
     */
    private $content;
    
    /**
     * Message constructor.
     *
     * @param string $type
     * @param string $content
     */
    public function __construct(string $type, string $content)
    {
        $this->type = $type;
        $this->content = $content;
    }
    
    /**
     * @inheritDoc
     */
    public function getType() : string
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
