<?php

declare(strict_types=1);

namespace NickZh\PhpAvroSchemaGenerator\Registry;

use NickZh\PhpAvroSchemaGenerator\Schema\SchemaTemplateInterface;

interface SchemaRegistryLoaderInterface
{

    /**
     * @param string $inputDirectory
     * @return SchemaRegistryLoaderInterface
     */
    public function addSchemaDirectory(string $inputDirectory): SchemaRegistryLoaderInterface;

    /**
     * @return array
     */
    public function getRootSchema(): array;

    /**
     * @return array
     */
    public function getSchemaDirectories(): array;

    /**
     * @return void
     */
    public function load(): SchemaRegistryLoaderInterface;

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
