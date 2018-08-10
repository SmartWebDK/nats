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
        
        return $this->convertEventToArray($object);
    }
    
    /**
     * @param EventInterface $event
     *
     * @return array
     */
    private function convertEventToArray(EventInterface $event) : array
    {
        return [
            EventFields::EVENT_TYPE           => $event->getEventType(),
            EventFields::EVENT_TYPE_VERSION   => $event->getEventTypeVersion(),
            EventFields::CLOUD_EVENTS_VERSION => $event->getCloudEventsVersion(),
            EventFields::SOURCE               => $event->getSource(),
            EventFields::EVENT_ID             => $event->getEventId(),
            EventFields::EVENT_TIME           => $event->getEventTime(),
            EventFields::SCHEMA_URL           => $event->getSchemaURL(),
            EventFields::CONTENT_TYPE         => $event->getContentType(),
            EventFields::EXTENSIONS           => $event->getExtensions(),
            EventFields::DATA                 => $event->getData(),
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
