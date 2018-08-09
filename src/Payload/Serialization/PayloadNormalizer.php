<?php
declare(strict_types = 1);


namespace SmartWeb\Nats\Payload\Serialization;

use SmartWeb\Nats\Payload\PayloadFields;
use SmartWeb\Nats\Payload\PayloadInterface;
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
     * @inheritDoc
     */
    public function normalize($object, $format = null, array $context = null) : array
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
     * @inheritDoc
     */
    public function supportsNormalization($data, $format = null) : bool
    {
        return $data instanceof PayloadInterface;
    }
}
