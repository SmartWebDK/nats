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
     * @var int
     */
    private $index;
    
    /**
     * @var string
     */
    private $subject;
    
    /**
     * @var string
     */
    private $data;
    
    /**
     * @var int
     */
    private $timestamp;
    
    /**
     * Message constructor.
     *
     * @param int    $index
     * @param string $subject
     * @param string $data
     * @param int    $timestamp
     */
    public function __construct(int $index, string $subject, string $data, int $timestamp)
    {
        $this->index = $index;
        $this->subject = $subject;
        $this->data = $data;
        $this->timestamp = $timestamp;
    }
    
    /**
     * @inheritDoc
     */
    public function getIndex() : int
    {
        return $this->index;
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
    public function getData() : string
    {
        return $this->data;
    }
    
    /**
     * @inheritDoc
     */
    public function getTimestamp() : int
    {
        return $this->timestamp;
    }
}
