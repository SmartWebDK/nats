<?php
declare(strict_types = 1);


namespace SmartWeb\Nats\Subscriber;

use SmartWeb\Nats\Payload\PayloadInterface;

/**
 * Class SubscriberTest
 *
 * @internal
 */
class SubscriberTest implements SubscriberInterface
{
    
    /**
     * @inheritDoc
     */
    public function handle(PayloadInterface $payload) : void
    {
        \var_dump($payload);
    }
}
