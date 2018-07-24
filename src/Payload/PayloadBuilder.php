<?php
declare(strict_types = 1);


namespace SmartWeb\Nats\Payload;

use SmartWeb\CloudEvents\VersionInterface;
use SmartWeb\Nats\Error\PayloadBuilderError;

/**
 * Class PayloadBuilder
 *
 * @api
 */
class PayloadBuilder implements PayloadBuilderInterface
{
    
    /**
     * Key used for storing 'eventType' payload data.
     */
    private const EVENT_TYPE = 'eventType';
    
    /**
     * Key used for storing 'eventTypeVersion' payload data.
     */
    private const EVENT_TYPE_VERSION = 'eventTypeVersion';
    
    /**
     * Key used for storing 'cloudEventsVersion' payload data.
     */
    private const CLOUD_EVENTS_VERSION = 'cloudEventsVersion';
    
    /**
     * Key used for storing 'source' payload data.
     */
    private const SOURCE = 'source';
    
    /**
     * Key used for storing 'eventId' payload data.
     */
    private const EVENT_ID = 'eventId';
    
    /**
     * Key used for storing 'eventTime' payload data.
     */
    private const EVENT_TIME = 'eventTime';
    
    /**
     * Key used for storing 'schemaURL' payload data.
     */
    private const SCHEMA_URL = 'schemaURL';
    
    /**
     * Key used for storing 'contentType' payload data.
     */
    private const CONTENT_TYPE = 'contentType';
    
    /**
     * Key used for storing 'extensions' payload data.
     */
    private const EXTENSIONS = 'extensions';
    
    /**
     * Key used for storing 'data' payload data.
     */
    private const DATA = 'data';
    
    /**
     * @var string[]
     */
    private static $requiredFields = [
        self::EVENT_TYPE,
        self::CLOUD_EVENTS_VERSION,
        self::SOURCE,
        self::EVENT_ID,
    ];
    
    /**
     * @var array
     */
    private $builderArgs = [];
    
    /**
     * @inheritDoc
     */
    public static function create() : PayloadBuilderInterface
    {
        return new self();
    }
    
    /**
     * @inheritDoc
     */
    public function build() : PayloadInterface
    {
        $this->validateBuilderArgs();
        
        return new Payload(...$this->builderArgs);
    }
    
    /**
     *
     */
    private function validateBuilderArgs() : void
    {
        $missingFields = $this->getMissingFields();
        
        if ($missingFields !== []) {
            throw new PayloadBuilderError($missingFields);
        }
    }
    
    /**
     * @return string[]
     */
    private function getMissingFields() : array
    {
        return \array_diff($this->getRequiredFields(), \array_keys($this->builderArgs));
    }
    
    /**
     * @return string[]
     */
    private function getRequiredFields() : array
    {
        return self::$requiredFields;
    }
    
    /**
     * @inheritDoc
     */
    public function setEventType(string $type) : PayloadBuilderInterface
    {
        $this->builderArgs[self::EVENT_TYPE] = $type;
        
        return $this;
    }
    
    /**
     * @inheritDoc
     */
    public function setEventTypeVersion(VersionInterface $version) : PayloadBuilderInterface
    {
        $this->builderArgs[self::EVENT_TYPE_VERSION] = $version;
        
        return $this;
    }
    
    /**
     * @inheritDoc
     */
    public function setCloudEventsVersion(VersionInterface $version) : PayloadBuilderInterface
    {
        $this->builderArgs[self::CLOUD_EVENTS_VERSION] = $version;
        
        return $this;
    }
    
    /**
     * @inheritDoc
     */
    public function setSource(string $source) : PayloadBuilderInterface
    {
        $this->builderArgs[self::SOURCE] = $source;
        
        return $this;
    }
    
    /**
     * @inheritDoc
     */
    public function setEventId(string $id) : PayloadBuilderInterface
    {
        $this->builderArgs[self::EVENT_ID] = $id;
        
        return $this;
    }
    
    /**
     * @inheritDoc
     */
    public function setEventTime(\DateTimeInterface $time) : PayloadBuilderInterface
    {
        $this->builderArgs[self::EVENT_TIME] = $time;
        
        return $this;
    }
    
    /**
     * @inheritDoc
     */
    public function setSchemaURL(string $schemaURL) : PayloadBuilderInterface
    {
        $this->builderArgs[self::SCHEMA_URL] = $schemaURL;
        
        return $this;
    }
    
    /**
     * @inheritDoc
     */
    public function setContentType(string $contentType) : PayloadBuilderInterface
    {
        $this->builderArgs[self::CONTENT_TYPE] = $contentType;
        
        return $this;
    }
    
    /**
     * @inheritDoc
     */
    public function setExtensions(array $extensions) : PayloadBuilderInterface
    {
        $this->builderArgs[self::EXTENSIONS] = $extensions;
        
        return $this;
    }
    
    /**
     * @inheritDoc
     */
    public function setData(array $data) : PayloadBuilderInterface
    {
        $this->builderArgs[self::DATA] = $data;
        
        return $this;
    }
}
