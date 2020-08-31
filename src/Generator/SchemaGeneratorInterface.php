<?php

declare(strict_types=1);

namespace NickZh\PhpAvroSchemaGenerator\Generator;

use NickZh\PhpAvroSchemaGenerator\Registry\ClassRegistryInterface;

interface SchemaGeneratorInterface
{
    /**
     * @return ClassRegistryInterface
     */
    public function getClassRegistry(): ClassRegistryInterface;

    /**
     * @return string
     */
    public function getOutputDirectory(): string;

    /**
     * @return array<string,string|false>
     */
    public function generate(): array;

    /**
     * @param array<string,string|false> $schemas
     * @return int
     */
    public function exportSchemas(array $schemas): int;
}
