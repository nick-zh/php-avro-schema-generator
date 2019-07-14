<?php

declare(strict_types=1);

require_once '../vendor/autoload.php';

use NickZh\PhpAvroSchemaGenerator\Registry\SchemaRegistry;
use NickZh\PhpAvroSchemaGenerator\Generator\SchemaGenerator;

$registry = (new SchemaRegistry())
    ->addSchemaTemplateDirectory('./schemaTemplates')
    ->load();

$generator = SchemaGenerator::create()
    ->setSchemaRegistry($registry)
    ->setOutputDirectory('./schema');

$generator->generateSchemas();
