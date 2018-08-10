<?php
declare(strict_types = 1);


namespace SmartWeb\Nats\Tests\Payload\Serialization;

use PHPUnit\Framework\TestCase;
use SmartWeb\CloudEvents\Nats\Event\EventFields;

/**
 * @author Nicolai Agersbæk <na@smartweb.dk>
 */
class EventProviderTest extends TestCase
{
    
    /**
     * @var EventProviderFactory
     */
    private static $factory;
    
    /**
     * @test
     *
     * @param EventProviderInterface $provider
     * @param bool                   $includeNullEntries
     * @param string                 $expected
     *
     * @dataProvider eventStringDataProvider
     */
    public function checkEventString(
        EventProviderInterface $provider,
        bool $includeNullEntries,
        string $expected
    ) : void {
        $actual = $provider->eventString($includeNullEntries);
        
        $this->assertSame($expected, $actual);
    }
    
    /**
     * @skip
     * @return array
     */
    public function eventStringDataProvider() : array
    {
        return [
            'minimal, with null entries'     => [
                'provider'           => self::factory()->minimal(),
                'includeNullEntries' => true,
                'expected'           => '{"eventType":"some.event","eventTypeVersion":null,"cloudEventsVersion":"0.1.0","source":"some.source","eventId":"some.event.id","eventTime":null,"schemaURL":null,"contentType":null,"extensions":null,"data":null}',
            ],
            'minimal, without null entries'  => [
                'provider'           => self::factory()->minimal(),
                'includeNullEntries' => false,
                'expected'           => '{"eventType":"some.event","cloudEventsVersion":"0.1.0","source":"some.source","eventId":"some.event.id"}',
            ],
            'complete, with null entries'    => [
                'provider'           => self::factory()->complete(),
                'includeNullEntries' => true,
                'expected'           => '{"eventType":"some.event","eventTypeVersion":"1.0.0","cloudEventsVersion":"0.1.0","source":"some.source","eventId":"some.event.id","eventTime":"2000-01-02T13:34:56+01:00","schemaURL":"https:\\/\\/www.some-schema.org\\/cloud-events\\/test.schema?version=2.3.4","contentType":"application\\/json","extensions":{"com.foo.extension":"barExtension"},"data":{"foo":"bar"}}',
            ],
            'complete, without null entries' => [
                'provider'           => self::factory()->complete(),
                'includeNullEntries' => false,
                'expected'           => '{"eventType":"some.event","eventTypeVersion":"1.0.0","cloudEventsVersion":"0.1.0","source":"some.source","eventId":"some.event.id","eventTime":"2000-01-02T13:34:56+01:00","schemaURL":"https:\\/\\/www.some-schema.org\\/cloud-events\\/test.schema?version=2.3.4","contentType":"application\\/json","extensions":{"com.foo.extension":"barExtension"},"data":{"foo":"bar"}}',
            ],
        ];
    }
    
    /**
     * @test
     *
     * @param EventProviderInterface $provider
     * @param bool                   $includeNullEntries
     * @param array                  $expected
     *
     * @dataProvider eventContentsDataProvider
     */
    public function checkEventContents(
        EventProviderInterface $provider,
        bool $includeNullEntries,
        array $expected
    ) : void {
        $actual = $provider->eventContents($includeNullEntries);
        
        $this->assertEquals($expected, $actual);
    }
    
    /**
     * @skip
     * @return array
     */
    public function eventContentsDataProvider() : array
    {
        return [
            'minimal, with null entries'     => [
                'provider'           => self::factory()->minimal(),
                'includeNullEntries' => true,
                'expected'           => [
                    EventFields::EVENT_TYPE           => 'some.event',
                    EventFields::EVENT_TYPE_VERSION   => null,
                    EventFields::CLOUD_EVENTS_VERSION => '0.1.0',
                    EventFields::SOURCE               => 'some.source',
                    EventFields::EVENT_ID             => 'some.event.id',
                    EventFields::EVENT_TIME           => null,
                    EventFields::SCHEMA_URL           => null,
                    EventFields::CONTENT_TYPE         => null,
                    EventFields::EXTENSIONS           => null,
                    EventFields::DATA                 => null,
                ],
            ],
            'minimal, without null entries'  => [
                'provider'           => self::factory()->minimal(),
                'includeNullEntries' => false,
                'expected'           => [
                    EventFields::EVENT_TYPE           => 'some.event',
                    EventFields::CLOUD_EVENTS_VERSION => '0.1.0',
                    EventFields::SOURCE               => 'some.source',
                    EventFields::EVENT_ID             => 'some.event.id',
                ],
            ],
            'complete, with null entries'    => [
                'provider'           => self::factory()->complete(),
                'includeNullEntries' => true,
                'expected'           => [
                    EventFields::EVENT_TYPE           => 'some.event',
                    EventFields::EVENT_TYPE_VERSION   => '1.0.0',
                    EventFields::CLOUD_EVENTS_VERSION => '0.1.0',
                    EventFields::SOURCE               => 'some.source',
                    EventFields::EVENT_ID             => 'some.event.id',
                    EventFields::EVENT_TIME           => '2000-01-02T13:34:56+01:00',
                    EventFields::SCHEMA_URL           => 'https://www.some-schema.org/cloud-events/test.schema?version=2.3.4',
                    EventFields::CONTENT_TYPE         => 'application/json',
                    EventFields::EXTENSIONS           => [
                        'com.foo.extension' => 'barExtension',
                    ],
                    EventFields::DATA                 => [
                        'foo' => 'bar',
                    ],
                ],
            ],
            'complete, without null entries' => [
                'provider'           => self::factory()->complete(),
                'includeNullEntries' => false,
                'expected'           => [
                    EventFields::EVENT_TYPE           => 'some.event',
                    EventFields::EVENT_TYPE_VERSION   => '1.0.0',
                    EventFields::CLOUD_EVENTS_VERSION => '0.1.0',
                    EventFields::SOURCE               => 'some.source',
                    EventFields::EVENT_ID             => 'some.event.id',
                    EventFields::EVENT_TIME           => '2000-01-02T13:34:56+01:00',
                    EventFields::SCHEMA_URL           => 'https://www.some-schema.org/cloud-events/test.schema?version=2.3.4',
                    EventFields::CONTENT_TYPE         => 'application/json',
                    EventFields::EXTENSIONS           => [
                        'com.foo.extension' => 'barExtension',
                    ],
                    EventFields::DATA                 => [
                        'foo' => 'bar',
                    ],
                ],
            ],
        ];
    }
    
    /**
     * @return EventProviderFactory
     */
    private static function factory() : EventProviderFactory
    {
        return self::$factory ?? self::$factory = new EventProviderFactory();
    }
}
