<?php
declare(strict_types = 1);


namespace SmartWeb\Nats;

use SmartWeb\CloudEvents\Context\ContextInterface;
use SmartWeb\Nats\Message\MessageInterface;

/**
 * Interface PayloadInterface
 *
 * @api
 */
interface PublishablePayloadInterface extends MessageInterface
{
    
    /**
     * @return string
     */
    public function getSubject() : string;
    
    /**
     * @return ContextInterface
     */
    public function getPayload() : ContextInterface;
    
    /**
     * @return string
     */
    public function getInbox() : string;
}
