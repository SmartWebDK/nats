<?php
declare(strict_types = 1);


namespace SmartWeb\Nats\Event\Factory;

use Google\Protobuf\Internal\Message;
use SmartWeb\Events\EventInterface;

/**
 * Creates response events from a given request.
 *
 * @author Nicolai Agersbæk <na@zitcom.dk>
 *
 * @api
 */
interface ResponseEventFactoryInterface
{
    
    /**
     * Creates a response event to the given request with the given data.
     *
     * @param EventInterface $request
     * @param Message        $responseData
     *
     * @return EventInterface
     */
    public function createResponse(EventInterface $request, Message $responseData) : EventInterface;
}
