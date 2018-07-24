<?php
declare(strict_types = 1);


namespace SmartWeb\Nats\Channel;

/**
 * Interface ChannelGroupInterface
 *
 * @api
 */
interface ChannelGroupInterface
{
    
    /**
     * @return ChannelInterface[]
     */
    public function getChannels() : array;
}
