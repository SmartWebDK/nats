<?php
declare(strict_types = 1);


namespace SmartWeb\Nats\Payload;

use SmartWeb\Nats\Payload\Data\PayloadDataInterface;

/**
 * Definition of the payload of an event according to the CloudEvents NATS Transporting Binding specification.
 *
 * @see https://github.com/cloudevents/spec/blob/master/nats-transport-binding.md
 *
 * @author Nicolai Agersbæk <na@smartweb.dk>
 *
 * @api
 */
interface PayloadInterface
{
    
    /**
     * @return string
     */
    public function getEventType() : string;
    
    /**
     * The event version.
     *
     * @return null|string
     */
    public function getEventTypeVersion() : ?string;
    
    /**
     * The CloudEvents specification version this payload adheres to.
     *
     * @return string
     */
    public function getCloudEventsVersion() : string;
    
    /**
     * The name of the service that emitted the event.
     *
     * @return string
     */
    public function getSource() : string;
    
    /**
     * @return string
     */
    public function getEventId() : string;
    
    /**
     * @return \DateTimeInterface|null
     */
    public function getEventTime() : ?\DateTimeInterface;
    
    /**
     * @return null|string
     */
    public function getSchemaURL() : ?string;
    
    /**
     * @return null|string
     */
    public function getContentType() : ?string;
    
    /**
     * @return array|null
     */
    public function getExtensions() : ?array;
    
    /**
     * @return null|PayloadDataInterface
     */
    public function getData() : ?PayloadDataInterface;
}
