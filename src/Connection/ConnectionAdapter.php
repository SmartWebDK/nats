<?php
declare(strict_types = 1);


namespace SmartWeb\Nats\Connection;

use Nats\Connection;
use Nats\ConnectionOptions;
use SmartWeb\Nats\Error\SendError;
use SmartWeb\Nats\Message\MessageInterface;

/**
 * Class ConnectionAdapter
 *
 * @internal
 */
class ConnectionAdapter implements ConnectionInterface
{
    
    /**
     * @var Connection
     */
    private $connection;
    
    /**
     * ConnectionAdapter constructor.
     *
     * @param ConnectionOptions $options
     */
    public function __construct(ConnectionOptions $options)
    {
        $this->connection = new Connection($options);
    }
    
    /**
     * @inheritDoc
     */
    public function connect(float $timeout = null) : ConnectionInterface
    {
        $this->connection->connect($timeout);
        
        return $this;
    }
    
    /**
     * @inheritDoc
     */
    public function reconnect() : ConnectionInterface
    {
        $this->connection->reconnect();
        
        return $this;
    }
    
    /**
     * @inheritDoc
     */
    public function close() : ConnectionInterface
    {
        $this->connection->close();
        
        return $this;
    }
    
    /**
     * @inheritDoc
     */
    public function wait(int $numMessages = null) : ConnectionInterface
    {
        $this->connection->wait($numMessages);
        
        return $this;
    }
    
    /**
     * @inheritDoc
     */
    public function send(MessageInterface $message) : ConnectionInterface
    {
        // TODO: Refactor to using MessageFormatter
        $msg = $this->formatMessageForStream($message);
        $len = \strlen($msg);
        
        while (true) {
            $written = @fwrite($this->connection->getStreamSocket(), $msg);
            
            if ($written === false) {
                throw new SendError('Error sending data');
            }
            
            if ($written === 0) {
                throw new SendError('Broken pipe or closed connection');
            }
            
            $len -= $written;
            
            if ($len > 0) {
                $msg = substr($msg, 0 - $len);
            } else {
                break;
            }
        }

//        if ($this->debug === true) {
//            printf('>>>> %s', $msg);
//        }
        
        return $this;
    }
    
    /**
     * @param MessageInterface $message
     *
     * @return string
     */
    private function formatMessageForStream(MessageInterface $message) : string
    {
        return "{$message->getType()} {$message->getContent()}\r\n";
    }
}
