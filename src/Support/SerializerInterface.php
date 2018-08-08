<?php
declare(strict_types = 1);


namespace SmartWeb\Nats\Support;

/**
 * Definition of an object capable of serializing data into the appropriate format.
 *
 * @api
 */
interface SerializerInterface
{
    
    /**
     * Serializes data in the appropriate format.
     *
     * @param mixed      $data    Any data
     * @param string     $format  Format name
     * @param array|null $context Options normalizers/encoders have access to
     *
     * @return string
     */
    public function serialize($data, string $format, ?array $context = null) : string;
}
