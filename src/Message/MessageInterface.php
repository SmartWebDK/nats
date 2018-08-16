<?php
declare(strict_types = 1);


namespace SmartWeb\Nats\Message;

/**
 * Definition of a message received from a NATS connection.
 *
 * @author Nicolai Agersbæk <na@smartweb.dk>
 *
 * @api
 */
interface MessageInterface
{
    
    /**
     * The key used for storing the 'sequence' data of this message.
     */
    public const SEQUENCE = 'sequence';
    
    /**
     * The key used for storing the 'subject' data of this message.
     */
    public const SUBJECT = 'subject';
    
    /**
     * The key used for storing the 'data' data of this message.
     */
    public const DATA = 'data';
    
    /**
     * The key used for storing the 'timestamp' data of this message.
     */
    public const TIMESTAMP = 'timestamp';
    
    /**
     * The sequential index of this message.
     *
     * @return int
     */
    public function getSequence() : int;
    
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
