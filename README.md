# Avro schema generator for PHP
Since avro does not support subschemas, this is just a small
helper unify your split schema.

```
$generator = SchemaGenerator::create()
    ->setOutputDirectory('./foo')
    ->addInputDirectory(('./schema'))
    ->addSchemaFile('./schema/Library.avsc');

$generator->generateSchema();
```

- Input directories: directories containing avsc files
- Schema files: Schema files that should be unified
- Output directory: output directory for the unified schema files
