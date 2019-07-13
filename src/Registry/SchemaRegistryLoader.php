<?php

declare(strict_types=1);

namespace NickZh\PhpAvroSchemaGenerator\Registry;

use \FilesystemIterator;
use NickZh\PhpAvroSchemaGenerator\Schema\SchemaTemplate;
use NickZh\PhpAvroSchemaGenerator\Schema\SchemaTemplateInterface;
use \RecursiveDirectoryIterator;
use \RecursiveIteratorIterator;
use \SplFileInfo;

final class SchemaRegistryLoader implements SchemaRegistryLoaderInterface
{

    const SCHEMA_LEVEL_ROOT = 'root';
    const SCHEMA_LEVEL_CHILD = 'child';
    const AVRO_FILE_EXTENSION = 'avsc';

    /**
     * @var array
     */
    private $schemaDirectories = [];

    /**
     * @var array
     */
    private $schemas = [];


    /**
     * @param string $inputDirectory
     * @return SchemaRegistryLoaderInterface
     */
    public function addSchemaDirectory(string $inputDirectory): SchemaRegistryLoaderInterface
    {
        $this->schemaDirectories[$inputDirectory] = 1;

        return $this;
    }

    /**
     * @return array
     */
    public function getRootSchema(): array
    {
        $rootSchema = [];

        /** @var SchemaTemplate $schema */
        foreach ($this->getSchemas() as $schema) {
            if (self::SCHEMA_LEVEL_ROOT == $schema->getSchemaLevel()) {
                $rootSchema[] = $schema;
            }
        }

        return $rootSchema;
    }

    /**
     * @return array
     */
    public function getSchemaDirectories(): array
    {
        return $this->schemaDirectories;
    }

    /**
     * @return SchemaRegistryLoaderInterface
     */
    public function load(): SchemaRegistryLoaderInterface
    {
        foreach ($this->getSchemaDirectories() as $directory => $loneliestNumber) {
            $iterator = new RecursiveIteratorIterator(new RecursiveDirectoryIterator(
                $directory,
                FilesystemIterator::SKIP_DOTS
            ));

            /** @var SplFileInfo $file */
            foreach ($iterator as $file) {
                if (self::AVRO_FILE_EXTENSION === $file->getExtension()) {
                    $this->registerSchemaFile($file);
                }
            }
        }

        return $this;
    }

    /**
     * @return array
     */
    public function getSchemas(): array
    {
        return $this->schemas;
    }

    /**
     * @param string $schemaId
     * @return SchemaTemplateInterface|null
     */
    public function getSchemaById(string $schemaId): ?SchemaTemplateInterface
    {
        if (false === isset($this->schemas[$schemaId])) {
            return null;
        }

        return $this->schemas[$schemaId];
    }

    /**
     * @param \SplFileInfo $fileInfo
     * @return void
     */
    protected function registerSchemaFile(SplFileInfo $fileInfo): void
    {
        $schemaData = json_decode(file_get_contents($fileInfo->getRealPath()), true);
        $this->schemas[$this->getSchemaId($schemaData)] = (new SchemaTemplate())
            ->withSchemaDefinition($schemaData)
            ->withSchemaLevel($this->getSchemaLevel($schemaData));
    }

    /**
     * @param array $schemaData
     * @return string
     */
    public function getSchemaId(array $schemaData): string
    {
        return $schemaData['namespace'] . '.' . $schemaData['name'];
    }

    /**
     * @param array $schemaData
     * @return string
     */
    private function getSchemaLevel(array $schemaData): string
    {
        if (true === isset($schemaData['schema_level']) && 'root' === $schemaData['schema_level']) {
            return 'root';
        }

        return 'child';
    }
}
