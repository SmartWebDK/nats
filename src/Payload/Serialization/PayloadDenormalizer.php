<?php
declare(strict_types = 1);


namespace SmartWeb\Nats\Payload\Serialization;

use SmartWeb\Nats\Payload\Payload;
use SmartWeb\Nats\Payload\PayloadFields;
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
     * @inheritDoc
     */
    public function denormalize($data, $class, $format = null, array $context = [])
    {
        if (!$this->supportsDenormalization($data, $class)) {
            throw new \InvalidArgumentException('The given data is not supported by this denormalizer.');
        }
        
        return new $class(...\array_values($data));
    }
    
    /**
     * @inheritDoc
     */
    public function supportsDenormalization($data, $type, $format = null)
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
