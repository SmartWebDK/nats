<?php
declare(strict_types = 1);


namespace SmartWeb\Nats\Event\Serialization;

use SmartWeb\CloudEvents\Nats\Event\EventFields;
use Symfony\Component\Serializer\Encoder\DecoderInterface;
use Symfony\Component\Serializer\Encoder\JsonDecode;
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Serializer\Exception\InvalidArgumentException;
use Symfony\Component\Serializer\Exception\UnexpectedValueException;

/**
 * Decoder responsible for decoding CloudEvents-formatted strings into arrays.
 *
 * @author Nicolai Agersbæk <na@smartweb.dk>
 *
 * @api
 */
class EventDecoder implements DecoderInterface
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
        
        return $this->decodeEventString($data);
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
     * @param string $event
     *
     * @return string[]
     */
    private function decodeEventString(string $event) : array
    {
        $jsonDecoded = self::getJsonDecoder()->decode($event, JsonEncoder::FORMAT);
        
        return $this->padEventArrayWithNullEntries($jsonDecoded);
    }
    
    /**
     * @param array $array
     *
     * @return array
     */
    private function padEventArrayWithNullEntries(array $array) : array
    {
        foreach (EventFields::getSupportedFields() as $field) {
            $array[$field] = $array[$field] ?? null;
        }
        
        return $array;
    }
    
    /**
     * @return JsonDecode
     */
    private static function getJsonDecoder() : JsonDecode
    {
        return self::$jsonDecoder ?? self::$jsonDecoder = new JsonDecode(true);
    }
}
