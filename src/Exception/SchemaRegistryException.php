<?php

declare(strict_types=1);

namespace NickZh\PhpAvroSchemaGenerator\Exception;

use \Exception;

class SchemaRegistryException extends Exception
{
    const FILE_PATH_EXCEPTION_MESSAGE = 'Unable to get file path';
    const FILE_NOT_READABLE_EXCEPTION_MESSAGE = 'Unable to read file: %s';
}
