<?php
declare(strict_types = 1);


namespace SmartWeb\Nats\Payload;

use SmartWeb\CloudEvents\VersionInterface;

/**
 * Interface PayloadInterface
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
     * @return null|string
     */
    public function getEventId() : ?string;
    
    /**
     * @return \DateTimeInterface
     */
    public function getEventTime() : \DateTimeInterface;
    
    /**
     * The CloudEvents specification version this payload adheres to.
     *
     * @return VersionInterface
     */
    public function getCloudEventsVersion() : VersionInterface;
    
    /**
     * The name of the service that emitted the event.
     *
     * @return string
     */
    public function getSource() : string;
    
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
     * @return array
     */
    public function getData() : array;
}
