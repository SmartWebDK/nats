<?php
declare(strict_types = 1);


namespace SmartWeb\Nats\Encoding;

use SmartWeb\Nats\Payload\PayloadField;
use SmartWeb\Nats\Payload\PayloadInterface;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;

/**
 * Class PayloadNormalizer
 */
class PayloadNormalizer implements NormalizerInterface
{
    
    /**
     * @inheritDoc
     */
    public function normalize($object, $format = null, array $context = [])
    {
        if (!$this->supportsNormalization($object)) {
            throw new \InvalidArgumentException('The given data is not supported by this normalizer.');
        }
        
        return $this->convertPayloadToArray($object);
    }
    
    /**
     * @param PayloadInterface $payload
     *
     * @return array
     */
    private function convertPayloadToArray(PayloadInterface $payload) : array
    {
        return [
            PayloadField::EVENT_TYPE           => $payload->getEventType(),
            PayloadField::EVENT_TYPE_VERSION   => $payload->getEventTypeVersion(),
            PayloadField::CLOUD_EVENTS_VERSION => $payload->getCloudEventsVersion(),
            PayloadField::SOURCE               => $payload->getSource(),
            PayloadField::EVENT_ID             => $payload->getEventId(),
            PayloadField::EVENT_TIME           => $payload->getEventTime(),
            PayloadField::SCHEMA_URL           => $payload->getSchemaURL(),
            PayloadField::CONTENT_TYPE         => $payload->getContentType(),
            PayloadField::EXTENSIONS           => $payload->getExtensions(),
            PayloadField::DATA                 => $payload->getData(),
        ];
    }
    
    /**
     * @inheritDoc
     */
    public function supportsNormalization($data, $format = null)
    {
        return $data instanceof PayloadInterface;
    }
}
