<?php

declare(strict_types=1);

require_once '../vendor/autoload.php';

use NickZh\PhpAvroSchemaGenerator\Registry\SchemaRegistry;
use NickZh\PhpAvroSchemaGenerator\Merger\SchemaMerger;

$registry = (new SchemaRegistry())
    ->addSchemaTemplateDirectory('./schemaTemplates')
    ->load();

$merger = SchemaMerger::create()
    ->setSchemaRegistry($registry)
    ->setOutputDirectory('./schema');

$merger->merge();
