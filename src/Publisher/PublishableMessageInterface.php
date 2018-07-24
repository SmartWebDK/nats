<?php
declare(strict_types = 1);


namespace SmartWeb\Nats\Publisher;

/**
 * Interface PublishableMessageInterface
 *
 * @api
 */
interface PublishableMessageInterface
{
    
    /**
     * The message type of this message.
     */
    public const MESSAGE_TYPE = '';
    
    /**
     * @return string
     */
    public function getSubject() : string;
    
    /**
     * @return array
     */
    public function getContext() : array;
    
    /**
     * @return null|string
     */
    public function getInbox() : ?string;
}
