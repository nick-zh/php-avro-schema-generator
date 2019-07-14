<?php

declare(strict_types=1);

namespace NickZh\PhpAvroSchemaGenerator\Registry;

use \FilesystemIterator;
use NickZh\PhpAvroSchemaGenerator\Avro\Avro;
use NickZh\PhpAvroSchemaGenerator\Schema\SchemaTemplate;
use NickZh\PhpAvroSchemaGenerator\Schema\SchemaTemplateInterface;
use \RecursiveDirectoryIterator;
use \RecursiveIteratorIterator;
use \SplFileInfo;

final class SchemaRegistry implements SchemaRegistryInterface
{

    const SCHEMA_LEVEL_ROOT = 'root';
    const SCHEMA_LEVEL_CHILD = 'child';

    /**
     * @var array
     */
    private $schemaDirectories = [];

    /**
     * @var array
     */
    private $schemas = [];


    /**
     * @param string $schemaTemplateDirectory
     * @return SchemaRegistryInterface
     */
    public function addSchemaTemplateDirectory(string $schemaTemplateDirectory): SchemaRegistryInterface
    {
        $this->schemaDirectories[$schemaTemplateDirectory] = 1;

        return $this;
    }

    /**
     * @return array
     */
    public function getRootSchemas(): array
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
     * @return SchemaRegistryInterface
     */
    public function load(): SchemaRegistryInterface
    {
        foreach ($this->getSchemaDirectories() as $directory => $loneliestNumber) {
            $iterator = new RecursiveIteratorIterator(new RecursiveDirectoryIterator(
                $directory,
                FilesystemIterator::SKIP_DOTS
            ));

            /** @var SplFileInfo $file */
            foreach ($iterator as $file) {
                if (Avro::FILE_EXTENSION === $file->getExtension()) {
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
    private function registerSchemaFile(SplFileInfo $fileInfo): void
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
