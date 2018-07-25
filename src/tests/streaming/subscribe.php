<?php
declare(strict_types = 1);

require __DIR__ . '/../../../vendor/autoload.php';

$port = 4223;
$natsOptions = new \Nats\ConnectionOptions(\compact('port'));
$options = new \NatsStreaming\ConnectionOptions(\compact('natsOptions'));

$clientID = mt_rand();
$options->setClientID($clientID);
$options->setClusterID('test-cluster');

$connection = new \NatsStreaming\Connection($options);

$connection->connect();

$subOptions = new \NatsStreaming\SubscriptionOptions();
$subOptions->setStartAt(\NatsStreamingProtos\StartPosition::NewOnly());

$subjects = 'some.channel';
$callback = function ($message) {
    \printf($message);
};

$sub = $connection->subscribe($subjects, $callback, $subOptions);

$sub->wait(3);

// not explicitly needed
$sub->unsubscribe(); // or $sub->close();

$connection->close();
