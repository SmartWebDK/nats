<?php
declare(strict_types = 1);


namespace SmartWeb\Nats\Tests\Event\Serialization;

/**
 * Provides easy access to an EventProviderFactory.
 *
 * @author Nicolai Agersbæk <na@smartweb.dk>
 *
 * @internal
 */
trait WithEventProviderFactory
{
    
    /**
     * @return EventProviderFactory
     */
    final protected static function factory() : EventProviderFactory
    {
        return EventProviderFactory::create();
    }
}
