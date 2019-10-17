<?php

declare(strict_types=1);

namespace NickZh\PhpAvroSchemaGenerator\Merger;

use AvroSchemaParseException;
use NickZh\PhpAvroSchemaGenerator\Avro\Avro;
use NickZh\PhpAvroSchemaGenerator\Exception\SchemaMergerException;
use NickZh\PhpAvroSchemaGenerator\Exception\SchemaGenerationException;
use NickZh\PhpAvroSchemaGenerator\Exception\UnknownSchemaTypeException;
use NickZh\PhpAvroSchemaGenerator\Registry\SchemaRegistryInterface;
use NickZh\PhpAvroSchemaGenerator\Schema\SchemaTemplateInterface;

final class SchemaMerger implements SchemaMergerInterface
{

    /**
     * @var string
     */
    private $outputDirectory;

    /**
     * @var SchemaRegistryInterface
     */
    private $schemaRegistry;

    public function __construct(SchemaRegistryInterface $schemaRegistry, string $outputDirectory = '/tmp')
    {
        $this->schemaRegistry = $schemaRegistry;
        $this->outputDirectory = $outputDirectory;
    }

    /**
     * @return SchemaRegistryInterface
     */
    public function getSchemaRegistry(): SchemaRegistryInterface
    {
        return $this->schemaRegistry;
    }

    /**
     * @return string
     */
    public function getOutputDirectory(): string
    {
        return $this->outputDirectory;
    }

    /**
     * @param SchemaTemplateInterface $schemaTemplate
     * @return array
     * @throws AvroSchemaParseException
     * @throws SchemaMergerException
     */
    public function getAllTypesForSchemaTemplate(SchemaTemplateInterface $schemaTemplate): array
    {
        $types = [];

        do {
            $exceptionThrown = false;
            $definition = $schemaTemplate->getSchemaDefinition();
            try {
                \AvroSchema::parse($definition);
            } catch (AvroSchemaParseException $e) {
                if (false === strpos($e->getMessage(), ' is not a schema we know about.')) {
                    throw $e;
                }
                $exceptionThrown = true;
                $schemaId = $this->getSchemaIdFromExceptionMessage($e->getMessage());
                $embeddedTemplate = $this->schemaRegistry->getSchemaById($schemaId);

                if (null === $embeddedTemplate) {
                    throw new SchemaMergerException(
                        sprintf(SchemaMergerException::UNKNOWN_SCHEMA_TYPE_EXCEPTION_MESSAGE, $schemaId)
                    );
                }

                $types[] = $schemaId;
                $types = array_merge($types, $this->getAllTypesForSchemaTemplate($embeddedTemplate));
                $definition =  $this->removeSchemaIdFromDefinition($definition, $schemaId);
                $schemaTemplate = $schemaTemplate->withSchemaDefinition($definition);
            }
        } while (true === $exceptionThrown);

        return $types;
    }

    private function getSchemaIdFromExceptionMessage(string $exceptionMessage)
    {
        return str_replace(' is not a schema we know about.', '', $exceptionMessage);
    }

    private function removeSchemaIdFromDefinition(string $definition, string $schemaId)
    {
        $definition = str_replace($schemaId, 'string', $definition);
        $definition = str_replace('"string", "string"', '"string"', $definition);
        return str_replace('"string","string"', '"string"', $definition);
    }


    /**
     * @param boolean $prefixWithNamespace
     * @param boolean $useTemplateName
     * @return integer
     * @throws AvroSchemaParseException
     * @throws SchemaMergerException
     */
    public function merge(bool $prefixWithNamespace = false, bool $useTemplateName = false): int
    {
        $mergedFiles = 0;
        $registry = $this->getSchemaRegistry();

        /** @var SchemaTemplateInterface $schemaTemplate */
        foreach ($registry->getRootSchemas() as $schemaTemplate) {
            try {
                $schemaTypes = $this->getAllTypesForSchemaTemplate($schemaTemplate);
            } catch (SchemaMergerException $e) {
                throw $e;
            }
            $this->exportSchema($schemaTemplate, $schemaTypes, $prefixWithNamespace, $useTemplateName);
            ++$mergedFiles;
        }

        return $mergedFiles;
    }

    /**
     * @param SchemaTemplateInterface $rootSchemaTemplate
     * @param array                   $schemaTypes
     * @param boolean                 $prefixWithNamespace
     * @param boolean                 $useTemplateName
     * @return void
     *@throws SchemaMergerException
     */
    public function exportSchema(
        SchemaTemplateInterface $rootSchemaTemplate,
        array $schemaTypes,
        bool $prefixWithNamespace = false,
        bool $useTemplateName = false
    ): void {
        $schemas = [];
        $addedSchemas = [];

        $schemaTypes = array_reverse($schemaTypes);

        foreach ($schemaTypes as $schemaId) {
            if (true === isset($addedSchemas[$schemaId])) {
                continue;
            }

            $schemaTemplate = $this->schemaRegistry->getSchemaById($schemaId);

            if (null === $schemaTemplate) {
                throw new SchemaMergerException(
                    sprintf(SchemaMergerException::UNKNOWN_SCHEMA_TYPE_EXCEPTION_MESSAGE, $schemaId)
                );
            }

            $schemas[] = $this->transformExportSchemaDefinition(
                json_decode($schemaTemplate->getSchemaDefinition(), true)
            );

            $addedSchemas[$schemaId] = true;
        }

        $rootSchemaDefinition = $this->transformExportSchemaDefinition(
            json_decode($rootSchemaTemplate->getSchemaDefinition(), true)
        );

        $schemas[] = $rootSchemaDefinition;

        $prefix = '';

        if (true === $prefixWithNamespace) {
            $prefix = $rootSchemaDefinition['namespace'] . '.';
        }

        $schemaFilename = $prefix . $rootSchemaDefinition['name'] . '.' . Avro::FILE_EXTENSION;

        if (true === $useTemplateName) {
            $schemaFilename = $rootSchemaTemplate->getFilename();
        }

        if (false === file_exists($this->getOutputDirectory())) {
            mkdir($this->getOutputDirectory());
        }

        $fileContents = json_encode($rootSchemaDefinition);

        if (1 < count($schemas)) {
            $fileContents = json_encode($schemas);
        }

        file_put_contents($this->getOutputDirectory() . '/' . $schemaFilename, $fileContents);
    }

    /**
     * @param  array $schemaDefinition
     * @return array
     */
    public function transformExportSchemaDefinition(array $schemaDefinition): array
    {
        unset($schemaDefinition['schema_level']);

        return $schemaDefinition;
    }
}
