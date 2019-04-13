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
class Deserializer implements DeserializerInterface
{
    
    /**
     * Error message used when receiving an event not compatible with Protobuf.
     */
    private const INVALID_MSG_TYPE_MSG = "'Invalid message type '%s'; expected instance of '" . Message::class;
    
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
    public function deserialize(string $messageData, string $messageType) : Message
    {
        // TODO: Missing tests!
        $this->validateMessageType($messageType);
        
        /** @var Message $msg */
        $msg = new $messageType();
        
        $msg->mergeFromString($messageData);
        
        return $msg;
    }
    
    /**
     * @param string $type
     *
     * @throws InvalidTypeException Occurs if the given type is not Protobuf-compatible.
     */
    public function validateMessageType(string $type) : void
    {
        if (!$this->typeIsProtobufCompatible($type)) {
            $msg = \sprintf(self::INVALID_MSG_TYPE_MSG, $type);
            
            throw new InvalidTypeException($type, $msg);
        }
    }
    
    /**
     * @param string $type
     *
     * @return bool
     */
    public function typeIsProtobufCompatible(string $type) : bool
    {
        return \is_a($type, Message::class, true);
    }
}
