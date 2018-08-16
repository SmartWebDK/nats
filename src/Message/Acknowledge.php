<?php
declare(strict_types = 1);


namespace SmartWeb\Nats\Message;

/**
 * Acknowledge behavior.
 *
 * @author Nicolai Agersbæk <na@smartweb.dk>
 *
 * @api
 */
final class Acknowledge
{
    
    /**
     * Acknowledge before invoking handler.
     */
    private const BEFORE = 'before';
    
    /**
     * Acknowledge after invoking handler.
     */
    private const AFTER = 'after';
    
    /**
     * Acknowledge manually in handler.
     */
    private const MANUAL = 'manual';
    
    /**
     * @var self[]
     */
    private static $instances = [];
    
    /**
     * @var string
     */
    private $behavior;
    
    /**
     * @param string $behavior
     */
    private function __construct(string $behavior)
    {
        $this->behavior = $behavior;
    }
    
    /**
     * @return self
     */
    public static function before() : self
    {
        return self::getInstance(self::BEFORE);
    }
    
    /**
     * @return self
     */
    public static function after() : self
    {
        return self::getInstance(self::AFTER);
    }
    
    /**
     * @return self
     */
    public static function manual() : self
    {
        return self::getInstance(self::MANUAL);
    }
    
    /**
     * @param string $key
     *
     * @return self
     */
    private static function getInstance(string $key) : self
    {
        return self::$instances[$key] ?? self::$instances[$key] = new self($key);
    }
}
