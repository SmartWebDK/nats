<?php
declare(strict_types = 1);


namespace SmartWeb\Nats\Event\Factory;

use SmartWeb\Events\EventInterface;

/**
 * Provides functionality for resolving response information for request events.
 *
 * @author Nicolai Agersbæk <na@zitcom.dk>
 *
 * @api
 */
interface ResponseInfoResolverInterface
{
    
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
    public function getResponseEventType(EventInterface $request) : string;
    
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
    public function getResponseChannel(EventInterface $request) : string;
}
