<?php
declare(strict_types = 1);


namespace SmartWeb\Nats\Payload\Serialization;

use SmartWeb\Nats\Payload\Payload;
use SmartWeb\Nats\Payload\PayloadFields;
use Symfony\Component\Serializer\Exception\BadMethodCallException;
use Symfony\Component\Serializer\Exception\ExtraAttributesException;
use Symfony\Component\Serializer\Exception\InvalidArgumentException;
use Symfony\Component\Serializer\Exception\LogicException;
use Symfony\Component\Serializer\Exception\RuntimeException;
use Symfony\Component\Serializer\Exception\UnexpectedValueException;
use Symfony\Component\Serializer\Normalizer\DenormalizerInterface;

/**
 * Class PayloadDenormalizer
 *
 * @api
 */
class PayloadDenormalizer implements DenormalizerInterface
{
    
    // TODO: Match $format parameter against support schema URLs, mapping schemas to denormalization strategies?
    
    /**
     * Denormalizes data back into an object of the given class.
     *
     * @param mixed  $data    Data to restore
     * @param string $class   The expected class to instantiate
     * @param string $format  Format the given data was extracted from
     * @param array  $context Options available to the denormalizer
     *
     * @return object
     *
     * @throws InvalidArgumentException Occurs when the arguments are not coherent or not supported
     * @throws UnexpectedValueException Occurs when the item cannot be hydrated with the given data
     * @throws ExtraAttributesException Occurs when the item doesn't have attribute to receive given data
     * @throws LogicException           Occurs when the normalizer is not supposed to denormalize
     * @throws RuntimeException         Occurs if the class cannot be instantiated
     */
    public function denormalize($data, $class, $format = null, ?array $context = null)
    {
        $context = $context ?? [];
        
        if (!$this->supportsDenormalization($data, $class)) {
            throw new \InvalidArgumentException('The given data is not supported by this denormalizer.');
        }
        
        return new $class(...\array_values($data));
    }
    
    /**
     * @inheritDoc
     */
    public function supportsDenormalization($data, $type, $format = null) : bool
    {
        // TODO: Refactor to separate validator class
        return \is_array($data)
               && $this->typeSupportsDenormalization($type)
               && $this->dataHasRequiredFields($data)
               && $this->dataHasOnlySupportedFields($data);
    }
    
    /**
     * @param string $type
     *
     * @return bool
     */
    private function typeSupportsDenormalization(string $type) : bool
    {
        return \in_array($type, $this->getSupportedTypesForDenormalization(), true);
    }
    
    /**
     * @return string[]
     */
    private function getSupportedTypesForDenormalization() : array
    {
        return [
            Payload::class,
        ];
    }
    
    /**
     * @param array $data
     *
     * @return bool
     */
    private function dataHasRequiredFields(array $data) : bool
    {
        return \count($this->getMissingFields($data)) === 0;
    }
    
    /**
     * @param array $data
     *
     * @return string[]
     */
    private function getMissingFields(array $data) : array
    {
        return \array_diff(PayloadFields::getRequiredFields(), \array_keys($data));
    }
    
    /**
     * @param array $data
     *
     * @return bool
     */
    private function dataHasOnlySupportedFields(array $data) : bool
    {
        return \count($this->getUnsupportedFields($data)) === 0;
    }
    
    /**
     * @param array $data
     *
     * @return array
     */
    private function getUnsupportedFields(array $data) : array
    {
        return \array_diff(\array_keys($data), PayloadFields::getSupportedFields());
    }
}
