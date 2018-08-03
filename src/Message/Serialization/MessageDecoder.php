<?php
declare(strict_types = 1);


namespace SmartWeb\Nats\Message\Serialization;

use Symfony\Component\Serializer\Encoder\DecoderInterface;
use Symfony\Component\Serializer\Encoder\JsonDecode;
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Serializer\Exception\UnexpectedValueException;

/**
 * Class MessageDecoder
 */
class MessageDecoder implements DecoderInterface
{
    
    /**
     * The format supported by this decoder.
     */
    public const FORMAT = 'NATS_STREAMING';
    
    /**
     * @var JsonDecode
     */
    private static $jsonDecoder;
    
    /**
     * @var callable[]
     */
    private static $messageLineProcessors;
    
    /**
     * Decodes a string into PHP data.
     *
     * Supported formats are:
     *  - 'NATS_STREAMING'
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
        if (!$this->supportsDecoding($format)) {
            throw new UnexpectedValueException('The given data format is not supported by this normalizer.');
        }
        
        return $this->decodeMessageString($data);
    }
    
    /**
     * @param string $message
     *
     * @return string[]
     */
    private function decodeMessageString(string $message) : array
    {
        return $this->processMessageLines($this->parseMessageString($message));
    }
    
    /**
     * @param string $message
     *
     * @return string[]
     */
    private function parseMessageString(string $message) : array
    {
        $pattern = '/(\w+)\:\s(.*)/';
        
        $order = PREG_SET_ORDER;
        
        \preg_match_all($pattern, $message, $matches, $order);
        
        return \array_column($matches, 2, 1);
    }
    
    /**
     * @param array $lines
     *
     * @return array
     */
    private function processMessageLines(array $lines) : array
    {
        $processed = [];
        
        foreach ($lines as $key => $line) {
            $processed[$key] = $this->processMessageLine($key, $line);
        }
        
        return $processed;
    }
    
    /**
     * @param string $key
     * @param string $value
     *
     * @return mixed
     */
    private function processMessageLine(string $key, string $value)
    {
        return \call_user_func($this->getMessageLineProcessor($key), $value);
    }
    
    /**
     * @param string $key
     *
     * @return callable
     */
    private function getMessageLineProcessor(string $key) : callable
    {
        $processor = self::getMessageLineProcessors(self::getJsonDecoder())[$key] ?? null;
        
        if ($processor === null) {
            throw new \RuntimeException("No processor found for message entry '{$key}'");
        }
        
        return $processor;
    }
    
    /**
     * @param JsonDecode $jsonDecoder
     *
     * @return callable[]
     */
    private static function getMessageLineProcessors(JsonDecode $jsonDecoder) : array
    {
        return self::$messageLineProcessors = self::$messageLineProcessors ??
                                              self::resolveMessageLineProcessors($jsonDecoder);
    }
    
    /**
     * @param JsonDecode $jsonDecoder
     *
     * @return callable[]
     */
    private static function resolveMessageLineProcessors(JsonDecode $jsonDecoder) : array
    {
        return [
            'sequence'  => function (string $value) : int {
                return (int)$value;
            },
            'subject'   => function (string $value) : string {
                return \trim($value, '"');
            },
            'data'      => function (string $value) use ($jsonDecoder) : string {
                $json = \trim($value, '"');
                $json = \str_replace('\"', '"', $json);

//                return $jsonDecoder->decode($json, JsonEncoder::FORMAT);
                return $json;
            },
            'timestamp' => function (string $value) : int {
                return (int)$value;
            },
        ];
    }
    
    /**
     * @return JsonDecode
     */
    private static function getJsonDecoder() : JsonDecode
    {
        return self::$jsonDecoder = self::$jsonDecoder ?? new JsonDecode(true);
    }
    
    /**
     * @inheritDoc
     */
    public function supportsDecoding($format) : bool
    {
        return $format === self::FORMAT;
    }
}
