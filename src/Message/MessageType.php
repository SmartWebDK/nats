<?php
declare(strict_types = 1);


namespace SmartWeb\Nats\Message;

/**
 * Class MessageType
 *
 * @method static static PUB()
 * @method static static SUB()
 */
final class MessageType
{
    
    private const PUB = 'PUB';
    
    private const SUB = 'SUB';
    
    /**
     * @var bool
     */
    private static $initialized = false;
    
    /**
     * @var self[]
     */
    private static $members = [];
    
    /**
     * @var string
     */
    private $value;
    
    /**
     * MessageType constructor.
     *
     * @param string $key
     * @param string $value
     */
    private function __construct(string $key, string $value)
    {
        $this->value = $value;
        self::$members[$key] = $this;
    }
    
    private static function initializeMembersIfNeeded() : void
    {
        if (!self::$initialized) {
            self::initializeMembers();
            self::$initialized = true;
        }
    }
    
    /**
     * @throws \ReflectionException
     */
    private static function initializeMembers() : void
    {
        $reflection = new \ReflectionClass(self::class);
        
        foreach ($reflection->getConstants() as $name => $value) {
            new self($value, $value);
        }
    }
    
    /**
     * @param string $key
     *
     * @return self
     */
    private static function getInstance(string $key) : self
    {
        self::initializeMembersIfNeeded();
        
        return self::$members[$key];
    }
    
    /**
     * @param string $name
     * @param array  $arguments
     *
     * @return self
     */
    public static function __callStatic(string $name, array $arguments) : self
    {
        return self::getInstance($name);
    }
    
    /**
     * @return string
     */
    public function __toString() : string
    {
        return $this->value;
    }
}
