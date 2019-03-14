<?php
declare(strict_types = 1);


namespace SmartWeb\Nats\Event\Factory;

use Google\Protobuf\Timestamp;
use SmartWeb\Events\EventInterface;
use SmartWeb\Events\Generator\EventIdGeneratorInterface;
use SmartWeb\Events\Generator\EventTimeGeneratorInterface;

/**
 * Provides functionality for resolving response information for request events.
 *
 * @author Nicolai Agersbæk <na@zitcom.dk>
 */
class ResponseInfoResolver implements ResponseInfoResolverInterface
{
    
    /**
     * @var EventIdGeneratorInterface
     */
    private $idGenerator;
    
    /**
     * @var EventTimeGeneratorInterface
     */
    private $timeGenerator;
    
    /**
     * @param EventIdGeneratorInterface   $idGenerator
     * @param EventTimeGeneratorInterface $timeGenerator
     */
    public function __construct(EventIdGeneratorInterface $idGenerator, EventTimeGeneratorInterface $timeGenerator)
    {
        $this->idGenerator = $idGenerator;
        $this->timeGenerator = $timeGenerator;
    }
    
    /**
     * Resolves the event type to use for a response to the given request.
     *
     * Expected format of the event type is `<request.eventType>.response`.
     * Thus, given a request, `r = {eventType: "io.example.type"}`, this MUST
     * return `io.example.type.response`.
     *
     * @param EventInterface $request
     *
     * @return string
     */
    public function getResponseEventType(EventInterface $request) : string
    {
        return "{$request->getEventType()}.response";
    }
    
    /**
     * Resolves the event ID to use for a response to the given request.
     *
     * @param EventInterface $request
     *
     * @return string
     */
    public function getResponseEventId(EventInterface $request) : string
    {
        return $this->idGenerator->generate();
    }
    
    /**
     * Resolves the event time to use for a response.
     *
     * @return Timestamp
     */
    public function getResponseEventTime() : Timestamp
    {
        return $this->timeGenerator->generate();
    }
    
    /**
     * Resolves the response channel to use when publishing a response to the given request.
     *
     * Expected format of the channel name is `responses.<subChannel>.<request.eventId>`.
     * Thus, given a request event, `r = {eventId: "some-unique-id"}` broadcast
     * on the channel `io.example.channel`, the response channel returned MUST be
     * `responses.io_example_channel.some-unique-id`.
     *
     * @param EventInterface $request
     *
     * @return string
     */
    public function getResponseChannel(EventInterface $request) : string
    {
        // Replace all '.' with '_' to conform to expected channel name.
        $subChannel = \str_replace('.', '_', $request->getEventType());
        
        return "responses.{$subChannel}.{$request->getEventId()}";
    }
}
