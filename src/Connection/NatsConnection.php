<?php
/** @noinspection EfferentObjectCouplingInspection */
declare(strict_types = 1);


namespace SmartWeb\Nats\Connection;

use Google\Protobuf\Internal\Message as ProtobufMessage;
use Nats\Connection;
use Nats\Exception;
use Nats\Message;
use SmartWeb\Events\EventInterface;
use SmartWeb\Nats\Error\Handler\ExceptionHandlerInterface;
use SmartWeb\Nats\Error\Handler\RethrowingHandler;
use SmartWeb\Nats\Error\InvalidEventException;
use SmartWeb\Nats\Error\InvalidTypeException;
use SmartWeb\Nats\Error\RequestFailedException;
use SmartWeb\Nats\Message\DeserializerInterface;
use SmartWeb\Nats\Subscriber\MessageInitializerInterface;
use SmartWeb\Nats\Subscriber\NatsSubscriberInterface as Subscriber;
use SmartWeb\Nats\Subscriber\UsesProtobufAnyInterface;

/**
 * Adapter for {@link Nats\Connection}, which makes interaction with NATS using
 * Protobuf-compatible events easier.
 *
 * @author Nicolai Agersbæk <na@smartweb.dk>
 *
 * @api
 */
class NatsConnection implements NatsConnectionInterface
{
    
    /**
     * Format used to create error message when receiving invalid event class.
     */
    private const INVALID_EVENT_CLS_MSG = 'Expected instance of %s; was %s';
    
    /**
     * @var ExceptionHandlerInterface
     */
    private static $defaultErrorHandler;
    
    /**
     * @var Connection
     */
    private $connection;
    
    /**
     * @var DeserializerInterface
     */
    private $deserializer;
    
    /**
     * @var MessageInitializerInterface
     */
    private $initializer;
    
    /**
     * @param Connection                  $connection
     * @param DeserializerInterface       $deserializer
     * @param MessageInitializerInterface $initializer
     */
    public function __construct(
        Connection $connection,
        DeserializerInterface $deserializer,
        MessageInitializerInterface $initializer
    ) {
        $this->connection = $connection;
        $this->deserializer = $deserializer;
        $this->initializer = $initializer;
    }
    
    /**
     * Publish an event to NATS.
     * The channel on which to publish the payload is inferred by the type of
     * the given event.
     *
     * @param EventInterface $event   Concrete event to publish.
     * @param string|null    $channel [Optional] channel to publish on. Default
     *                                will be determined from the type of the given event.
     *
     * @throws InvalidEventException Occurs when the given event is not a valid Protobuf message.
     * @throws Exception Occurs if subscription not found.
     */
    public function publish(EventInterface $event, ?string $channel = null) : void
    {
        $this->connection->publish(
            $channel ?? $event->getEventType(),
            $this->serializeEvent($event)
        );
    }
    
    /**
     * @param EventInterface $event
     *
     * @return string
     *
     * @throws InvalidEventException
     */
    public function serializeEvent(EventInterface $event) : string
    {
        if ($event instanceof ProtobufMessage) {
            return $event->serializeToString();
        }
        
        throw $this->invalidEventException($event);
    }
    
    /**
     * @param EventInterface $event
     *
     * @return InvalidEventException
     */
    private function invalidEventException(EventInterface $event) : InvalidEventException
    {
        $msg = \sprintf(
            self::INVALID_EVENT_CLS_MSG,
            ProtobufMessage::class,
            \get_class($event)
        );
        
        return new InvalidEventException($event, $msg);
    }
    
    /**
     * Register an event subscriber on the given channel.
     *
     * @param string     $channel
     * @param Subscriber $subscriber
     *
     * @return string The SID of the subscription.
     *
     * @throws InvalidTypeException Occurs if the expected type of the given
     *                              subscriber is not Protobuf-compatible.
     */
    public function subscribe(string $channel, Subscriber $subscriber) : string
    {
        return $this->connection->subscribe($channel, $this->createSubscriberCallback($subscriber));
    }
    
    /**
     * Register an event subscriber on the given channel in the given queue group.
     *
     * @param string     $channel
     * @param string     $group
     * @param Subscriber $subscriber
     *
     * @return string The SID of the subscription.
     *
     * @throws InvalidTypeException Occurs if the expected type of the given
     *                              subscriber is not Protobuf-compatible.
     */
    public function groupSubscribe(string $channel, string $group, Subscriber $subscriber) : string
    {
        return $this->connection->queueSubscribe(
            $channel,
            $group,
            $this->createSubscriberCallback($subscriber)
        );
    }
    
    /**
     * Performs a synchronous request, which expects a reply.
     *
     * @param EventInterface $event
     * @param Subscriber     $responseHandler
     *
     * @throws RequestFailedException Occurs if the request could not be published to NATS.
     */
    public function request(EventInterface $event, Subscriber $responseHandler) : void
    {
        $this->connection->request(
            $event->getEventType(),
            $this->serializeEvent($event),
            $this->createSubscriberCallback($responseHandler)
        );
    }
    
    /**
     * @param Subscriber $subscriber
     *
     * @return \Closure
     *
     * @throws InvalidTypeException Occurs if the given type is not Protobuf-compatible.
     */
    public function createSubscriberCallback(Subscriber $subscriber) : \Closure
    {
        return function (Message $msg) use ($subscriber): void {
            try {
                $event = $this->deserializeMessage($msg, $subscriber);
            } catch (\Throwable $exception) {
                // FIXME: Missing tests!
                // If an exception occurs, delegate handling to the subscriber.
                $this->getSubscriberErrorHandler($subscriber)->handleException($exception);
                
                return;
            }
            
            $subscriber->handle($event);
        };
    }
    
    /**
     * @param Subscriber $subscriber
     *
     * @return ExceptionHandlerInterface
     */
    private function getSubscriberErrorHandler(Subscriber $subscriber) : ExceptionHandlerInterface
    {
        return $subscriber instanceof ExceptionHandlerInterface
            ? $subscriber
            : $this->getDefaultErrorHandler();
    }
    
    /**
     * @return ExceptionHandlerInterface
     */
    private function getDefaultErrorHandler() : ExceptionHandlerInterface
    {
        return self::$defaultErrorHandler ?? self::$defaultErrorHandler = new RethrowingHandler();
    }
    
    /**
     * @param Message    $message
     * @param Subscriber $subscriber
     *
     * @return ProtobufMessage
     *
     * @throws InvalidTypeException Occurs if the given type is not Protobuf-compatible.
     * @throws \Exception Occurs if the data of the given `$message` is not valid for the expected type.
     */
    public function deserializeMessage(Message $message, Subscriber $subscriber) : ProtobufMessage
    {
        $this->initializeUses($subscriber);
        
        return $this->deserializer->deserialize($message->getBody(), $subscriber->expects());
    }
    
    /**
     * @param Subscriber $subscriber
     */
    public function initializeUses(Subscriber $subscriber) : void
    {
        $uses = $subscriber instanceof UsesProtobufAnyInterface
            ? $subscriber->uses()
            : [];
        
        $this->initializer->initialize(...$uses);
    }
}
