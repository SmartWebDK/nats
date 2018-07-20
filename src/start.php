<?php
declare(strict_types = 1);

require __DIR__.'/../vendor/autoload.php';

$service = new \SmartWeb\NATS\Service();

$service->boot();
$service->run();
