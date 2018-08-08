<?php
declare(strict_types = 1);


namespace SmartWeb\Nats\Message\Serialization;

use SmartWeb\Nats\Message\MessageInterface;
use SmartWeb\Nats\Support\DeserializerInterface;
use Symfony\Component\Serializer\Exception\NotEncodableValueException;
use Symfony\Component\Serializer\Exception\NotNormalizableValueException;

/**
 * Deserializes message strings from a NATS streaming connection to message objects.
 *
 * @author Nicolai Agersbæk <na@smartweb.dk>
 *
 * @api
 */
final class MessageDeserializer implements DeserializerInterface
{
    
    /**
     * @var MessageDecoder
     */
    private $decoder;
    
    /**
     * @var MessageDenormalizer
     */
    private $denormalizer;
    
    /**
     * @param MessageDecoder      $decoder
     * @param MessageDenormalizer $denormalizer
     */
    public function __construct(MessageDecoder $decoder, MessageDenormalizer $denormalizer)
    {
        $this->decoder = $decoder;
        $this->denormalizer = $denormalizer;
    }
    
    /**
     * @inheritDoc
     */
    public function deserialize($data, string $type, string $format, ?array $context = null)
    {
        if (!$this->decoder->supportsDecoding($format)) {
            throw new NotEncodableValueException("Decoding for the format '{$format}' is not supported");
        }
        
        $decoded = $this->decoder->decode($data, $format, $context);
        
        return $this->denormalize($decoded, $type, $format, $context);
    }
    
    /**
     * @param mixed      $data
     * @param string     $class
     * @param string     $format
     * @param array|null $context
     *
     * @return MessageInterface
     */
    private function denormalize($data, string $class, string $format, ?array $context = null) : MessageInterface
    {
        if (!$this->denormalizer->supportsDenormalization($data, $class, $format)) {
            throw new NotNormalizableValueException(
                "Denormalization for the format '{$format}' and class '{$class}' is not supported"
            );
        }
        
        return $this->denormalizer->denormalize($data, $class, $format, $context);
    }
}
