<?php
declare(strict_types = 1);


namespace SmartWeb\Nats\Message;

use SmartWeb\Nats\Support\DataContainerDefinition;
use SmartWeb\Nats\Support\DataContainerDefinitionInterface;
use SmartWeb\Nats\Support\FieldDefinition;

/**
 * Definition of fields for a NATS message.
 *
 * @author Nicolai Agersbæk <na@smartweb.dk>
 *
 * @api
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
     * @var self
     */
    private static $instance;
    
    /**
     * @var DataContainerDefinitionInterface
     */
    private $definition;
    
    private function __construct()
    {
        $this->definition = new DataContainerDefinition(
            [
                new FieldDefinition(self::SEQUENCE, true),
                new FieldDefinition(self::SUBJECT, true),
                new FieldDefinition(self::DATA, true),
                new FieldDefinition(self::TIMESTAMP, true),
            ]
        );
    }
    
    /**
     * @return self
     */
    private static function getInstance() : self
    {
        return self::$instance ?? self::$instance = new static();
    }
    
    /**
     * @return DataContainerDefinitionInterface
     */
    public static function getDefinition() : DataContainerDefinitionInterface
    {
        return self::getInstance()->definition;
    }
}
