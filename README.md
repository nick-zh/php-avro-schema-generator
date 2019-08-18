# Avro schema generator for PHP
[![CircleCI](https://circleci.com/gh/nick-zh/php-avro-schema-generator.svg?style=svg)](https://circleci.com/gh/nick-zh/php-avro-schema-generator)
[![Maintainability](https://api.codeclimate.com/v1/badges/937e14c63beb08885c70/maintainability)](https://codeclimate.com/github/nick-zh/php-avro-schema-generator/maintainability)
[![Test Coverage](https://api.codeclimate.com/v1/badges/937e14c63beb08885c70/test_coverage)](https://codeclimate.com/github/nick-zh/php-avro-schema-generator/test_coverage)
[![Latest Stable Version](https://poser.pugx.org/nick-zh/php-avro-schema-generator/v/stable)](https://packagist.org/packages/nick-zh/php-avro-schema-generator)
[![Latest Unstable Version](https://poser.pugx.org/nick-zh/php-avro-schema-generator/v/unstable)](https://packagist.org/packages/nick-zh/php-avro-schema-generator)

## Installation
```
composer require nick-zh/php-avro-schema-generator "^0.1.0"
```

## Description
Since avro does not support external subschemas, this is just a small
helper to unify your schemas and to create basic schemas from php classes (experimental!).

### Merging subschemas / schemas
Schema template directories: directories containing avsc template files (with subschema)
Output directory: output directory for the unified schema files

#### Merge subschemas (code)
```php
<?php

use NickZh\PhpAvroSchemaGenerator\Registry\SchemaRegistry;
use NickZh\PhpAvroSchemaGenerator\Merger\SchemaMerger;

$registry = (new SchemaRegistry())
    ->addSchemaTemplateDirectory('./schemaTemplates')
    ->load();

$merger = new SchemaMerger($registry, './schema');

$merger->merge();

```

#### Merge subschemas (command)
```bash
./bin/console avro:subschema:merge ./example/schemaTemplates ./example/schema
```

### Generating schemas from classes
Please note, that this feature is highly experimental.  
You probably still need to adjust the generated templates, but it gives you a basic tempalte to work with.  
Class direcotries: Directories containing the classes you want to generate schemas from
Output directory: output directory for your generated schema templates

#### Generate schemas (code)
```php
<?php

use NickZh\PhpAvroSchemaGenerator\Registry\ClassRegistry;
use NickZh\PhpAvroSchemaGenerator\Generator\SchemaGenerator;

$registry = (new ClassRegistry())
    ->addClassDirectory('./example/classes')
    ->load();

$generator = new SchemaGenerator($registry, './example/schemaTemplates');

$schemas = $generator->generate();

$generator->exportSchemas($schemas);

```

#### Merge subschemas (command)
```bash
./bin/console avro:schema:generate ./example/classes ./example/schemaTemplates
```
