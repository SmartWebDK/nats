<?php
declare(strict_types = 1);


namespace SmartWeb\Nats\Message;

use Google\Protobuf\Internal\Message;
use SmartWeb\Nats\Error\InvalidTypeException;

/**
 * De-serializes raw strings into Protobuf-compatible messages.
 *
 * @author Nicolai Agersbæk <na@zitcom.dk>
 *
 * @api
 */
interface DeserializerInterface
{
    
    /**
     * Deserialize the provided `$messageData` string into a concrete Message
     * instance of the given `$messageType`.
     *
     * @param string $messageData
     * @param string $messageType
     *
     * @return Message
     *
     * @throws InvalidTypeException Occurs if the given type is not Protobuf-compatible.
     * @throws \Exception
     */
    public function deserialize(string $messageData, string $messageType) : Message;
}
