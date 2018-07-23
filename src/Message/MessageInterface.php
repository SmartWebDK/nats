<?php
declare(strict_types = 1);


namespace SmartWeb\Nats\Message;

/**
 * Interface MessageInterface
 *
 * @api
 */
interface MessageInterface
{
    
    /**
     * Get the type of the message.
     *
     * @return string
     */
    public function getType() : string;
    
    /**
     * The string representation of the content of this message.
     *
     * @return string
     */
    public function getContent() : string;
}
