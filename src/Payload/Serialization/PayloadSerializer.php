<?php
declare(strict_types = 1);


namespace SmartWeb\Nats\Payload\Serialization;

use SmartWeb\Nats\Payload\Payload;
use SmartWeb\Nats\Payload\PayloadInterface;
use Symfony\Component\Serializer\Encoder\JsonEncode;
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Serializer\Serializer;
use Symfony\Component\Serializer\SerializerInterface;

/**
 * Class PayloadSerializer
 *
 * @api
 */
class PayloadSerializer implements PayloadSerializerInterface
{
    
    /**
     * @var SerializerInterface
     */
    private $serializer;
    
    /**
     * PayloadSerializer constructor.
     *
     * @param PayloadNormalizer   $normalizer
     * @param JsonEncode          $encoder
     * @param PayloadDecoder      $decoder
     * @param PayloadDenormalizer $denormalizer
     */
    public function __construct(
        PayloadNormalizer $normalizer,
        JsonEncode $encoder,
        PayloadDecoder $decoder,
        PayloadDenormalizer $denormalizer
    ) {
        $this->serializer = new Serializer(
            [$normalizer, $denormalizer],
            [$encoder, $decoder]
        );
    }
    
    /**
     * @param PayloadInterface $payload
     *
     * @return string
     */
    public function serialize(PayloadInterface $payload) : string
    {
        return $this->serializer->serialize($payload, JsonEncoder::FORMAT);
    }
    
    /**
     * @param string $payload
     *
     * @return PayloadInterface
     */
    public function deserialize(string $payload) : PayloadInterface
    {
        return $this->serializer->deserialize($payload, Payload::class, PayloadDecoder::FORMAT);
    }
}
