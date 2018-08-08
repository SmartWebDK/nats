<?php
declare(strict_types = 1);


namespace SmartWeb\Nats\Support;

/**
 * Definition of an object capable of deserializing data into a given type.
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
     */
    public function deserialize($data, string $type, string $format, ?array $context = null);
}
