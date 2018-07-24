<?php
declare(strict_types = 1);


namespace SmartWeb\Nats\Payload;

use SmartWeb\CloudEvents\VersionInterface;
use SmartWeb\Nats\Error\PayloadBuilderError;

/**
 * Interface PayloadBuilderInterface
 *
 * @api
 */
interface PayloadBuilderInterface
{
    
    /**
     * @return PayloadBuilderInterface
     */
    public static function create() : self;
    
    /**
     * @param string $type
     *
     * @return PayloadBuilderInterface
     */
    public function setEventType(string $type) : self;
    
    /**
     * @param VersionInterface $version
     *
     * @return PayloadBuilderInterface
     */
    public function setEventTypeVersion(VersionInterface $version) : self;
    
    /**
     * @param VersionInterface $version
     *
     * @return PayloadBuilderInterface
     */
    public function setCloudEventsVersion(VersionInterface $version) : self;
    
    /**
     * @param string $source
     *
     * @return PayloadBuilderInterface
     */
    public function setSource(string $source) : self;
    
    /**
     * @param string $id
     *
     * @return PayloadBuilderInterface
     */
    public function setEventId(string $id) : self;
    
    /**
     * @param \DateTimeInterface $time
     *
     * @return PayloadBuilderInterface
     */
    public function setEventTime(\DateTimeInterface $time) : self;
    
    /**
     * @param string $schemaURL
     *
     * @return PayloadBuilderInterface
     */
    public function setSchemaURL(string $schemaURL) : self;
    
    /**
     * @param string $contentType
     *
     * @return PayloadBuilderInterface
     */
    public function setContentType(string $contentType) : self;
    
    /**
     * @param array $extensions
     *
     * @return PayloadBuilderInterface
     */
    public function setExtensions(array $extensions) : self;
    
    /**
     * @param array $data
     *
     * @return PayloadBuilderInterface
     */
    public function setData(array $data) : self;
    
    /**
     * @return PayloadInterface
     *
     * @throws PayloadBuilderError
     */
    public function build() : PayloadInterface;
}
