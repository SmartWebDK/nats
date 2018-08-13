<?php
declare(strict_types = 1);


namespace SmartWeb\Nats\Message;

use NatsStreaming\Msg;

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
     * @var Msg
     */
    private $msg;
    
    /**
     * Message constructor.
     *
     * @param Msg $msg
     */
    public function __construct(Msg $msg)
    {
        $this->sequence = $msg->getSequence();
        $this->subject = $msg->getSubject();
        $this->data = $msg->getData()->getContents();
        $this->timestamp = $msg->getTimestamp();
        $this->msg = $msg;
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
    
    public function acknowledge() : void
    {
        $this->msg->ack();
    }
}
