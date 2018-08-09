<?php
declare(strict_types = 1);


namespace SmartWeb\Nats\Payload;

use SmartWeb\Nats\Error\PayloadBuilderError;
use SmartWeb\Nats\Payload\Data\PayloadDataInterface;

/**
 * Builder for creating payload objects.
 *
 * @author Nicolai Agersbæk <na@smartweb.dk>
 *
 * @api
 */
class PayloadBuilder implements PayloadBuilderInterface
{
    
    // FIXME: Missing tests!
    
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
    
    private function validateBuilderArgs() : void
    {
        $missingFields = $this->getMissingFields();
        
        if ($missingFields !== []) {
            $missingFieldsString = \implode("', '", $missingFields);
            throw new PayloadBuilderError("Missing required fields: '{$missingFieldsString}'");
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
    public function setEventTypeVersion(string $version) : PayloadBuilderInterface
    {
        $this->builderArgs[PayloadFields::EVENT_TYPE_VERSION] = $version;
        
        return $this;
    }
    
    /**
     * @inheritDoc
     */
    public function setCloudEventsVersion(string $version) : PayloadBuilderInterface
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
    public function setData(PayloadDataInterface $data) : PayloadBuilderInterface
    {
        $this->builderArgs[PayloadFields::DATA] = $data;
        
        return $this;
    }
}
