<?php

declare(strict_types=1);

namespace NickZh\PhpAvroSchemaGenerator\Registry;

use NickZh\PhpAvroSchemaGenerator\Schema\SchemaTemplateInterface;

interface SchemaRegistryInterface
{

    /**
     * @param string $schemaTemplateDirectory
     * @return SchemaRegistryInterface
     */
    public function addSchemaTemplateDirectory(string $schemaTemplateDirectory): SchemaRegistryInterface;

    /**
     * @return array
     */
    public function getRootSchemas(): array;

    /**
     * @return array
     */
    public function getSchemaDirectories(): array;

    /**
     * @return void
     */
    public function load(): SchemaRegistryInterface;

    /**
     * @return array
     */
    public function getSchemas(): array;

    /**
     * @param string $schemaId
     * @return SchemaTemplateInterface|null
     */
    public function getSchemaById(string $schemaId): ?SchemaTemplateInterface;


    /**
     * @param array $schemaData
     * @return string
     */
    public function getSchemaId(array $schemaData): string;
}
