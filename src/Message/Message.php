<?php
declare(strict_types = 1);


namespace SmartWeb\Nats\Message;

/**
 * Class Message
 *
 * @api
 */
class Message implements MessageInterface
{
    
    /**
     * @var string
     */
    private $content;
    
    /**
     * Message constructor.
     *
     * @param string $content
     */
    public function __construct(string $content)
    {
        $this->content = $content;
    }
    
    /**
     * @inheritDoc
     */
    public function getContent() : string
    {
        return $this->content;
    }
}
