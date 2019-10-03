<?php

declare(strict_types=1);

namespace NickZh\PhpAvroSchemaGenerator\Exception;

class ClassRegistryException extends \Exception
{
    public const FILE_PATH_EXCEPTION_MESSAGE = 'Unable to get file path';
    public const FILE_NOT_READABLE_EXCEPTION_MESSAGE = 'Unable to read file: %s';
}
