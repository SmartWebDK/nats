<?php
declare(strict_types = 1);


namespace SmartWeb\Nats\Message;

/**
 * Interface MessageTypeAwareInterface
 *
 * @api
 */
interface MessageTypeAwareInterface
{
    
    /**
     * Get the type of the message.
     *
     * @return MessageType
     */
    public function getType() : MessageType;
}
