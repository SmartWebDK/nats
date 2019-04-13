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
     * @var self[]
     */
    private static $instances = [];
    
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
     * @param string $key
     *
     * @return self
     */
    private static function getInstance(string $key) : self
    {
        return self::$instances[$key] ?? self::$instances[$key] = new self();
    }
}
