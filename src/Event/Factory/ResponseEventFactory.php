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
class ResponseEventFactory implements ResponseEventFactoryInterface
{
    
    /**
     * @var string
     */
    private $eventCls;
    
    /**
     * @var string
     */
    private $eventSource;
    
    /**
     * @var ResponseInfoResolverInterface
     */
    private $infoResolver;
    
    /**
     * @param string                        $eventCls
     * @param string                        $eventSource
     * @param ResponseInfoResolverInterface $infoResolver
     */
    public function __construct(
        string $eventCls,
        string $eventSource,
        ResponseInfoResolverInterface $infoResolver
    ) {
        $this->eventCls = $eventCls;
        $this->eventSource = $eventSource;
        $this->infoResolver = $infoResolver;
    }
    
    /**
     * @param EventInterface $request
     * @param Message        $responseData
     *
     * @return EventInterface
     */
    public function createResponse(EventInterface $request, Message $responseData) : EventInterface
    {
        return new $this->eventCls(
            [
                'eventType' => $this->infoResolver->getResponseEventType($request),
                'source'    => $this->eventSource,
                'eventId'   => $this->infoResolver->getResponseEventId($request),
                'eventTime' => $this->infoResolver->getResponseEventTime(),
                'data'      => $responseData,
            ]
        );
    }
}
