<?php
declare(strict_types = 1);


namespace SmartWeb\Nats\Support;

use Symfony\Component\Serializer\Exception\NotEncodableValueException;
use Symfony\Component\Serializer\Exception\NotNormalizableValueException;

/**
 * Defines the interface of an class capable of deserializing data into a given type.
 *
 * @author Nicolai Agersbæk <na@smartweb.dk>
 *
 * @api
 */
interface DeserializerInterface
{
    
    /**
     * Deserializes data into the given type.
     *
     * @param mixed      $data
     * @param string     $type
     * @param string     $format
     * @param array|null $context
     *
     * @return object
     *
     * @throws NotEncodableValueException    Occurs when decoding for the given format is not supported.
     * @throws NotNormalizableValueException Occurs when denormalization for the given format is not supported.
     */
    public function deserialize($data, string $type, string $format, ?array $context = null);
}
