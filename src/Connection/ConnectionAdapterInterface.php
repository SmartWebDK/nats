<?php
declare(strict_types = 1);


namespace SmartWeb\Nats\Connection;

use NatsStreaming\TrackedNatsRequest;
use SmartWeb\Nats\Channel\ChannelInterface;
use SmartWeb\Nats\PayloadInterface;

/**
 * Interface ConnectionAdapterInterface
 *
 * @api
 */
interface ConnectionAdapterInterface
{
    
    /**
     * @param ChannelInterface $channel
     * @param PayloadInterface $payload
     *
     * @return TrackedNatsRequest
     */
    public function publish(ChannelInterface $channel, PayloadInterface $payload) : TrackedNatsRequest;
}
