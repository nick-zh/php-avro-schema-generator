<?php

declare(strict_types=1);

namespace NickZh\PhpAvroSchemaGenerator\Generator;

use NickZh\PhpAvroSchemaGenerator\Registry\SchemaRegistry;
use NickZh\PhpAvroSchemaGenerator\Registry\SchemaRegistryLoaderInterface;
use NickZh\PhpAvroSchemaGenerator\Schema\SchemaTemplateInterface;

interface SchemaGeneratorInterface
{

    /**
     * @return SchemaGeneratorInterface
     */
    public static function create(): SchemaGeneratorInterface;

    /**
     * @param SchemaRegistryLoaderInterface $schemaRegistry
     * @return SchemaGeneratorInterface
     */
    public function setSchemaRegistry(SchemaRegistryLoaderInterface $schemaRegistry): SchemaGeneratorInterface;

    /**
     * @return SchemaRegistryLoaderInterface|null
     */
    public function getSchemaRegistry(): ?SchemaRegistryLoaderInterface;

    /**
     * @param string $outputDirectory
     * @return $this
     */
    public function setOutputDirectory(string $outputDirectory): SchemaGeneratorInterface;

    /**
     * @return string
     */
    public function getOutputDirectory(): string;

    /**
     * @param SchemaTemplateInterface $schemaTemplate
     * @return array
     */
    public function resolveSchemaTemplate(SchemaTemplateInterface $schemaTemplate): array;

    /**
     * @return void
     */
    public function generateSchema(): void;

    /**
     * @param array $schemaData
     * @return void
     */
    public function exportSchema(array $schemaData): void;

}
