<?php
declare(strict_types = 1);

require __DIR__ . '/../vendor/autoload.php';


$options = new \Nats\ConnectionOptions(
    [
        'port' => 4222,
    ]
);
$client = new \Nats\Connection();

$client->connect();

$connection = new \SmartWeb\Nats\Connection\ConnectionAdapter($options);
$encoder = new \Symfony\Component\Serializer\Encoder\JsonEncode();

$publisher = new \SmartWeb\Nats\Publisher\Publisher($connection, $encoder);

$subject = 'foo';
$messageContent = [
    'someField' => 'someVal'
];

$publishable = new \SmartWeb\Nats\Publisher\PublishableMessage($subject, $messageContent);


$connection->connect();

$publisher->publish($publishable);
$publisher->publish($publishable);
$publisher->publish($publishable);

$connection->close();

//$client->publish('foo', 'Marty McFly');
//$client->publish('foo', 'Marty McFly');
//$client->publish('foo', 'Marty McFly');

$client->close();
