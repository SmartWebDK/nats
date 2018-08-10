<?php
declare(strict_types = 1);


namespace SmartWeb\Nats\Payload\Serialization;

use SmartWeb\CloudEvents\Nats\Event\Event;
use SmartWeb\CloudEvents\Nats\Event\EventFields;
use SmartWeb\CloudEvents\Nats\Event\EventInterface;
use Symfony\Component\Serializer\Exception\ExtraAttributesException;
use Symfony\Component\Serializer\Exception\InvalidArgumentException;
use Symfony\Component\Serializer\Exception\RuntimeException;
use Symfony\Component\Serializer\Exception\UnexpectedValueException;
use Symfony\Component\Serializer\Normalizer\DenormalizerInterface;

/**
 * Denormalizer responsible for denormalizing CloudEvent events.
 *
 * @author Nicolai Agersbæk <na@smartweb.dk>
 *
 * @api
 */
class EventDenormalizer implements DenormalizerInterface
{
    
    // TODO: Match $format parameter against supported schema URLs, mapping schemas to denormalization strategies?
    
    /**
     * Denormalizes data back into an object of the given class.
     *
     * @param mixed  $data    Data to restore
     * @param string $class   The expected class to instantiate
     * @param string $format  Format the given data was extracted from
     * @param array  $context Options available to the denormalizer
     *
     * @return EventInterface
     *
     * @throws InvalidArgumentException Occurs when the arguments are not coherent or not supported
     * @throws UnexpectedValueException Occurs when the item cannot be hydrated with the given data
     * @throws ExtraAttributesException Occurs when the item doesn't have attribute to receive given data
     * @throws RuntimeException         Occurs if the class cannot be instantiated
     */
    public function denormalize($data, $class, $format = null, ?array $context = null) : EventInterface
    {
        if (!$this->targetIsSupported($class)) {
            $this->invalidArgumentException(
                'The given target class is not supported. Expected one of: %s',
                $this->getSupportedTargets()
            );
        }
        
        if (!$this->dataTypeIsSupported($data)) {
            throw new InvalidArgumentException('The given data must be an array');
        }
        
        if (!$this->hasRequiredFields($data)) {
            $this->invalidArgumentException(
                'The given data is invalid. Missing required fields: %s',
                $this->getMissingFields($data)
            );
        }
        
        if ($this->hasExtraAttributes($data)) {
            throw new ExtraAttributesException($this->getExtraAttributes($data));
        }
        
        return $this->createEvent($data, $class);
    }
    
    /**
     * @param string $format
     * @param array  $values
     */
    private function invalidArgumentException(string $format, array $values) : void
    {
        throw new InvalidArgumentException($this->hydrateExceptionMessage($format, $values));
    }
    
    /**
     * @param string $format
     * @param array  $values
     *
     * @return string
     */
    private function hydrateExceptionMessage(string $format, array $values) : string
    {
        return \sprintf($format, \implode(', ', $values));
    }
    
    /**
     * @param array  $data
     * @param string $class
     *
     * @return EventInterface
     */
    private function createEvent(array $data, string $class) : EventInterface
    {
        return new $class(...\array_values($data));
    }
    
    /**
     * @inheritDoc
     */
    public function supportsDenormalization($data, $type, $format = null) : bool
    {
        // TODO: Refactor to separate validator class
        return $this->supportsFormat($format)
               && $this->targetIsSupported($type)
               && $this->dataTypeIsSupported($data)
               && $this->hasRequiredFields($data)
               && !$this->hasExtraAttributes($data);
    }
    
    /**
     * @param null|string $format
     *
     * @return bool
     */
    private function supportsFormat(?string $format) : bool
    {
        return $format !== null && \in_array($format, $this->getSupportedFormats(), true);
    }
    
    /**
     * @return string[]
     */
    private function getSupportedFormats() : array
    {
        return [
            EventDecoder::FORMAT,
        ];
    }
    
    /**
     * @param string $type
     *
     * @return bool
     */
    private function targetIsSupported(string $type) : bool
    {
        return \in_array($type, $this->getSupportedTargets(), true);
    }
    
    /**
     * @return string[]
     */
    private function getSupportedTargets() : array
    {
        return [
            Event::class,
        ];
    }
    
    /**
     * @param mixed $data
     *
     * @return bool
     */
    private function dataTypeIsSupported($data) : bool
    {
        return \is_array($data);
    }
    
    /**
     * @param array $data
     *
     * @return bool
     */
    private function hasRequiredFields(array $data) : bool
    {
        return empty($this->getMissingFields($data));
    }
    
    /**
     * @param array $data
     *
     * @return string[]
     */
    private function getMissingFields(array $data) : array
    {
        return \array_diff(EventFields::getRequiredFields(), \array_keys($data));
    }
    
    /**
     * @param array $data
     *
     * @return bool
     */
    private function hasExtraAttributes(array $data) : bool
    {
        return !empty($this->getExtraAttributes($data));
    }
    
    /**
     * @param array $data
     *
     * @return array
     */
    private function getExtraAttributes(array $data) : array
    {
        return \array_diff(\array_keys($data), EventFields::getSupportedFields());
    }
}
