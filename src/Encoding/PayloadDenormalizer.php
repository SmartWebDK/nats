<?php
declare(strict_types = 1);


namespace SmartWeb\Nats\Encoding;

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
    
    /**
     * @inheritDoc
     */
    public function denormalize($data, $class, $format = null, array $context = [])
    {
        // TODO: Implement denormalize() method.
        throw new \BadMethodCallException(__METHOD__ . ' not yet implemented!');
    }
    
    /**
     * @inheritDoc
     */
    public function supportsDenormalization($data, $type, $format = null)
    {
        return \is_array($data)
               && $this->typeSupportsDenormalization($type)
               && $this->dataHasRequiredFields($data);
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
        return $this->getMissingFields($data) === [];
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
}
