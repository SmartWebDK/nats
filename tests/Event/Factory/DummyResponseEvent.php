<?php
declare(strict_types = 1);


namespace SmartWeb\Nats\Tests\Event\Factory;

use Google\Protobuf\Timestamp;
use SmartWeb\Events\EventInterface;

/**
 * Dummy response event used for testing.
 *
 * @author Nicolai Agersbæk <na@zitcom.dk>
 */
class DummyResponseEvent implements EventInterface
{
    
    /**
     * @var array
     */
    private $data;
    
    /**
     * @param array $data
     */
    public function __construct(array $data)
    {
        $this->data = $data;
    }
    
    /**
     * @return string
     */
    public function getEventType() : string
    {
        /** @noinspection PhpIncompatibleReturnTypeInspection */
        return $this->data['eventType'];
    }
    
    /**
     * @return string
     */
    public function getSource() : string
    {
        /** @noinspection PhpIncompatibleReturnTypeInspection */
        return $this->data['source'];
    }
    
    /**
     * @return string
     */
    public function getEventId() : string
    {
        /** @noinspection PhpIncompatibleReturnTypeInspection */
        return $this->data['eventId'];
    }
    
    /**
     * @return Timestamp
     */
    public function getEventTime() : Timestamp
    {
        return $this->data['eventTime'];
    }
    
    /**
     * @return object
     */
    public function getData()
    {
        return $this->data['data'];
    }
}
