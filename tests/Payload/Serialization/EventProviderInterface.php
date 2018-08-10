<?php
declare(strict_types = 1);


namespace SmartWeb\Nats\Tests\Payload\Serialization;

use SmartWeb\CloudEvents\Nats\Event\EventInterface;

/**
 * Definition of a provider of fixed CloudEvents events data.
 *
 * @author Nicolai Agersbæk <na@smartweb.dk>
 *
 * @internal
 */
interface EventProviderInterface
{
    
    /**
     * @return EventInterface
     */
    public function event() : EventInterface;
    
    /**
     * @param bool|null $includeNullEntries
     *
     * @return array
     */
    public function eventContents(?bool $includeNullEntries = null) : array;
    
    /**
     * @param bool|null $includeNullEntries
     *
     * @return string
     */
    public function eventString(?bool $includeNullEntries = null) : string;
}
