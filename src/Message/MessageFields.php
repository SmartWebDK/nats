<?php
declare(strict_types = 1);


namespace SmartWeb\Nats\Message;

/**
 * Class MessageFields
 */
final class MessageFields
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
     * @var string[]
     */
    private static $requiredFields = [
        self::SEQUENCE,
        self::SUBJECT,
        self::DATA,
        self::TIMESTAMP,
    ];
    
    private function __construct()
    {
    }
    
    /**
     * @return array
     */
    public static function getRequiredFields() : array
    {
        return self::$requiredFields;
    }
}
