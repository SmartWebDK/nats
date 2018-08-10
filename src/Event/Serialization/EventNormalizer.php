<?php
declare(strict_types = 1);


namespace SmartWeb\Nats\Event\Serialization;

use SmartWeb\CloudEvents\Nats\Event\EventFields;
use SmartWeb\CloudEvents\Nats\Event\EventInterface;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;

/**
 * Normalizer responsible for normalizing CloudEvent events.
 *
 * @author Nicolai Agersbæk <na@smartweb.dk>
 *
 * @api
 */
class EventNormalizer implements NormalizerInterface
{
    
    // FIXME: Possibly replace this with a GetSetMethodNormalizer/PropertyNormalizer?
    
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
     * @param EventInterface $payload
     *
     * @return array
     */
    private function convertPayloadToArray(EventInterface $payload) : array
    {
        return [
            EventFields::EVENT_TYPE           => $payload->getEventType(),
            EventFields::EVENT_TYPE_VERSION   => $payload->getEventTypeVersion(),
            EventFields::CLOUD_EVENTS_VERSION => $payload->getCloudEventsVersion(),
            EventFields::SOURCE               => $payload->getSource(),
            EventFields::EVENT_ID             => $payload->getEventId(),
            EventFields::EVENT_TIME           => $payload->getEventTime(),
            EventFields::SCHEMA_URL           => $payload->getSchemaURL(),
            EventFields::CONTENT_TYPE         => $payload->getContentType(),
            EventFields::EXTENSIONS           => $payload->getExtensions(),
            EventFields::DATA                 => $payload->getData(),
        ];
    }
    
    /**
     * @inheritDoc
     */
    public function supportsNormalization($data, $format = null) : bool
    {
        return $data instanceof EventInterface;
    }
}
