<?php

declare(strict_types=1);

require_once '../vendor/autoload.php';
include 'classes/SomeTestClass.php';
include 'classes/SomeOtherTestClass.php';


use NickZh\PhpAvroSchemaGenerator\Registry\ClassRegistry;
use NickZh\PhpAvroSchemaGenerator\Generator\SchemaGenerator;

$registry = (new ClassRegistry())->addClassDirectory('./classes')->load();
