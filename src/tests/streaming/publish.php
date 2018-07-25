<?php
declare(strict_types = 1);

require __DIR__ . '/../../../vendor/autoload.php';

$port = 4223;
$host = 'nats-streaming';
$natsOptions = new \Nats\ConnectionOptions(
    [
        'port' => $port,
        'host' => $host,
    ]
);

$serviceName = 'fancy-publish-service';
$service = new \SmartWeb\Nats\Service($serviceName, $natsOptions);

$service->runPublishTest();
//$service->runSimplePublishTest();
