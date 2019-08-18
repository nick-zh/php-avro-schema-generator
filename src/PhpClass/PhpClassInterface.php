<?php

declare(strict_types=1);

namespace NickZh\PhpAvroSchemaGenerator\PhpClass;

interface PhpClassInterface
{
    public function getClassNamespace(): string;

    public function getClassName(): string;

    public function getClassBody(): string;

    public function getClassProperties(): array;
}
