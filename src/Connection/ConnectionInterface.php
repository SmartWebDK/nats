<?php
declare(strict_types = 1);


namespace SmartWeb\Nats\Connection;

use SmartWeb\Nats\Error\SendError;
use SmartWeb\Nats\Message\MessageInterface;

/**
 * Interface ConnectionInterface
 *
 * @internal
 */
interface ConnectionInterface
{
    
    /**
     * Connect to server.
     *
     * @param float|null $timeout Number of seconds until the connect() system call should timeout.
     *
     * @return self
     *
     * @throws \Exception Exception raised if connection fails.
     */
    public function connect(float $timeout = null) : self;
    
    /**
     * Reconnect to the server.
     *
     * @return self
     */
    public function reconnect() : self;
    
    /**
     * Close the connection to the server.
     *
     * @return self
     */
    public function close() : self;
    
    /**
     * Waits for messages.
     *
     * @param int|null $numMessages Number of messages to wait for.
     *
     * @return self
     */
    public function wait(int $numMessages = null) : self;
    
    /**
     * Sends data thought the stream.
     *
     * @param MessageInterface $message Message data.
     *
     * @return self
     *
     * @throws SendError
     */
    public function send(MessageInterface $message) : self;
}
