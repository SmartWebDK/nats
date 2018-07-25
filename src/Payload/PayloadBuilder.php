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
     * @var array
     */
    private $builderArgs = [
        PayloadFields::EVENT_TYPE           => null,
        PayloadFields::EVENT_TYPE_VERSION   => null,
        PayloadFields::CLOUD_EVENTS_VERSION => null,
        PayloadFields::SOURCE               => null,
        PayloadFields::EVENT_ID             => null,
        PayloadFields::EVENT_TIME           => null,
        PayloadFields::SCHEMA_URL           => null,
        PayloadFields::CONTENT_TYPE         => null,
        PayloadFields::EXTENSIONS           => null,
        PayloadFields::DATA                 => null,
    ];
    
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
        
        return new Payload(...\array_values($this->builderArgs));
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
        return \array_diff(PayloadFields::getRequiredFields(), \array_keys($this->builderArgs));
    }
    
    /**
     * @inheritDoc
     */
    public function setEventType(string $type) : PayloadBuilderInterface
    {
        $this->builderArgs[PayloadFields::EVENT_TYPE] = $type;
        
        return $this;
    }
    
    /**
     * @inheritDoc
     */
    public function setEventTypeVersion(VersionInterface $version) : PayloadBuilderInterface
    {
        $this->builderArgs[PayloadFields::EVENT_TYPE_VERSION] = $version;
        
        return $this;
    }
    
    /**
     * @inheritDoc
     */
    public function setCloudEventsVersion(VersionInterface $version) : PayloadBuilderInterface
    {
        $this->builderArgs[PayloadFields::CLOUD_EVENTS_VERSION] = $version;
        
        return $this;
    }
    
    /**
     * @inheritDoc
     */
    public function setSource(string $source) : PayloadBuilderInterface
    {
        $this->builderArgs[PayloadFields::SOURCE] = $source;
        
        return $this;
    }
    
    /**
     * @inheritDoc
     */
    public function setEventId(string $id) : PayloadBuilderInterface
    {
        $this->builderArgs[PayloadFields::EVENT_ID] = $id;
        
        return $this;
    }
    
    /**
     * @inheritDoc
     */
    public function setEventTime(\DateTimeInterface $time) : PayloadBuilderInterface
    {
        $this->builderArgs[PayloadFields::EVENT_TIME] = $time;
        
        return $this;
    }
    
    /**
     * @inheritDoc
     */
    public function setSchemaURL(string $schemaURL) : PayloadBuilderInterface
    {
        $this->builderArgs[PayloadFields::SCHEMA_URL] = $schemaURL;
        
        return $this;
    }
    
    /**
     * @inheritDoc
     */
    public function setContentType(string $contentType) : PayloadBuilderInterface
    {
        $this->builderArgs[PayloadFields::CONTENT_TYPE] = $contentType;
        
        return $this;
    }
    
    /**
     * @inheritDoc
     */
    public function setExtensions(array $extensions) : PayloadBuilderInterface
    {
        $this->builderArgs[PayloadFields::EXTENSIONS] = $extensions;
        
        return $this;
    }
    
    /**
     * @inheritDoc
     */
    public function setData(array $data) : PayloadBuilderInterface
    {
        $this->builderArgs[PayloadFields::DATA] = $data;
        
        return $this;
    }
}
