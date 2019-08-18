<?php

declare(strict_types=1);

require_once '../vendor/autoload.php';

use NickZh\PhpAvroSchemaGenerator\Registry\ClassRegistry;
use NickZh\PhpAvroSchemaGenerator\Generator\SchemaGenerator;

$registry = (new ClassRegistry())->addClassDirectory('./classes')->load();
$generator = new SchemaGenerator($registry);
$schemas = $generator->generate();
$generator->exportSchemas($schemas);
