<?php
declare(strict_types = 1);


namespace SmartWeb\Nats\Tests\Payload\Serialization;

use SmartWeb\CloudEvents\Nats\Payload\PayloadInterface;

/**
 * Definition of a provider of fixed payload data.
 *
 * @author Nicolai Agersbæk <na@smartweb.dk>
 *
 * @internal
 */
interface PayloadProviderInterface
{
    
    /**
     * @return PayloadInterface
     */
    public function payload() : PayloadInterface;
    
    /**
     * @return array
     */
    public function payloadContents() : array;
    
    /**
     * @return array
     */
    public function payloadContentsArray() : array;
    
    /**
     * @return string
     */
    public function payloadString() : string;
}
