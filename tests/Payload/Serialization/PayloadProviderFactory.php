<?php
declare(strict_types = 1);


namespace SmartWeb\Nats\Tests\Payload\Serialization;

use SmartWeb\CloudEvents\Nats\Payload\PayloadFields;
use Symfony\Component\Serializer\Encoder\EncoderInterface;
use Symfony\Component\Serializer\Encoder\JsonEncode;
use Symfony\Component\Serializer\Normalizer\DateTimeNormalizer;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;

/**
 * Creates providers of sample data for payload serialization tests.
 *
 * @author Nicolai Agersbæk <na@smartweb.dk>
 *
 * @internal
 */
class PayloadProviderFactory
{
    
    /**
     * @var array
     */
    private static $cachedContentsArray;
    
    /**
     * @var PayloadProviderInterface[]
     */
    private static $cachedProviders = [];
    
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
     * @return PayloadProviderInterface
     */
    public function complete() : PayloadProviderInterface
    {
        $contentsArray = $this->getContentsArray(PayloadFields::getSupportedFields());
        
        return self::$cachedProviders['complete'] ??
               self::$cachedProviders['complete'] = new PayloadProvider($contentsArray);
    }
    
    /**
     * @return PayloadProviderInterface
     */
    public function minimal() : PayloadProviderInterface
    {
        $contentsArray = $this->getContentsArray(PayloadFields::getRequiredFields());
        
        return self::$cachedProviders['minimal'] ??
               self::$cachedProviders['minimal'] = new PayloadProvider($contentsArray);
    }
    
    /**
     * @param string[] $includedFields
     *
     * @return PayloadProviderInterface
     */
    public function custom(array $includedFields) : PayloadProviderInterface
    {
        $contentsArray = $this->getContentsArray($includedFields);
        
        return new PayloadProvider($contentsArray);
    }
    
    /**
     * @param string[] $includedFields
     *
     * @return array
     */
    private function getContentsArray(array $includedFields) : array
    {
        $this->validateFieldListIsCorrect($includedFields);
        
        $data = $this->getCompletePayloadContentsArray();
        
        foreach ($data as $field => &$value) {
            $value = \in_array($field, $includedFields, true)
                ? $value
                : null;
        }
        
        return $data;
    }
    
    /**
     * @param array $fieldList
     */
    private function validateFieldListIsCorrect(array $fieldList) : void
    {
        if ($this->hasMissingFields($fieldList)) {
            $missingFields = \implode("', '", $this->getMissingFields($fieldList));
            throw new \LogicException("Missing fields: ['{$missingFields}']");
        }
        
        if ($this->hasExtraFields($fieldList)) {
            $extraFields = \implode("', '", $this->getExtraFields($fieldList));
            throw new \LogicException("Too many fields: ['{$extraFields}']");
        }
    }
    
    /**
     * @param array $fieldList
     *
     * @return bool
     */
    private function hasMissingFields(array $fieldList) : bool
    {
        return \count($this->getMissingFields($fieldList)) !== 0;
    }
    
    /**
     * @param array $fieldList
     *
     * @return array
     */
    private function getMissingFields(array $fieldList) : array
    {
        return \array_diff(PayloadFields::getRequiredFields(), $fieldList);
    }
    
    /**
     * @param array $fieldList
     *
     * @return bool
     */
    private function hasExtraFields(array $fieldList) : bool
    {
        return \count($this->getExtraFields($fieldList)) !== 0;
    }
    
    /**
     * @param array $fieldList
     *
     * @return array
     */
    private function getExtraFields(array $fieldList) : array
    {
        return \array_diff($fieldList, PayloadFields::getSupportedFields());
    }
    
    /**
     * @return array
     */
    private function getCompletePayloadContentsArray() : array
    {
        return self::$cachedContentsArray ?? self::$cachedContentsArray = $this->resolveCompletePayloadContentsArray();
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
