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

$service = new \SmartWeb\Nats\Service($client);

$service->run();

//$client->publish('foo', 'Marty McFly');
//$client->publish('foo', 'Marty McFly');
//$client->publish('foo', 'Marty McFly');

$client->close();
