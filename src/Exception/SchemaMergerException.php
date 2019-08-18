<?php

namespace NickZh\PhpAvroSchemaGenerator\Exception;

use \Exception;

class SchemaMergerException extends Exception
{
    const NO_SCHEMA_REGISTRY_SET_EXCEPTION_MESSAGE = 'No schema registry set.';
    const UNKNOWN_SCHEMA_TYPE_EXCEPTION_MESSAGE = 'Unknown schema type: %s';
}
