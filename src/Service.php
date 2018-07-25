<?php
declare(strict_types = 1);


namespace SmartWeb\Nats;

use NatsStreaming\Connection;
use NatsStreaming\ConnectionOptions;
use SmartWeb\CloudEvents\Version;
use SmartWeb\Nats\Channel\Channel;
use SmartWeb\Nats\Connection\ConnectionAdapterInterface;
use SmartWeb\Nats\Connection\StreamingConnectionAdapter;
use SmartWeb\Nats\Encoding\PayloadDenormalizer;
use SmartWeb\Nats\Encoding\PayloadNormalizer;
use SmartWeb\Nats\Encoding\PayloadSerializer;
use SmartWeb\Nats\Encoding\SerializerInterface;
use SmartWeb\Nats\Payload\PayloadBuilder;
use Symfony\Component\Serializer\Encoder\JsonDecode;
use Symfony\Component\Serializer\Encoder\JsonEncode;

/**
 * Class Service
 */
class Service implements ServiceInterface
{
    
    public function run() : void
    {
        // TODO: Implement run() method.
        throw new \BadMethodCallException(__METHOD__ . ' not yet implemented!');
    }
    
    /**
     * @param string      $clusterID
     * @param null|string $clientID
     *
     * @throws \NatsStreaming\Exceptions\ConnectException
     * @throws \NatsStreaming\Exceptions\TimeoutException
     */
    public function runPublishTest(string $clusterID, ?string $clientID = null) : void
    {
        $connection = $this->createConnection($clusterID, $clientID);
        $connection->connect();
    
        $adapter = new StreamingConnectionAdapter($connection, $this->getSerializer());
        
        $channel = new Channel('some.channel');
        
        $data = [
            'foo' => 'bar'
        ];
        $payload = PayloadBuilder::create()
            ->setEventType('some.event')
            ->setCloudEventsVersion(new Version(0, 1, 0))
            ->setSource('some.source')
            ->setEventId('some.event.id')
            ->setData($data)
            ->build();
    
        $r = $adapter->publish($channel, $payload);
    
        $gotAck = $r->wait();
    
        $statusResponse = $gotAck
            ? 'Acknowledged'
            : 'Not acknowledged';
    
        \printf("$statusResponse\r\n");
    
        $connection->close();
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
     * @param string $clusterID
     *
     * @return ConnectionAdapterInterface
     */
    private function createAdapter(string $clusterID) : ConnectionAdapterInterface
    {
        return new StreamingConnectionAdapter($this->createConnection($clusterID), $this->getSerializer());
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
     * @param string      $clusterID
     * @param string|null $clientID
     *
     * @return Connection
     */
    private function createConnection(string $clusterID, string $clientID = null) : Connection
    {
        $options = new ConnectionOptions();
        
        $clientID = $clientID ?? (string)\mt_rand();
        $options->setClientID($clientID);
        $options->setClusterID($clusterID);
        
        return new Connection($options);
    }
}
