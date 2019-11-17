<?php

declare(strict_types=1);

namespace NickZh\PhpAvroSchemaGenerator\Merger;

use NickZh\PhpAvroSchemaGenerator\Registry\SchemaRegistryInterface;
use NickZh\PhpAvroSchemaGenerator\Schema\SchemaTemplateInterface;

interface SchemaMergerInterface
{

    /**
     * @return SchemaRegistryInterface
     */
    public function getSchemaRegistry(): SchemaRegistryInterface;

    /**
     * @return string
     */
    public function getOutputDirectory(): string;

    /**
     * @param  SchemaTemplateInterface $schemaTemplate
     * @return SchemaTemplateInterface
     */
    public function getResolvedSchemaTemplate(SchemaTemplateInterface $schemaTemplate): SchemaTemplateInterface;

    /**
     * @return int
     */
    public function merge(): int;

    /**
     * @param SchemaTemplateInterface $rootRootSchemaTemplate
     * @return void
     */
    public function exportSchema(SchemaTemplateInterface $rootRootSchemaTemplate): void;

    /**
     * @param  array $schemaDefinition
     * @return array
     */
    public function transformExportSchemaDefinition(array $schemaDefinition): array;
}
