<?php
declare(strict_types = 1);


namespace SmartWeb\Nats\Channel;

/**
 * Interface ChannelInterface
 *
 * @api
 */
interface ChannelInterface
{
    
    /**
     * @return string
     */
    public function getName() : string;
}
