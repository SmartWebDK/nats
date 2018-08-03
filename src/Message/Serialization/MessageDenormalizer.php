<?php
declare(strict_types = 1);


namespace SmartWeb\Nats\Message\Serialization;

use SmartWeb\Nats\Message\Message;
use SmartWeb\Nats\Message\MessageFields;
use SmartWeb\Nats\Message\MessageInterface;
use Symfony\Component\Serializer\Exception\ExtraAttributesException;
use Symfony\Component\Serializer\Exception\InvalidArgumentException;
use Symfony\Component\Serializer\Exception\RuntimeException;
use Symfony\Component\Serializer\Exception\UnexpectedValueException;
use Symfony\Component\Serializer\Normalizer\DenormalizerInterface;

/**
 * Class MessageDenormalizer
 */
class MessageDenormalizer implements DenormalizerInterface
{
    
    /**
     * Denormalizes data back into an object of the given class.
     *
     * @param mixed       $data    Data to restore
     * @param string      $class   The expected class to instantiate
     * @param string|null $format  Format the given data was extracted from
     * @param array|null  $context Options available to the denormalizer
     *
     * @return MessageInterface
     *
     * @throws InvalidArgumentException Occurs when the arguments are not coherent or not supported
     * @throws UnexpectedValueException Occurs when the item cannot be hydrated with the given data
     * @throws ExtraAttributesException Occurs when the item doesn't have attribute to receive given data
     * @throws RuntimeException         Occurs if the class cannot be instantiated
     */
    public function denormalize($data, $class, $format = null, ?array $context = null) : MessageInterface
    {
        $this->validateDenormalizeInput($data, $class);
        
        return $this->createMessage($data, $class);
    }
    
    /**
     * Validate the data given to the denormalizer.
     *
     * @param mixed       $data    Data to restore
     * @param string      $class   The expected class to instantiate
     * @param string|null $format  Format the given data was extracted from
     * @param array|null  $context Options available to the denormalizer
     *
     * @throws InvalidArgumentException Occurs when the arguments are not coherent or not supported
     * @throws UnexpectedValueException Occurs when the item cannot be hydrated with the given data
     * @throws ExtraAttributesException Occurs when the item doesn't have attribute to receive given data
     */
    private function validateDenormalizeInput($data, string $class, $format = null, ?array $context = null) : void
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
        
        if (!$this->hasCorrectFieldTypes($data)) {
            throw new UnexpectedValueException('The given data contains values of invalid type');
        }
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
     * @return MessageInterface
     */
    private function createMessage(array $data, string $class) : MessageInterface
    {
        return new $class(...\array_values($data));
    }
    
    /**
     * @inheritDoc
     */
    public function supportsDenormalization($data, $type, $format = null) : bool
    {
        return $this->targetIsSupported($type)
               && $this->dataTypeIsSupported($data)
               && $this->hasRequiredFields($data)
               && !$this->hasExtraAttributes($data)
               && $this->hasCorrectFieldTypes($data);
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
            Message::class,
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
     * @return array
     */
    private function getMissingFields(array $data) : array
    {
        return \array_diff(MessageFields::getDefinition()->getRequiredFields(), \array_keys($data));
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
        return \array_diff(\array_keys($data), MessageFields::getDefinition()->getFields());
    }
    
    /**
     * @param array $data
     *
     * @return bool
     */
    private function hasCorrectFieldTypes(array $data) : bool
    {
        return \is_int($data[MessageFields::SEQUENCE])
               && \is_string($data[MessageFields::SUBJECT])
               && \is_string($data[MessageFields::DATA])
               && \is_int($data[MessageFields::TIMESTAMP]);
    }
}
