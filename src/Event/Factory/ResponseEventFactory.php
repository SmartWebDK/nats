<?php
declare(strict_types = 1);


namespace SmartWeb\Nats\Event\Factory;

use Google\Protobuf\Internal\Message;
use SmartWeb\Events\EventInterface;
use SmartWeb\Events\Generator\EventIdGeneratorInterface;
use SmartWeb\Events\Generator\EventTimeGeneratorInterface;

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
     * @var EventIdGeneratorInterface
     */
    private $idGenerator;
    
    /**
     * @var EventTimeGeneratorInterface
     */
    private $timeGenerator;
    
    /**
     * @param string                        $eventCls
     * @param string                        $eventSource
     * @param ResponseInfoResolverInterface $infoResolver
     * @param EventIdGeneratorInterface     $idGenerator
     * @param EventTimeGeneratorInterface   $timeGenerator
     */
    public function __construct(
        string $eventCls,
        string $eventSource,
        ResponseInfoResolverInterface $infoResolver,
        EventIdGeneratorInterface $idGenerator,
        EventTimeGeneratorInterface $timeGenerator
    ) {
        $this->eventCls = $eventCls;
        $this->eventSource = $eventSource;
        $this->infoResolver = $infoResolver;
        $this->idGenerator = $idGenerator;
        $this->timeGenerator = $timeGenerator;
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
                'eventId'   => $this->idGenerator->generate(),
                'eventTime' => $this->timeGenerator->generate(),
                'data'      => $responseData,
            ]
        );
    }
}
