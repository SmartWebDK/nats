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
 * Provides sample data for payload serialization tests.
 *
 * @author Nicolai Agersbæk <na@smartweb.dk>
 *
 * @internal
 */
class PayloadProvider
{
    
    /**
     * @var array[]
     */
    private static $cached = [
        'complete' => [],
        'minimal'  => [],
    ];
    
    /**
     * @var NormalizerInterface
     */
    private static $dateTimeNormalizer;
    
    /**
     * @var string
     */
    private static $dateTimeFormat;
    
    /**
     * @var EncoderInterface
     */
    private static $jsonEncoder;
    
    public function __construct()
    {
        self::$jsonEncoder = self::$jsonEncoder ?? new JsonEncode();
        self::$dateTimeFormat = self::$dateTimeFormat ?? \DateTime::RFC3339;
        self::$dateTimeNormalizer = self::$dateTimeNormalizer ?? new DateTimeNormalizer(self::$dateTimeFormat);
    }
    
    /**
     * @return PayloadInterface
     */
    public function getCompletePayload() : PayloadInterface
    {
        return self::$cached['complete']['payload'] ??
               self::$cached['complete']['payload'] = $this->resolveCompletePayload();
    }
    
    /**
     * @return array
     */
    public function getCompletePayloadContents() : array
    {
        return self::$cached['complete']['payloadContents'] ??
               self::$cached['complete']['payloadContents'] = $this->resolveCompletePayloadContents();
    }
    
    /**
     * @return array
     */
    public function getCompletePayloadContentsArray() : array
    {
        return self::$cached['complete']['payloadContentsArray'] ??
               self::$cached['complete']['payloadContentsArray'] = $this->resolveCompletePayloadContentsArray();
    }
    
    /**
     * @return string
     */
    public function getCompletePayloadString() : string
    {
        return self::$cached['complete']['payloadString'] ??
               self::$cached['complete']['payloadString'] = $this->resolveCompletePayloadString();
    }
    
    /**
     * @return PayloadInterface
     */
    public function getMinimalPayload() : PayloadInterface
    {
        return self::$cached['minimal']['payload'] ??
               self::$cached['minimal']['payload'] = $this->resolveMinimalPayload();
    }
    
    /**
     * @return array
     */
    public function getMinimalPayloadContents() : array
    {
        return self::$cached['minimal']['payloadContents'] ??
               self::$cached['minimal']['payloadContents'] = $this->resolveMinimalPayloadContents();
    }
    
    /**
     * @return array
     */
    public function getMinimalPayloadContentsArray() : array
    {
        return self::$cached['minimal']['payloadContentsArray'] ??
               self::$cached['minimal']['payloadContentsArray'] = $this->resolveMinimalPayloadContentsArray();
    }
    
    /**
     * @return string
     */
    public function getMinimalPayloadString() : string
    {
        return self::$cached['minimal']['payloadString'] ??
               self::$cached['minimal']['payloadString'] = $this->resolveMinimalPayloadString();
    }
    
    /**
     * @return PayloadInterface
     */
    private function resolveCompletePayload() : PayloadInterface
    {
        $contents = $this->getCompletePayloadContents();
        
        return $this->resolvePayloadFromContents($contents);
    }
    
    /**
     * @return array
     */
    private function resolveCompletePayloadContents() : array
    {
        $dataArray = $this->getCompletePayloadContentsArray();
        
        return $this->resolvePayloadContentsFromDataArray($dataArray);
    }
    
    /**
     * @return string
     */
    private function resolveCompletePayloadString() : string
    {
        $dataArray = $this->getCompletePayloadContentsArray();
        
        return $this->resolvePayloadStringFromDataArray($dataArray);
    }
    
    /**
     * @return array
     */
    private function resolveCompletePayloadContentsArray() : array
    {
        return [
            PayloadFields::EVENT_TYPE           => 'some.event',
            PayloadFields::EVENT_TYPE_VERSION   => '1.0.0',
            PayloadFields::CLOUD_EVENTS_VERSION => '0.1.0',
            PayloadFields::SOURCE               => 'some.source',
            PayloadFields::EVENT_ID             => 'some.event.id',
            PayloadFields::EVENT_TIME           => $this->getFixedTime(),
            PayloadFields::SCHEMA_URL           => 'https://www.some-schema.org/cloud-events/test.schema?version=2.3.4',
            PayloadFields::CONTENT_TYPE         => 'application/json',
            PayloadFields::EXTENSIONS           => [
                'com.foo.extension' => 'barExtension',
            ],
            PayloadFields::DATA                 => [
                'foo' => 'bar',
            ],
        ];
    }
    
    /**
     * @return PayloadInterface
     */
    private function resolveMinimalPayload() : PayloadInterface
    {
        $contents = $this->getMinimalPayloadContents();
        
        return $this->resolvePayloadFromContents($contents);
    }
    
    /**
     * @return array
     */
    private function resolveMinimalPayloadContents() : array
    {
        $dataArray = $this->getMinimalPayloadContentsArray();
        
        return $this->resolvePayloadContentsFromDataArray($dataArray);
    }
    
    /**
     * @return string
     */
    private function resolveMinimalPayloadString() : string
    {
        $dataArray = $this->getCompletePayloadContentsArray();
        
        return $this->resolvePayloadStringFromDataArray($dataArray);
    }
    
    /**
     * @return array
     */
    private function resolveMinimalPayloadContentsArray() : array
    {
        $requiredFields = PayloadFields::getRequiredFields();
        $data = $this->getCompletePayloadContentsArray();
        
        foreach ($data as $field => &$value) {
            $value = \in_array($field, $requiredFields, true)
                ? $value
                : null;
        }
        
        return $data;
    }
    
    /**
     * @param array $contents
     *
     * @return PayloadInterface
     */
    private function resolvePayloadFromContents(array $contents) : PayloadInterface
    {
        return new Payload(...\array_values($contents));
    }
    
    /**
     * @param array $dataArray
     *
     * @return array
     */
    private function resolvePayloadContentsFromDataArray(array $dataArray) : array
    {
        $dataArray[PayloadFields::DATA] = new ArrayData($dataArray[PayloadFields::DATA]);
        
        return $dataArray;
    }
    
    /**
     * @param array $dataArray
     *
     * @return string
     */
    private function resolvePayloadStringFromDataArray(array $dataArray) : string
    {
        $eventTime = $dataArray[PayloadFields::EVENT_TIME];
        $dataArray[PayloadFields::EVENT_TIME] = self::$dateTimeNormalizer->normalize($eventTime);
        
        return self::$jsonEncoder->encode($dataArray, JsonEncoder::FORMAT);
    }
    
    /**
     * @return \DateTimeInterface
     */
    private function getFixedTime() : \DateTimeInterface
    {
        $time = new \DateTime();
        $time->setDate(2000, 1, 2);
        $time->setTime(12, 34, 56);
        
        $timeZone = new \DateTimeZone('Europe/Copenhagen');
        $time->setTimezone($timeZone);
        
        return $time;
    }
}
