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
     * @return null|VersionInterface
     */
    public function getEventTypeVersion() : ?VersionInterface;
    
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
     * @return array|null
     */
    public function getData() : ?array;
}
