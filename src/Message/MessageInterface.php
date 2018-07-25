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
     * The sequential index of this message.
     *
     * @return int
     */
    public function getIndex() : int;
    
    /**
     * The subject of this message.
     *
     * @return string
     */
    public function getSubject() : string;
    
    /**
     * The string representation of the data of this message.
     *
     * @return string
     */
    public function getData() : string;
    
    /**
     * The timestamp this message was received.
     *
     * @return int
     */
    public function getTimestamp() : int;
}
