<?php

$loader = require __DIR__ . '/../vendor/autoload.php';

$loader->setPsr4('NickZh\\PhpAvroSchemaGenerator\\Tests\\', __DIR__);

echo sprintf('php version: %s', phpversion()) . PHP_EOL;
