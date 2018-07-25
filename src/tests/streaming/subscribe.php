<?php
declare(strict_types = 1);

require __DIR__ . '/../../../vendor/autoload.php';

$port = 4223;
$host = 'nats-streaming';
$natsOptions = new \Nats\ConnectionOptions(\compact('port', 'host'));

$serviceName = 'fancy-subscribe-service';
$service = new \SmartWeb\Nats\Service($serviceName, $natsOptions);

$service->runSimpleSubscribeTest();
