<?php

declare(strict_types=1);

require_once '../vendor/autoload.php';

$generator = \NickZh\PhpAvroSchemaGenerator\Generator\SchemaGenerator::create()
    ->setOutputDirectory('./foo')
    ->addInputDirectory(('./schema'))
    ->addSchemaFile('./schema/Library.avsc');

$generator->generateSchema();
