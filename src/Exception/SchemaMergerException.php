<?php

namespace NickZh\PhpAvroSchemaGenerator\Exception;

class SchemaMergerException extends \Exception
{
    public const NO_SCHEMA_REGISTRY_SET_EXCEPTION_MESSAGE = 'No schema registry set.';
    public const UNKNOWN_SCHEMA_TYPE_EXCEPTION_MESSAGE = 'Unknown schema type: %s';
}
