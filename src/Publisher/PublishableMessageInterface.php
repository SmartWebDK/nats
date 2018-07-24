<?php
declare(strict_types = 1);


namespace SmartWeb\Nats\Publisher;

use SmartWeb\Nats\Message\MessageTypeAwareInterface;

/**
 * Interface PublishableMessageInterface
 *
 * @api
 */
interface PublishableMessageInterface extends MessageTypeAwareInterface
{
    
    /**
     * The message type of this message.
     */
    public const MESSAGE_TYPE = 'PUB';
    
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
