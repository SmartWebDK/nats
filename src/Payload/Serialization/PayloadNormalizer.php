<?php
declare(strict_types = 1);


namespace SmartWeb\Nats\Payload\Serialization;

use SmartWeb\CloudEvents\Nats\Payload\PayloadFields;
use SmartWeb\CloudEvents\Nats\Payload\PayloadInterface;
use Symfony\Component\Serializer\Normalizer\DateTimeNormalizer;
use Symfony\Component\Serializer\Normalizer\JsonSerializableNormalizer;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;

/**
 * Normalizer responsible for normalizing payloads.
 *
 * @author Nicolai Agersbæk <na@smartweb.dk>
 *
 * @api
 */
class PayloadNormalizer implements NormalizerInterface
{
    
    /**
     * @var NormalizerInterface[]
     */
    private static $fieldNormalizers = [];
    
    /**
     * @inheritDoc
     */
    public function normalize($object, $format = null, array $context = null) : array
    {
        if (!$this->supportsNormalization($object)) {
            throw new \InvalidArgumentException('The given data is not supported by this normalizer.');
        }
        
        return $this->normalizePayload($object);
    }
    
    /**
     * @param PayloadInterface $payload
     *
     * @return array
     */
    private function normalizePayload(PayloadInterface $payload) : array
    {
        $data = $this->convertPayloadToArray($payload);
        
        foreach ($data as $field => &$value) {
            $value = $this->normalizePayloadField($field, $value);
        }
        
        return $data;
    }
    
    /**
     * @param PayloadInterface $payload
     *
     * @return array
     */
    private function convertPayloadToArray(PayloadInterface $payload) : array
    {
        return [
            PayloadFields::EVENT_TYPE           => $payload->getEventType(),
            PayloadFields::EVENT_TYPE_VERSION   => $payload->getEventTypeVersion(),
            PayloadFields::CLOUD_EVENTS_VERSION => $payload->getCloudEventsVersion(),
            PayloadFields::SOURCE               => $payload->getSource(),
            PayloadFields::EVENT_ID             => $payload->getEventId(),
            PayloadFields::EVENT_TIME           => $payload->getEventTime(),
            PayloadFields::SCHEMA_URL           => $payload->getSchemaURL(),
            PayloadFields::CONTENT_TYPE         => $payload->getContentType(),
            PayloadFields::EXTENSIONS           => $payload->getExtensions(),
            PayloadFields::DATA                 => $payload->getData(),
        ];
    }
    
    /**
     * @param string                             $field
     * @param array|bool|float|int|string|object $value
     *
     * @return array|bool|float|int|string|object
     */
    private function normalizePayloadField(string $field, $value)
    {
        $normalizer = $this->getFieldNormalizer($field);
        
        return $normalizer === null
            ? $value
            : $normalizer->normalize($value);
    }
    
    /**
     * @param string $field
     *
     * @return null|NormalizerInterface
     */
    private function getFieldNormalizer(string $field) : ?NormalizerInterface
    {
        return self::getFieldNormalizers()[$field] ?? null;
    }
    
    /**
     * @return NormalizerInterface[]
     */
    private static function getFieldNormalizers() : array
    {
        return self::$fieldNormalizers ?? self::$fieldNormalizers = self::resolveFieldNormalizers();
    }
    
    /**
     * @return NormalizerInterface[]
     */
    private static function resolveFieldNormalizers() : array
    {
        return [
            PayloadFields::EVENT_TIME => new DateTimeNormalizer(\DateTime::RFC3339),
            PayloadFields::DATA       => new JsonSerializableNormalizer(),
        ];
    }
    
    /**
     * @inheritDoc
     */
    public function supportsNormalization($data, $format = null) : bool
    {
        return $data instanceof PayloadInterface;
    }
}
