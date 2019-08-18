#!/usr/bin/env php
<?php

declare(strict_types=1);

require __DIR__ . '/../vendor/autoload.php';

use Symfony\Component\Console\Application;
use NickZh\PhpAvroSchemaGenerator\Command\SubSchemaMergeCommand;
use NickZh\PhpAvroSchemaGenerator\Command\SchemaGenerateCommand;

$application = new Application();

$application->add(new SchemaGenerateCommand());
$application->add(new SubSchemaMergeCommand());

$application->run();
