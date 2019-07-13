<?php

declare(strict_types=1);

namespace NickZh\PhpAvroSchemaGenerator\Generator;

use NickZh\PhpAvroSchemaGenerator\Registry\SchemaRegistry;
use NickZh\PhpAvroSchemaGenerator\Registry\SchemaRegistryLoaderInterface;
use NickZh\PhpAvroSchemaGenerator\Schema\SchemaTemplateInterface;

final class SchemaGenerator implements SchemaGeneratorInterface
{

    /**
     * @var string
     */
    private $outputDirectory = '/tmp';

    /**
     * @var SchemaRegistryLoaderInterface
     */
    private $schemaRegistry;

    /**
     * @return SchemaGenerator
     */
    public static function create(): SchemaGeneratorInterface
    {
        return new self();
    }

    /**
     * @param SchemaRegistryLoaderInterface $schemaRegistry
     * @return SchemaGeneratorInterface
     */
    public function setSchemaRegistry(SchemaRegistryLoaderInterface $schemaRegistry): SchemaGeneratorInterface
    {
        $this->schemaRegistry = $schemaRegistry;

        return $this;
    }

    /**
     * @return SchemaRegistryLoaderInterface|null
     */
    public function getSchemaRegistry(): ?SchemaRegistryLoaderInterface
    {
        return $this->schemaRegistry;
    }

    /**
     * @param string $outputDirectory
     * @return $this
     */
    public function setOutputDirectory(string $outputDirectory): SchemaGeneratorInterface
    {
        $this->outputDirectory = $outputDirectory;

        return $this;
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
     */
    public function resolveSchemaTemplate(SchemaTemplateInterface $schemaTemplate): array
    {
        $schemaData = $schemaTemplate->getSchemaDefinition();

        foreach ($schemaData['fields'] as $idx => $field) {
            if (true === is_array($field['type'])) {
                $type = 'amazing';
                if (true === isset($field['type']['items'])) {
                    $type = $field['type']['items'];
                }
            } else {
                $type = $field['type'];
            }

            $resolvedSchema = $this->getSchemaRegistry()->getSchemaById($type);

            if (null === $resolvedSchema) {
                continue;
            }

            if (true === is_array($field['type'])) {
                $schemaData['fields'][$idx]['type']['items'] = $this->resolveSchemaTemplate($resolvedSchema);
            } else {
                $schemaData['fields'][$idx]['type'] = $this->resolveSchemaTemplate($resolvedSchema);
            }

        }

        return $schemaData;
    }


    /**
     * @return void
     */
    public function generateSchema(): void
    {
        $registry = $this->getSchemaRegistry();

        if (null === $registry) {
            return;
        }

        /** @var SchemaTemplateInterface $schemaTemplate */
        foreach ($registry->getRootSchema() as $schemaTemplate) {
            $schemaData = $this->resolveSchemaTemplate($schemaTemplate);
            unset($schemaData['schema_level']);
            $this->exportSchema($schemaData);
        }
    }

    /**
     * @param array $schemaData
     * @return void
     */
    public function exportSchema(array $schemaData): void
    {
        $schemaFilename = $schemaData['name'] . '.avsc';

        if (false === file_exists($this->getOutputDirectory())) {
            mkdir($this->getOutputDirectory());
        }

        file_put_contents($this->getOutputDirectory() . '/' .$schemaFilename, json_encode($schemaData));
    }

}
