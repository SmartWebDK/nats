<?php
declare(strict_types = 1);

require __DIR__ . '/../../../vendor/autoload.php';

$options = new \NatsStreaming\ConnectionOptions();

$clientID = mt_rand();
$options->setClientID($clientID);
$options->setClusterID('test-cluster');

$connection = new \NatsStreaming\Connection($options);

$connection->connect();

$subOptions = new \NatsStreaming\SubscriptionOptions();

$subjects = 'some.subject';
$queue = 'some.queue';
$callback = function ($message) {
    \printf($message);
};

$sub = $connection->queueSubscribe($subjects, $queue, $callback, $subOptions);


$sub->wait(1);

// not explicitly needed
$sub->close(); // or $sub->unsubscribe();

$connection->close();
