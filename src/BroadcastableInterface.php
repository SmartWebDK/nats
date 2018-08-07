<?php
declare(strict_types = 1);


namespace SmartWeb\Nats;

use SmartWeb\Nats\Channel\ChannelInterface;
use SmartWeb\Nats\Payload\Data\PayloadDataInterface;

/**
 * Interface BroadcastableInterface
 *
 * @api
 */
interface BroadcastableInterface
{
    
    /**
     * @return ChannelInterface
     */
    public function getChannel() : ChannelInterface;
    
    /**
     * @return string
     */
    public function getName() : string;
    
    /**
     * @return string
     */
    public function getId() : string;
    
    /**
     * @return PayloadDataInterface
     */
    public function getData() : PayloadDataInterface;
}
