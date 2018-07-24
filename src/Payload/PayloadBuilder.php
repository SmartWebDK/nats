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
     * @var string[]
     */
    private static $requiredFields = [
        PayloadField::EVENT_TYPE,
        PayloadField::CLOUD_EVENTS_VERSION,
        PayloadField::SOURCE,
        PayloadField::EVENT_ID,
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
        $this->builderArgs[PayloadField::EVENT_TYPE] = $type;
        
        return $this;
    }
    
    /**
     * @inheritDoc
     */
    public function setEventTypeVersion(VersionInterface $version) : PayloadBuilderInterface
    {
        $this->builderArgs[PayloadField::EVENT_TYPE_VERSION] = $version;
        
        return $this;
    }
    
    /**
     * @inheritDoc
     */
    public function setCloudEventsVersion(VersionInterface $version) : PayloadBuilderInterface
    {
        $this->builderArgs[PayloadField::CLOUD_EVENTS_VERSION] = $version;
        
        return $this;
    }
    
    /**
     * @inheritDoc
     */
    public function setSource(string $source) : PayloadBuilderInterface
    {
        $this->builderArgs[PayloadField::SOURCE] = $source;
        
        return $this;
    }
    
    /**
     * @inheritDoc
     */
    public function setEventId(string $id) : PayloadBuilderInterface
    {
        $this->builderArgs[PayloadField::EVENT_ID] = $id;
        
        return $this;
    }
    
    /**
     * @inheritDoc
     */
    public function setEventTime(\DateTimeInterface $time) : PayloadBuilderInterface
    {
        $this->builderArgs[PayloadField::EVENT_TIME] = $time;
        
        return $this;
    }
    
    /**
     * @inheritDoc
     */
    public function setSchemaURL(string $schemaURL) : PayloadBuilderInterface
    {
        $this->builderArgs[PayloadField::SCHEMA_URL] = $schemaURL;
        
        return $this;
    }
    
    /**
     * @inheritDoc
     */
    public function setContentType(string $contentType) : PayloadBuilderInterface
    {
        $this->builderArgs[PayloadField::CONTENT_TYPE] = $contentType;
        
        return $this;
    }
    
    /**
     * @inheritDoc
     */
    public function setExtensions(array $extensions) : PayloadBuilderInterface
    {
        $this->builderArgs[PayloadField::EXTENSIONS] = $extensions;
        
        return $this;
    }
    
    /**
     * @inheritDoc
     */
    public function setData(array $data) : PayloadBuilderInterface
    {
        $this->builderArgs[PayloadField::DATA] = $data;
        
        return $this;
    }
}
