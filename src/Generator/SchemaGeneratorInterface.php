<?php

declare(strict_types=1);

namespace NickZh\PhpAvroSchemaGenerator\Generator;

use NickZh\PhpAvroSchemaGenerator\Registry\SchemaRegistryInterface;
use NickZh\PhpAvroSchemaGenerator\Schema\SchemaTemplateInterface;

interface SchemaGeneratorInterface
{

    /**
     * @return SchemaGeneratorInterface
     */
    public static function create(): SchemaGeneratorInterface;

    /**
     * @param SchemaRegistryInterface $schemaRegistry
     * @return SchemaGeneratorInterface
     */
    public function setSchemaRegistry(SchemaRegistryInterface $schemaRegistry): SchemaGeneratorInterface;

    /**
     * @return SchemaRegistryInterface|null
     */
    public function getSchemaRegistry(): ?SchemaRegistryInterface;

    /**
     * @param SchemaTemplateInterface $schemaTemplate
     * @return SchemaTemplateInterface
     */
    public function resolveSchemaTemplate(SchemaTemplateInterface $schemaTemplate): SchemaTemplateInterface;

    /**
     * @return void
     */
    public function generateSchemas(): void;

    /**
     * @param SchemaTemplateInterface $schemaTemplate
     * @return void
     */
    public function exportSchema(SchemaTemplateInterface $schemaTemplate): void;

    /**
     * @param array $schemaDefinition
     * @return array
     */
    public function transformExportSchemaDefinition(array $schemaDefinition): array;

}
