<?php
declare(strict_types = 1);


namespace SmartWeb\Nats\Tests\Payload\Serialization;

use SmartWeb\CloudEvents\Nats\Payload\Data\ArrayData;
use SmartWeb\CloudEvents\Nats\Payload\Payload;
use SmartWeb\CloudEvents\Nats\Payload\PayloadFields;
use SmartWeb\CloudEvents\Nats\Payload\PayloadInterface;
use Symfony\Component\Serializer\Encoder\EncoderInterface;
use Symfony\Component\Serializer\Encoder\JsonEncode;
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Serializer\Normalizer\DateTimeNormalizer;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;

/**
 * Provides payload data for tests.
 *
 * @author Nicolai Agersbæk <na@smartweb.dk>
 *
 * @internal
 */
class PayloadProvider implements PayloadProviderInterface
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
    private $contentsArray;
    
    /**
     * @param array $contentsArray
     */
    public function __construct(array $contentsArray)
    {
        $this->contentsArray = $contentsArray;
        
        $this->initialize();
    }
    
    private function initialize() : void
    {
        self::$jsonEncoder = self::$jsonEncoder ?? new JsonEncode();
        self::$dateTimeFormat = self::$dateTimeFormat ?? \DateTime::RFC3339;
        self::$dateTimeNormalizer = self::$dateTimeNormalizer ?? new DateTimeNormalizer(self::$dateTimeFormat);
    }
    
    /**
     * @return PayloadInterface
     */
    public function payload() : PayloadInterface
    {
        return new Payload(...\array_values($this->payloadContents()));
    }
    
    /**
     * @param bool|null $includeNullEntries
     *
     * @return array
     */
    public function payloadContents(?bool $includeNullEntries = null) : array
    {
        return $this->resolvePayloadContents($this->payloadContentsArray($includeNullEntries));
    }
    
    /**
     * @param array $dataArray
     *
     * @return array
     */
    private function resolvePayloadContents(array $dataArray) : array
    {
        $dataArray[PayloadFields::DATA] = new ArrayData($dataArray[PayloadFields::DATA]);
        
        return $dataArray;
    }
    
    /**
     * @param bool|null $includeNullEntries
     *
     * @return array
     */
    public function payloadContentsArray(?bool $includeNullEntries = null) : array
    {
        return $this->resolveContentsArray($includeNullEntries);
    }
    
    /**
     * @param bool|null $includeNullEntries
     *
     * @return array
     */
    private function resolveContentsArray(?bool $includeNullEntries = null) : array
    {
        $includeNullEntries = $includeNullEntries ?? true;
        
        return $includeNullEntries
            ? $this->contentsArray
            : $this->filterNullValues($this->contentsArray);
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
    public function payloadString(?bool $includeNullEntries = null) : string
    {
        return $this->resolvePayloadString($this->payloadContentsArray($includeNullEntries));
    }
    
    /**
     * @param array $dataArray
     *
     * @return string
     */
    private function resolvePayloadString(array $dataArray) : string
    {
        $eventTime = $dataArray[PayloadFields::EVENT_TIME];
        $dataArray[PayloadFields::EVENT_TIME] = self::$dateTimeNormalizer->normalize($eventTime);
        
        return self::$jsonEncoder->encode($dataArray, JsonEncoder::FORMAT);
    }
}
