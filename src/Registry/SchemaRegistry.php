<?php

declare(strict_types=1);

namespace NickZh\PhpAvroSchemaGenerator\Registry;

use NickZh\PhpAvroSchemaGenerator\Avro\Avro;
use NickZh\PhpAvroSchemaGenerator\Exception\SchemaRegistryException;
use NickZh\PhpAvroSchemaGenerator\Schema\SchemaTemplate;
use NickZh\PhpAvroSchemaGenerator\Schema\SchemaTemplateInterface;

final class SchemaRegistry implements SchemaRegistryInterface
{

    public const SCHEMA_LEVEL_ROOT = 'root';
    public const SCHEMA_LEVEL_CHILD = 'child';

    /**
     * @var array
     */
    private $schemaDirectories = [];

    /**
     * @var array
     */
    private $schemas = [];


    /**
     * @param  string $schemaTemplateDirectory
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
        $rootSchemas = [];

        /**
 * @var SchemaTemplate $schema
*/
        foreach ($this->getSchemas() as $schema) {
            if (self::SCHEMA_LEVEL_ROOT == $schema->getSchemaLevel()) {
                $rootSchemas[] = $schema;
            }
        }

        return $rootSchemas;
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
     * @throws SchemaRegistryException
     */
    public function load(): SchemaRegistryInterface
    {
        foreach ($this->getSchemaDirectories() as $directory => $loneliestNumber) {
            $iterator = new \RecursiveIteratorIterator(
                new \RecursiveDirectoryIterator(
                    $directory,
                    \FilesystemIterator::SKIP_DOTS
                )
            );

            /** @var \SplFileInfo $file */
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
     * @param  string $schemaId
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
     * @param  \SplFileInfo $fileInfo
     * @throws SchemaRegistryException
     * @return void
     */
    private function registerSchemaFile(\SplFileInfo $fileInfo): void
    {
        if (false === $fileName = $fileInfo->getRealPath()) {
            throw new SchemaRegistryException(SchemaRegistryException::FILE_PATH_EXCEPTION_MESSAGE);
        }

        if (false === $fileContent = @file_get_contents($fileName)) {
            throw new SchemaRegistryException(
                sprintf(
                    SchemaRegistryException::FILE_NOT_READABLE_EXCEPTION_MESSAGE,
                    $fileName
                )
            );
        }

        $schemaData = json_decode($fileContent, true);
        $schemaId = $this->getSchemaId($schemaData);
        $this->schemas[$schemaId] = (new SchemaTemplate())
            ->withSchemaId($schemaId)
            ->withSchemaDefinition($fileContent)
            ->withSchemaLevel($this->getSchemaLevel($schemaData));
    }

    /**
     * @param  array $schemaData
     * @return string
     */
    public function getSchemaId(array $schemaData): string
    {
        return $schemaData['namespace'] . '.' . $schemaData['name'];
    }

    /**
     * @param  array $schemaData
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
