<?php
declare(strict_types = 1);


namespace SmartWeb\Nats\Message\Serialization;

use SmartWeb\Nats\Message\Message;
use SmartWeb\Nats\Message\MessageFields;
use Symfony\Component\Serializer\Exception\UnexpectedValueException;
use Symfony\Component\Serializer\Normalizer\DenormalizerInterface;

/**
 * Class MessageDenormalizer
 */
class MessageDenormalizer implements DenormalizerInterface
{
    
    /**
     * @inheritDoc
     */
    public function denormalize($data, $class, $format = null, array $context = [])
    {
        if (!$this->supportsDenormalization($data, $class, $format)) {
            throw new UnexpectedValueException('The given data format is not supported by this denormalizer.');
        }
        
        return new $class(...\array_values($data));
    }
    
    /**
     * @inheritDoc
     */
    public function supportsDenormalization($data, $type, $format = null) : bool
    {
        return $type === Message::class
               && \is_array($data)
               && $this->dataHasRequiredFields($data)
               && $this->dataHasCorrectFieldTypes($data);
    }
    
    /**
     * @param array $data
     *
     * @return bool
     */
    private function dataHasRequiredFields(array $data) : bool
    {
        return MessageFields::getRequiredFields() === \array_keys($data);
    }
    
    /**
     * @param array $data
     *
     * @return bool
     */
    private function dataHasCorrectFieldTypes(array $data) : bool
    {
        return \is_int($data[MessageFields::SEQUENCE])
               && \is_string($data[MessageFields::SUBJECT])
               && \is_string($data[MessageFields::DATA])
               && \is_int($data[MessageFields::TIMESTAMP]);
    }
}
