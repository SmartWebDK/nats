<?php
declare(strict_types = 1);


namespace SmartWeb\Nats\Payload;

use SmartWeb\CloudEvents\VersionInterface;

/**
 * Class Payload
 *
 * @api
 */
class Payload implements PayloadInterface
{
    
    /**
     * @var string
     */
    private $eventType;
    
    /**
     * @var null|VersionInterface
     */
    private $eventTypeVersion;
    
    /**
     * @var VersionInterface
     */
    private $cloudEventsVersion;
    
    /**
     * @var string
     */
    private $source;
    
    /**
     * @var string
     */
    private $eventId;
    
    /**
     * @var \DateTimeInterface|null
     */
    private $eventTime;
    
    /**
     * @var null|string
     */
    private $schemaURL;
    
    /**
     * @var null|string
     */
    private $contentType;
    
    /**
     * @var array|null
     */
    private $extensions;
    
    /**
     * @var array|null
     */
    private $data;
    
    /**
     * Payload constructor.
     *
     * @param string                  $eventType
     * @param VersionInterface|null   $eventTypeVersion
     * @param VersionInterface        $cloudEventsVersion
     * @param string                  $source
     * @param string                  $eventId
     * @param \DateTimeInterface|null $eventTime
     * @param string|null             $schemaURL
     * @param string|null             $contentType
     * @param array|null              $extensions
     * @param array|null              $data
     */
    public function __construct(
        string $eventType,
        ?VersionInterface $eventTypeVersion,
        VersionInterface $cloudEventsVersion,
        string $source,
        string $eventId,
        ?\DateTimeInterface $eventTime,
        ?string $schemaURL,
        ?string $contentType,
        ?array $extensions,
        ?array $data
    ) {
        $this->eventType = $eventType;
        $this->eventTypeVersion = $eventTypeVersion;
        $this->eventId = $eventId;
        $this->eventTime = $eventTime;
        $this->cloudEventsVersion = $cloudEventsVersion;
        $this->source = $source;
        $this->schemaURL = $schemaURL;
        $this->contentType = $contentType;
        $this->extensions = $extensions;
        $this->data = $data;
    }
    
    /**
     * @inheritDoc
     */
    public function getEventType() : string
    {
        return $this->eventType;
    }
    
    /**
     * @inheritDoc
     */
    public function getEventTypeVersion() : ?VersionInterface
    {
        return $this->eventTypeVersion;
    }
    
    /**
     * @inheritDoc
     */
    public function getEventId() : string
    {
        return $this->eventId;
    }
    
    /**
     * @inheritDoc
     */
    public function getEventTime() : ?\DateTimeInterface
    {
        return $this->eventTime;
    }
    
    /**
     * @inheritDoc
     */
    public function getCloudEventsVersion() : VersionInterface
    {
        return $this->cloudEventsVersion;
    }
    
    /**
     * @inheritDoc
     */
    public function getSource() : string
    {
        return $this->source;
    }
    
    /**
     * @inheritDoc
     */
    public function getSchemaURL() : ?string
    {
        return $this->schemaURL;
    }
    
    /**
     * @inheritDoc
     */
    public function getContentType() : ?string
    {
        return $this->contentType;
    }
    
    /**
     * @inheritDoc
     */
    public function getExtensions() : ?array
    {
        return $this->extensions;
    }
    
    /**
     * @inheritDoc
     */
    public function getData() : ?array
    {
        return $this->data;
    }
}
