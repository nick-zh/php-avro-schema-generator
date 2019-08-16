<?php

declare(strict_types=1);

namespace NickZh\PhpAvroSchemaGenerator\Merger;

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
    private $outputDirectory = '/tmp';

    /**
     * @var SchemaRegistryInterface
     */
    private $schemaRegistry;

    private function __construct()
    {
    }

    /**
     * @return SchemaMerger
     */
    public static function create(): SchemaMergerInterface
    {
        return new self();
    }

    /**
     * @param  SchemaRegistryInterface $schemaRegistry
     * @return SchemaMergerInterface
     */
    public function setSchemaRegistry(SchemaRegistryInterface $schemaRegistry): SchemaMergerInterface
    {
        $this->schemaRegistry = $schemaRegistry;

        return $this;
    }

    /**
     * @return SchemaRegistryInterface|null
     */
    public function getSchemaRegistry(): ?SchemaRegistryInterface
    {
        return $this->schemaRegistry;
    }

    /**
     * @param  string $outputDirectory
     * @return $this
     */
    public function setOutputDirectory(string $outputDirectory): SchemaMergerInterface
    {
        $this->outputDirectory = $outputDirectory;

        return $this;
    }

    /**
     * @return string
     */
    private function getOutputDirectory(): string
    {
        return $this->outputDirectory;
    }

    /**
     * @param  SchemaTemplateInterface $schemaTemplate
     * @return SchemaTemplateInterface
     * @throws SchemaMergerException
     */
    public function resolveSchemaTemplate(SchemaTemplateInterface $schemaTemplate): SchemaTemplateInterface
    {
        $schemaDefinition = $schemaTemplate->getSchemaDefinition();

        foreach ($schemaDefinition['fields'] as $idx => $field) {
            $type = $field['type'];

            if (true === is_array($type) && true === isset($type['items'])) {
                $schemaDefinition['fields'][$idx]['type']['items'] = $this->getResolvedType($type['items']);
            } else {
                $schemaDefinition['fields'][$idx]['type'] = $this->getResolvedType($type);
            }
        }

        return $schemaTemplate->withSchemaDefinition($schemaDefinition);
    }

    /**
     * @param  mixed $type
     * @return array|string
     * @throws SchemaMergerException
     */
    private function getResolvedType($type)
    {
        if (true === is_string($type)) {
            return $this->getTypeValue($type);
        }

        if (false === is_array($type)) {
            return $type;
        }

        $result = [];

        foreach ($type as $typeItem) {
            if (true === is_array($typeItem)) {
                $typeItem['type'] = $this->getTypeValue($typeItem['type']);
                $result[] = $typeItem;
                continue;
            }

            $result[] = $this->getTypeValue($typeItem);
        }

        return $result;
    }

    /**
     * @param  string $type
     * @return mixed
     * @throws SchemaMergerException
     */
    private function getTypeValue(string $type)
    {
        $typeDefinition = $this->getTypeDefinition($type);

        if ($typeDefinition instanceof SchemaTemplateInterface) {
            return $this->transformChildSchemaDefinition($typeDefinition->getSchemaDefinition());
        }

        return $type;
    }

    /**
     * @param  string $type
     * @return SchemaTemplateInterface|null
     * @throws SchemaMergerException
     */
    private function getTypeDefinition(string $type): ?SchemaTemplateInterface
    {
        if (true === $this->isAvroType($type)) {
            return null;
        }

        if (null === $this->getSchemaRegistry()) {
            throw new SchemaMergerException(SchemaMergerException::NO_SCHEMA_REGISTRY_SET_EXCEPTION_MESSAGE);
        }

        $schemaTemplate = $this->getSchemaRegistry()->getSchemaById($type);

        if (null === $schemaTemplate) {
            throw new SchemaMergerException(
                sprintf(
                    SchemaMergerException::UNKNOWN_SCHEMA_TYPE_EXCEPTION_MESSAGE,
                    $type
                )
            );
        }

        return $this->resolveSchemaTemplate($schemaTemplate);
    }


    /**
     * @return int
     * @throws SchemaMergerException
     */
    public function merge(): int
    {
        $mergedFiles = 0;
        $registry = $this->getSchemaRegistry();

        if (null === $registry) {
            throw new SchemaMergerException(SchemaMergerException::NO_SCHEMA_REGISTRY_SET_EXCEPTION_MESSAGE);
        }

        /** @var SchemaTemplateInterface $schemaTemplate */
        foreach ($registry->getRootSchemas() as $schemaTemplate) {
            try {
                $schemaTemplate = $this->resolveSchemaTemplate($schemaTemplate);
            } catch (SchemaMergerException $e) {
                throw $e;
            }
            $this->exportSchema($schemaTemplate);
            ++$mergedFiles;
        }

        return $mergedFiles;
    }

    /**
     * @param  SchemaTemplateInterface $schemaTemplate
     * @return void
     */
    public function exportSchema(SchemaTemplateInterface $schemaTemplate): void
    {
        $schemaDefinition = $this->transformExportSchemaDefinition($schemaTemplate->getSchemaDefinition());

        $schemaFilename = $schemaDefinition['name'] . '.' . Avro::FILE_EXTENSION;

        if (false === file_exists($this->getOutputDirectory())) {
            mkdir($this->getOutputDirectory());
        }

        file_put_contents($this->getOutputDirectory() . '/' . $schemaFilename, json_encode($schemaDefinition));
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

    /**
     * @param  array $schemaDefinition
     * @return array
     */
    public function transformChildSchemaDefinition(array $schemaDefinition): array
    {
        unset($schemaDefinition['namespace']);

        return $schemaDefinition;
    }

    /**
     * @param  string $type
     * @return boolean
     */
    private function isAvroType(string $type): bool
    {
        return true === isset(Avro::TYPES[$type]);
    }
}
