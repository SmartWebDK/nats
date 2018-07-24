<?php
declare(strict_types = 1);


namespace SmartWeb\Nats\Encoding;

use SmartWeb\Nats\Payload\PayloadInterface;

/**
 * Interface DecoderInterface
 *
 * @api
 */
interface DecoderInterface
{
    
    /**
     * @param string $payload
     *
     * @return PayloadInterface
     */
    public function decode(string $payload) : PayloadInterface;
}
