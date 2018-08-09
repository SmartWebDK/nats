<?php
declare(strict_types = 1);

$sourceDir = __DIR__ . '/../../src';

$options = [
    'title'     => 'SmartWeb NATS streaming with CloudEvents',
    'build_dir' => __DIR__ . '/../sami/build',
    'cache_dir' => __DIR__ . '/../sami/cache',
];

return new \Sami\Sami($sourceDir, $options);
