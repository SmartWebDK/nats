<?php
declare(strict_types = 1);


namespace SmartWeb\Nats\Publisher;

use SmartWeb\CloudEvents\Context\ContextInterface;

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
     * @return ContextInterface
     */
    public function getContext() : ContextInterface;
    
    /**
     * @return null|string
     */
    public function getInbox() : ?string;
}
