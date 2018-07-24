<?php
declare(strict_types = 1);


namespace SmartWeb\Nats;

use NatsStreaming\Connection;
use NatsStreaming\ConnectionOptions;
use SmartWeb\Nats\Connection\ConnectionAdapterInterface;
use SmartWeb\Nats\Connection\StreamingConnectionAdapter;
use SmartWeb\Nats\Encoding\PayloadDenormalizer;
use SmartWeb\Nats\Encoding\PayloadNormalizer;
use SmartWeb\Nats\Encoding\PayloadSerializer;
use SmartWeb\Nats\Encoding\SerializerInterface;
use Symfony\Component\Serializer\Encoder\JsonDecode;
use Symfony\Component\Serializer\Encoder\JsonEncode;

/**
 * Class Service
 */
class Service implements ServiceInterface
{
    
    /**
     * @var ConnectionAdapterInterface
     */
    protected $connection;
    
    /**
     * Service constructor.
     *
     * @param ConnectionAdapterInterface $connection
     */
    public function __construct(ConnectionAdapterInterface $connection)
    {
        $this->connection = $connection;
    }
    
    public function run() : void
    {
        // TODO: Implement run() method.
        throw new \BadMethodCallException(__METHOD__ . ' not yet implemented!');
    }
    
    public function runPublishTest() : void
    {
    
    }
    
    public function runSubscribeTest() : void
    {
    
    }
    
    public function runQueueGroupSubscribeTest() : void
    {
        // TODO: Implement run() method.
        throw new \BadMethodCallException(__METHOD__ . ' not yet implemented!');
    }
    
    /**
     * @return ConnectionAdapterInterface
     */
    private function createAdapter() : ConnectionAdapterInterface
    {
        return new StreamingConnectionAdapter($this->createConnection(), $this->getSerializer());
    }
    
    /**
     * @return SerializerInterface
     */
    private function getSerializer() : SerializerInterface
    {
        $normalizer = new PayloadNormalizer();
        $denormalizer = new PayloadDenormalizer();
        $encoder = new JsonEncode();
        $decoder = new JsonDecode();
        
        return new PayloadSerializer($normalizer, $denormalizer, $encoder, $decoder);
    }
    
    /**
     * @return Connection
     */
    private function createConnection() : Connection
    {
        $options = new ConnectionOptions();
        
        $clientID = mt_rand();
        $options->setClientID($clientID);
        $options->setClusterID('test-cluster');
        
        return new Connection($options);
    }
}
