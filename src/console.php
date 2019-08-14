#!/usr/bin/env php
<?php

declare(strict_types=1);

require __DIR__ . '/../vendor/autoload.php';

use Symfony\Component\Console\Application;
use NickZh\PhpAvroSchemaGenerator\Command\SubSchemaMergeCommand;

$application = new Application();

$application->add(new SubSchemaMergeCommand());

$application->run();
