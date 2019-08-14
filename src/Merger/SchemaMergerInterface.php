<?php

declare(strict_types=1);

namespace NickZh\PhpAvroSchemaGenerator\Merger;

use NickZh\PhpAvroSchemaGenerator\Registry\SchemaRegistryInterface;
use NickZh\PhpAvroSchemaGenerator\Schema\SchemaTemplateInterface;

interface SchemaMergerInterface
{

    /**
     * @return SchemaMergerInterface
     */
    public static function create(): SchemaMergerInterface;

    /**
     * @param  SchemaRegistryInterface $schemaRegistry
     * @return SchemaMergerInterface
     */
    public function setSchemaRegistry(SchemaRegistryInterface $schemaRegistry): SchemaMergerInterface;


    /**
     * @param string $outputDirectory
     * @return SchemaMergerInterface
     */
    public function setOutputDirectory(string $outputDirectory): SchemaMergerInterface;

    /**
     * @return SchemaRegistryInterface|null
     */
    public function getSchemaRegistry(): ?SchemaRegistryInterface;

    /**
     * @param  SchemaTemplateInterface $schemaTemplate
     * @return SchemaTemplateInterface
     */
    public function resolveSchemaTemplate(SchemaTemplateInterface $schemaTemplate): SchemaTemplateInterface;

    /**
     * @return int
     */
    public function merge(): int;

    /**
     * @param  SchemaTemplateInterface $schemaTemplate
     * @return void
     */
    public function exportSchema(SchemaTemplateInterface $schemaTemplate): void;

    /**
     * @param  array $schemaDefinition
     * @return array
     */
    public function transformExportSchemaDefinition(array $schemaDefinition): array;
}
