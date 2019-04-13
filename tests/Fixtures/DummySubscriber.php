<?php
declare(strict_types = 1);


namespace SmartWeb\Nats\Tests\Fixtures;

use SmartWeb\Nats\Message\Acknowledge;
use SmartWeb\Nats\Subscriber\SubscriberInterface;

/**
 * @author Nicolai Agersbæk <na@zitcom.dk>
 */
class DummySubscriber implements SubscriberInterface
{
    
    /**
     * @var \Closure|null
     */
    private $handle;
    
    /**
     * @var string|null
     */
    private $expects;
    
    /**
     * @var Acknowledge|null
     */
    private $acknowledge;
    
    /**
     * @param array|null $config
     */
    public function __construct(?array $config = null)
    {
        $this->handle = $this->resolveConfigValue($config, 'handle', \Closure::class);
        $this->expects = $this->resolveConfigValue($config, 'expects', 'string');
        $this->acknowledge = $this->resolveConfigValue($config, 'acknowledge', Acknowledge::class);
    }
    
    /**
     * @param array|null $config
     * @param string     $key
     * @param string     $expectedType
     *
     * @return mixed|null
     */
    final protected function resolveConfigValue(?array $config, string $key, string $expectedType)
    {
        if ($config === null) {
            return null;
        }
        
        $value = $config[$key] ?? null;
        
        $this->validateType($key, $expectedType, $value);
        
        return $value;
    }
    
    /**
     * @param string     $key
     * @param string     $expected
     * @param mixed|null $value
     */
    private function validateType(string $key, string $expected, $value) : void
    {
        if ($value === null) {
            return;
        }
        
        $isValid = \is_object($value)
            ? $value instanceof $expected
            : \gettype($value) === $expected;
        
        if ($isValid) {
            return;
        }
        
        throw $this->unexpectedTypeException($key, $expected, \gettype($value));
    }
    
    /**
     * @param string $key
     * @param string $expected
     * @param string $actual
     *
     * @return \DomainException
     */
    private function unexpectedTypeException(string $key, string $expected, string $actual) : \DomainException
    {
        return new \DomainException("Unexpected type for '{$key}': Expected '{$expected}'; was {$actual}");
    }
    
    /**
     * Handle the event.
     * The class of the provided event **MUST** be an instance of the expected
     * event class for this subscriber, given by `SubscriberInterface::expects()`.
     *
     * @param object $event Event to handle.
     *
     * @see \SmartWeb\Nats\Subscriber\SubscriberInterface::expects()
     */
    public function handle($event) : void
    {
        if ($this->handle === null) {
            throw new \BadMethodCallException(__METHOD__ . ' not yet implemented!');
        }
        
        $this->handle->call($this, $event);
    }
    
    /**
     * Get the fully-qualified class name of events expected by this subscriber.
     * This **MUST** be an instantiable class, which means that interfaces or
     * abstract classes are never considered valid.
     *
     * @return string
     */
    public function expects() : string
    {
        if ($this->expects === null) {
            throw new \BadMethodCallException(__METHOD__ . ' not yet implemented!');
        }
        
        return $this->expects;
    }
    
    /**
     * Get the acknowledge behavior for this subscriber.
     * This determines when the subscriber will acknowledge a message from NATS.
     *
     * @return Acknowledge
     */
    public function acknowledge() : Acknowledge
    {
        if ($this->acknowledge === null) {
            throw new \BadMethodCallException(__METHOD__ . ' not yet implemented!');
        }
        
        return $this->acknowledge;
    }
}
