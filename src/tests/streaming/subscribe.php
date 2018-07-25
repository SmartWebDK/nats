<?php
declare(strict_types = 1);

require __DIR__ . '/../../../vendor/autoload.php';

$port = 4223;
$natsOptions = new \Nats\ConnectionOptions(\compact('port'));

$service = new \SmartWeb\Nats\Service($natsOptions);

$service->runSimpleSubscribeTest('test-cluster');
