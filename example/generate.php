<?php

declare(strict_types=1);

require_once '../vendor/autoload.php';

use NickZh\PhpAvroSchemaGenerator\Registry\SchemaRegistryLoader;
use NickZh\PhpAvroSchemaGenerator\Generator\SchemaGenerator;

$registry = (new SchemaRegistryLoader())
    ->addSchemaDirectory('./schemaTemplates')
    ->load();

$generator = SchemaGenerator::create()
    ->setSchemaRegistry($registry)
    ->setOutputDirectory('./schema');

$generator->generateSchema();
