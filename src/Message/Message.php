<?php
declare(strict_types = 1);


namespace SmartWeb\Nats\Message;

/**
 * Message received from a NATS connection.
 *
 * @author Nicolai Agersbæk <na@smartweb.dk>
 *
 * @api
 */
class Message implements MessageInterface
{
    
    /**
     * @var int
     */
    private $sequence;
    
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
     * @param int    $sequence
     * @param string $subject
     * @param string $data
     * @param int    $timestamp
     */
    public function __construct(int $sequence, string $subject, string $data, int $timestamp)
    {
        $this->sequence = $sequence;
        $this->subject = $subject;
        $this->data = $data;
        $this->timestamp = $timestamp;
    }
    
    /**
     * @inheritDoc
     */
    public function getSequence() : int
    {
        return $this->sequence;
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
