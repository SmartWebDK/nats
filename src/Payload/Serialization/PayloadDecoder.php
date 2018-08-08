<?php
declare(strict_types = 1);


namespace SmartWeb\Nats\Payload\Serialization;

use SmartWeb\Nats\Payload\PayloadFields;
use Symfony\Component\Serializer\Encoder\DecoderInterface;
use Symfony\Component\Serializer\Encoder\JsonDecode;
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Serializer\Exception\InvalidArgumentException;
use Symfony\Component\Serializer\Exception\UnexpectedValueException;

/**
 * Decoder responsible for decoding payload strings.
 *
 * @api
 */
class PayloadDecoder implements DecoderInterface
{
    
    /**
     * The format supported by this decoder.
     */
    public const FORMAT = 'CLOUD_EVENTS_NATS';
    
    /**
     * @var JsonDecode
     */
    private static $jsonDecoder;
    
    /**
     * Decodes a string into a PHP array.
     *
     * Supported formats are:
     *  - 'CLOUD_EVENTS_NATS'
     *
     * @param string     $data    Data to decode
     * @param string     $format  Format name
     * @param array|null $context Options that decoders have access to
     *
     * @return array
     *
     * @throws UnexpectedValueException
     */
    public function decode($data, $format, ?array $context = null) : array
    {
        if (!$this->formatIsValidType($format)) {
            $actualType = \gettype($format);
            throw new InvalidArgumentException(
                "Expected format to be a string, was '{$actualType}'"
            );
        }
        
        if (!$this->formatIsSupported($format)) {
            throw new UnexpectedValueException(
                "The given data format '{$format}' is not supported by this normalizer."
            );
        }
        
        return $this->decodePayloadString($data);
    }
    
    /**
     * @inheritDoc
     */
    public function supportsDecoding($format) : bool
    {
        return $this->formatIsValidType($format)
               && $this->formatIsSupported($format);
    }
    
    /**
     * @param mixed $format
     *
     * @return bool
     */
    private function formatIsValidType($format) : bool
    {
        return \is_string($format);
    }
    
    /**
     * @param string $format
     *
     * @return bool
     */
    private function formatIsSupported(string $format) : bool
    {
        return $format === self::FORMAT;
    }
    
    /**
     * @param string $payload
     *
     * @return string[]
     */
    private function decodePayloadString(string $payload) : array
    {
        $jsonDecoded = self::getJsonDecoder()->decode($payload, JsonEncoder::FORMAT);
        
        return $this->padPayloadArrayWithNullEntries($jsonDecoded);
    }
    
    /**
     * @param array $array
     *
     * @return array
     */
    private function padPayloadArrayWithNullEntries(array $array) : array
    {
        foreach (PayloadFields::getSupportedFields() as $field) {
            $array[$field] = $array[$field] ?? null;
        }
        
        return $array;
    }
    
    /**
     * @return JsonDecode
     */
    private static function getJsonDecoder() : JsonDecode
    {
        return self::$jsonDecoder = self::$jsonDecoder ?? new JsonDecode(true);
    }
}
