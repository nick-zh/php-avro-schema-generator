# Avro schema generator for PHP

## Installation

```
composer require nick-zh/php-avro-schema-generator "^0.1.0"
```

## Description
Since avro does not support subschemas, this is just a small
helper to unify your subschema schema.

```
$registry = (new SchemaRegistr())
    ->addSchemaTemplateDirectory('./schemaTemplates')
    ->load();

$merger = SchemaMerger::create()
    ->setSchemaRegistry($registry)
    ->setOutputDirectory('./schema');

$merger->merge();

```

- Schema template directories: directories containing avsc template files (with subschema)
- Output directory: output directory for the unified schema files
