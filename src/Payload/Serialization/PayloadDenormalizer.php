<?php
declare(strict_types = 1);


namespace SmartWeb\Nats\Payload\Serialization;

use SmartWeb\CloudEvents\Nats\Payload\Data\ArrayData;
use SmartWeb\CloudEvents\Nats\Payload\Data\PayloadDataInterface;
use SmartWeb\CloudEvents\Nats\Payload\Data\ScalarData;
use SmartWeb\CloudEvents\Nats\Payload\Payload;
use SmartWeb\CloudEvents\Nats\Payload\PayloadFields;
use SmartWeb\CloudEvents\Nats\Payload\PayloadInterface;
use Symfony\Component\Serializer\Exception\ExtraAttributesException;
use Symfony\Component\Serializer\Exception\InvalidArgumentException;
use Symfony\Component\Serializer\Exception\RuntimeException;
use Symfony\Component\Serializer\Exception\UnexpectedValueException;
use Symfony\Component\Serializer\Normalizer\DateTimeNormalizer;
use Symfony\Component\Serializer\Normalizer\DenormalizerInterface;

/**
 * Denormalizer responsible for denormalizing payloads.
 *
 * @todo   Move denormalization of payload data to separate class
 *
 * @author Nicolai Agersbæk <na@smartweb.dk>
 *
 * @api
 */
class PayloadDenormalizer implements DenormalizerInterface
{
    
    // TODO: Match $format parameter against supported schema URLs, mapping schemas to denormalization strategies?
    
    /**
     * Denormalizes data back into an object of the given class.
     *
     * @param mixed  $data    Data to restore
     * @param string $class   The expected class to instantiate
     * @param string $format  Format the given data was extracted from
     * @param array  $context Options available to the denormalizer
     *
     * @return PayloadInterface
     *
     * @throws InvalidArgumentException Occurs when the arguments are not coherent or not supported
     * @throws UnexpectedValueException Occurs when the item cannot be hydrated with the given data
     * @throws ExtraAttributesException Occurs when the item doesn't have attribute to receive given data
     * @throws RuntimeException         Occurs if the class cannot be instantiated
     */
    public function denormalize($data, $class, $format = null, ?array $context = null) : PayloadInterface
    {
        if (!$this->targetIsSupported($class)) {
            $this->invalidArgumentException(
                'The given target class is not supported. Expected one of: %s',
                $this->getSupportedTargets()
            );
        }
        
        if (!$this->dataTypeIsSupported($data)) {
            throw new InvalidArgumentException('The given data must be an array');
        }
        
        if (!$this->hasRequiredFields($data)) {
            $this->invalidArgumentException(
                'The given data is invalid. Missing required fields: %s',
                $this->getMissingFields($data)
            );
        }
        
        if ($this->hasExtraAttributes($data)) {
            throw new ExtraAttributesException($this->getExtraAttributes($data));
        }
        
        return $this->createPayload($data, $class);
    }
    
    /**
     * @param string $format
     * @param array  $values
     */
    private function invalidArgumentException(string $format, array $values) : void
    {
        throw new InvalidArgumentException($this->hydrateExceptionMessage($format, $values));
    }
    
    /**
     * @param string $format
     * @param array  $values
     *
     * @return string
     */
    private function hydrateExceptionMessage(string $format, array $values) : string
    {
        return \sprintf($format, \implode(', ', $values));
    }
    
    /**
     * @param array  $data
     * @param string $class
     *
     * @return PayloadInterface
     */
    private function createPayload(array $data, string $class) : PayloadInterface
    {
        $data[PayloadFields::EVENT_TIME] = $this->denormalizeEventTime($data[PayloadFields::EVENT_TIME]);
        $data[PayloadFields::DATA] = $this->denormalizePayloadData($data[PayloadFields::DATA]);
        
        return new $class(...\array_values($data));
    }
    
    /**
     * @param null|string $date
     *
     * @return \DateTimeInterface|null
     */
    private function denormalizeEventTime(?string $date) : ?\DateTimeInterface
    {
        return $date !== null
            ? $this->denormalizeDate($date)
            : null;
    }
    
    /**
     * @param string $date
     *
     * @return \DateTimeInterface
     */
    private function denormalizeDate(string $date) : \DateTimeInterface
    {
        $denormalizer = new DateTimeNormalizer(\DateTime::RFC3339);
        
        return $denormalizer->denormalize($date, \DateTimeImmutable::class);
    }
    
    /**
     * @param $payloadData
     *
     * @return null|PayloadDataInterface
     */
    private function denormalizePayloadData($payloadData) : ?PayloadDataInterface
    {
        // FIXME: Refactor to separate class.
        $format = $this->getPayloadDataFormat($payloadData);
        
        return $this->getPayloadDataDenormalizer($format)($payloadData);
    }
    
    /**
     * @param mixed $payloadData
     *
     * @return string
     */
    private function getPayloadDataFormat($payloadData) : string
    {
        static $payloadDataFormatToTypeMapping = [
            'NULL'   => 'null',
            'array'  => 'array',
            'bool'   => 'scalar',
            'float'  => 'scalar',
            'int'    => 'scalar',
            'string' => 'scalar',
        ];
        
        $type = \gettype($payloadData);
        
        return $payloadDataFormatToTypeMapping[$type] ?? $type;
    }
    
    /**
     * @param string $format
     *
     * @return callable
     */
    private function getPayloadDataDenormalizer(string $format) : callable
    {
        $denormalizer = $this->getPayloadDataDenormalizers()[$format] ?? null;
        
        if ($denormalizer === null) {
            throw new UnexpectedValueException("No payload data denormalizer found for format '{$format}'");
        }
        
        return $denormalizer;
    }
    
    /**
     * @return callable[]
     */
    private function getPayloadDataDenormalizers() : array
    {
        return [
            'null'   => function () : ?PayloadDataInterface {
                return null;
            },
            'array'  => function (array $payloadData) : PayloadDataInterface {
                return new ArrayData($payloadData);
            },
            'scalar' => function ($payloadData) : PayloadDataInterface {
                return new ScalarData($payloadData);
            },
        ];
    }
    
    /**
     * @inheritDoc
     */
    public function supportsDenormalization($data, $type, $format = null) : bool
    {
        // TODO: Refactor to separate validator class
        return $this->supportsFormat($format)
               && $this->targetIsSupported($type)
               && $this->dataTypeIsSupported($data)
               && $this->hasRequiredFields($data)
               && !$this->hasExtraAttributes($data);
    }
    
    /**
     * @param null|string $format
     *
     * @return bool
     */
    private function supportsFormat(?string $format) : bool
    {
        return $format !== null && \in_array($format, $this->getSupportedFormats(), true);
    }
    
    /**
     * @return string[]
     */
    private function getSupportedFormats() : array
    {
        return [
            PayloadDecoder::FORMAT,
        ];
    }
    
    /**
     * @param string $type
     *
     * @return bool
     */
    private function targetIsSupported(string $type) : bool
    {
        return \in_array($type, $this->getSupportedTargets(), true);
    }
    
    /**
     * @return string[]
     */
    private function getSupportedTargets() : array
    {
        return [
            Payload::class,
        ];
    }
    
    /**
     * @param mixed $data
     *
     * @return bool
     */
    private function dataTypeIsSupported($data) : bool
    {
        return \is_array($data);
    }
    
    /**
     * @param array $data
     *
     * @return bool
     */
    private function hasRequiredFields(array $data) : bool
    {
        return empty($this->getMissingFields($data));
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
    private function hasExtraAttributes(array $data) : bool
    {
        return !empty($this->getExtraAttributes($data));
    }
    
    /**
     * @param array $data
     *
     * @return array
     */
    private function getExtraAttributes(array $data) : array
    {
        return \array_diff(\array_keys($data), PayloadFields::getSupportedFields());
    }
}
