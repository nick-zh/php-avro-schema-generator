<?php

declare(strict_types=1);

namespace NickZh\PhpAvroSchemaGenerator\Generator;

use NickZh\PhpAvroSchemaGenerator\Registry\SchemaRegistry;

class SchemaGenerator
{
    /**
     * @var string
     */
    private $outputDirectory;

    /**
     * @var array
     */
    private $inputDirectories = [];

    /**
     * @var array
     */
    private $schemaFiles = [];

    /**
     * @return SchemaGenerator
     */
    public static function create(): self
    {
        return new self();
    }

    /**
     * @param string $inputDirectory
     * @return $this
     */
    public function addInputDirectory(string $inputDirectory)
    {
        $this->inputDirectories[$inputDirectory] = 1;

        return $this;
    }

    /**
     * @return array
     */
    public function getInputDirectories(): array
    {
        return $this->inputDirectories;
    }

    /**
     * @param string $inputDirectory
     * @return $this
     */
    public function addSchemaFile(string $schemaFilePath)
    {
        $this->schemaFiles[$schemaFilePath] = 1;

        return $this;
    }

    /**
     * @return array
     */
    public function getSchemaFiles(): array
    {
        return $this->schemaFiles;
    }

    /**
     * @param string $outputDirectory
     * @return $this
     */
    public function setOutputDirectory(string $outputDirectory)
    {
        $this->outputDirectory = $outputDirectory;

        return $this;
    }

    public function getOutputDirectory()
    {
        return $this->outputDirectory;
    }

    /**
     * @param SchemaRegistry $registry
     * @param array $schemaData
     * @return array
     */
    public function resolveSchema(SchemaRegistry $registry, array $schemaData)
    {
        foreach($schemaData['fields'] as $idx => $field) {
            if(true === is_array($field['type'])) {
                $type = 'amazing';
                if(true === isset($field['type']['items'])) {
                    $type = $field['type']['items'];
                }
            } else {
                $type = $field['type'];
            }

            $resolvedSchema = $registry->getSchemaAsArray($type);

            if(null === $resolvedSchema) {
                continue;
            }

            if(true === is_array($field['type'])) {
                $schemaData['fields'][$idx]['type']['items'] = $this->resolveSchema($registry, $resolvedSchema);
            } else {
                $schemaData['fields'][$idx]['type'] = $this->resolveSchema($registry, $resolvedSchema);
            }

        }

        return $schemaData;
    }

    public function generateSchema()
    {
        $registry = new SchemaRegistry($this->getInputDirectories());
        $registry->load();

        foreach ($this->getSchemaFiles() as $schemaFilePath => $loneliestNumber) {
            $schemaData = json_decode(file_get_contents($schemaFilePath), true);
            $schemaData = $this->resolveSchema($registry, $schemaData);
            $this->saveSchema($schemaData);
        }
    }

    /**
     * @param array $schemaData
     */
    public function saveSchema(array $schemaData)
    {
        $schemaFilename = $schemaData['name'] . '.avsc';

        if(false === file_exists($this->getOutputDirectory())) {
            mkdir($this->getOutputDirectory());
        }

        file_put_contents($this->getOutputDirectory() . '/' .$schemaFilename, json_encode($schemaData));
    }

}
