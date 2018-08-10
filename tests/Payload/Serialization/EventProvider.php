<?php
declare(strict_types = 1);


namespace SmartWeb\Nats\Tests\Payload\Serialization;

use SmartWeb\CloudEvents\Nats\Event\Event;
use SmartWeb\CloudEvents\Nats\Event\EventInterface;
use Symfony\Component\Serializer\Encoder\EncoderInterface;
use Symfony\Component\Serializer\Encoder\JsonEncode;
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Serializer\Normalizer\DateTimeNormalizer;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;

/**
 * Provides CloudEvents event data for tests.
 *
 * @author Nicolai Agersbæk <na@smartweb.dk>
 *
 * @internal
 */
class EventProvider implements EventProviderInterface
{
    
    /**
     * @var EncoderInterface
     */
    private static $jsonEncoder;
    
    /**
     * @var string
     */
    private static $dateTimeFormat;
    
    /**
     * @var NormalizerInterface
     */
    private static $dateTimeNormalizer;
    
    /**
     * @var array
     */
    private $eventContents;
    
    /**
     * @param array $eventContents
     */
    public function __construct(array $eventContents)
    {
        $this->eventContents = $eventContents;
        
        $this->initialize();
    }
    
    private function initialize() : void
    {
        self::$jsonEncoder = self::$jsonEncoder ?? new JsonEncode();
        self::$dateTimeFormat = self::$dateTimeFormat ?? \DateTime::RFC3339;
        self::$dateTimeNormalizer = self::$dateTimeNormalizer ?? new DateTimeNormalizer(self::$dateTimeFormat);
    }
    
    /**
     * @return EventInterface
     */
    public function event() : EventInterface
    {
        return new Event(...\array_values($this->eventContents()));
    }
    
    /**
     * @param bool|null $includeNullEntries
     *
     * @return array
     */
    public function eventContents(?bool $includeNullEntries = null) : array
    {
        return $this->resolveContents($includeNullEntries);
    }
    
    /**
     * @param bool|null $includeNullEntries
     *
     * @return array
     */
    private function resolveContents(?bool $includeNullEntries = null) : array
    {
        $includeNullEntries = $includeNullEntries ?? true;
        
        return $includeNullEntries
            ? $this->eventContents
            : $this->filterNullValues($this->eventContents);
    }
    
    /**
     * @param array $array
     *
     * @return array
     */
    private function filterNullValues(array $array) : array
    {
        return \array_filter(
            $array,
            function ($value) : bool {
                return $value !== null;
            }
        );
    }
    
    /**
     * @param bool|null $includeNullEntries
     *
     * @return string
     */
    public function eventString(?bool $includeNullEntries = null) : string
    {
        return $this->resolvePayloadString($this->eventContents($includeNullEntries));
    }
    
    /**
     * @param array $dataArray
     *
     * @return string
     */
    private function resolvePayloadString(array $dataArray) : string
    {
        return self::$jsonEncoder->encode($dataArray, JsonEncoder::FORMAT);
    }
}
