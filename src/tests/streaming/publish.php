<?php
declare(strict_types = 1);

require __DIR__ . '/../../../vendor/autoload.php';

$options = new \NatsStreaming\ConnectionOptions();

$clientID = mt_rand();
$options->setClientID($clientID);
$options->setClusterID('test-cluster');

$connection = new \NatsStreaming\Connection($options);

$connection->connect();

$subject = 'some.subject';
$data = 'Foo!';

$r = $connection->publish($subject, $data);

$gotAck = $r->wait();

$statusResponse = $gotAck
    ? 'Acknowledged'
    : 'Not acknowledged';

\printf("$statusResponse\r\n");

$connection->close();
