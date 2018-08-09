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
     * @param bool|null $includeNullEntries
     *
     * @return array
     */
    public function payloadContents(?bool $includeNullEntries = null) : array;
    
    /**
     * @param bool|null $includeNullEntries
     *
     * @return array
     */
    public function payloadContentsArray(?bool $includeNullEntries = null) : array;
    
    /**
     * @param bool|null $includeNullEntries
     *
     * @return string
     */
    public function payloadString(?bool $includeNullEntries = null) : string;
}
