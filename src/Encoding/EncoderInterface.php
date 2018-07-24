<?php
declare(strict_types = 1);


namespace SmartWeb\Nats\Encoding;

use SmartWeb\Nats\PayloadInterface;

/**
 * Interface EncoderInterface
 *
 * @api
 */
interface EncoderInterface
{
    
    /**
     * @param PayloadInterface $payload
     *
     * @return string
     */
    public function encode(PayloadInterface $payload) : string;
}
