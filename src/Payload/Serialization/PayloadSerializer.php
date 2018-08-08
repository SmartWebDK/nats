<?php
declare(strict_types = 1);


namespace SmartWeb\Nats\Payload\Serialization;

use SmartWeb\Nats\Message\Message;
use SmartWeb\Nats\Message\Serialization\MessageDecoder;
use SmartWeb\Nats\Message\Serialization\MessageDenormalizer;
use SmartWeb\Nats\Payload\Payload;
use SmartWeb\Nats\Payload\PayloadInterface;
use Symfony\Component\Serializer\Encoder\DecoderInterface;
use Symfony\Component\Serializer\Encoder\EncoderInterface;
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Serializer\Normalizer\DenormalizerInterface;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;

/**
 * Class PayloadSerializer
 *
 * @api
 */
class PayloadSerializer implements PayloadSerializerInterface
{
    
    /**
     * @var NormalizerInterface
     */
    private $normalizer;
    
    /**
     * @var DenormalizerInterface
     */
    private $denormalizer;
    
    /**
     * @var EncoderInterface
     */
    private $encoder;
    
    /**
     * @var DecoderInterface
     */
    private $decoder;
    
    /**
     * PayloadSerializer constructor.
     *
     * @param NormalizerInterface   $normalizer
     * @param DenormalizerInterface $denormalizer
     * @param EncoderInterface      $encoder
     * @param DecoderInterface      $decoder
     */
    public function __construct(
        NormalizerInterface $normalizer,
        DenormalizerInterface $denormalizer,
        EncoderInterface $encoder,
        DecoderInterface $decoder
    ) {
        $this->normalizer = $normalizer;
        $this->denormalizer = $denormalizer;
        $this->encoder = $encoder;
        $this->decoder = $decoder;
    }
    
    /**
     * @param PayloadInterface $payload
     *
     * @return string
     */
    public function serialize(PayloadInterface $payload) : string
    {
        return $this->encoder->encode($this->normalizer->normalize($payload), JsonEncoder::FORMAT);
    }
    
    /**
     * @param string $payload
     *
     * @return PayloadInterface
     */
    public function deserialize(string $payload) : PayloadInterface
    {
        $msgDecoder = new MessageDecoder();
        $msgDenormalizer = new MessageDenormalizer();
        
        $msgArray = $msgDecoder->decode($payload, MessageDecoder::FORMAT);
        $msgObject = $msgDenormalizer->denormalize($msgArray, Message::class);
        
        $payloadString = $msgObject->getData();
        
        $payloadDecoder = new PayloadDecoder();
        $payloadDenormalizer = new PayloadDenormalizer();
        
        $payloadArray = $payloadDecoder->decode($payloadString, PayloadDecoder::FORMAT);
        $payloadObject = $payloadDenormalizer->denormalize($payloadArray, Payload::class);
        
        return $payloadObject;
    }
}
