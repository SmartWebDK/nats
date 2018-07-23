<?php
declare(strict_types = 1);


namespace SmartWeb\Nats\Connection;

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
     * @throws \Exception Exception raised if connection fails.
     */
    public function connect(float $timeout = null) : void;
    
    /**
     * Reconnect to the server.
     */
    public function reconnect() : void;
    
    /**
     * Close the connection to the server.
     */
    public function close() : void;
    
    /**
     * Waits for messages.
     *
     * @param int|null $numMessages Number of messages to wait for.
     *
     * @return ConnectionInterface
     */
    public function wait(int $numMessages = null) : self;
    
    /**
     * Sends data thought the stream.
     *
     * @param MessageInterface $message Message data.
     *
     * @return void
     */
    public function send(MessageInterface $message) : void;
    
    /**
     * @return bool
     */
    public function isConnected() : bool;
}
