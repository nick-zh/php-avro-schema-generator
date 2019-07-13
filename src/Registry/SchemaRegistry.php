<?php

declare(strict_types=1);

namespace NickZh\PhpAvroSchemaGenerator\Registry;

use http\Exception\RuntimeException;

class SchemaRegistry
{

    /**
     * @var array
     */
    private $schemaDirectories;

    /**
     * @var array
     */
    private $schemas = [];

    /**
     * SchemaRegistry constructor.
     * @param array $schemaDirectories
     */
    public function __construct(array $schemaDirectories)
    {
        $this->schemaDirectories = $schemaDirectories;
    }

    /**
     * @return array
     */
    public function getSchemaDirectories(): array
    {
        return $this->schemaDirectories;
    }

    public function load()
    {
        foreach ($this->getSchemaDirectories() as $directory => $loneliestNumber) {
            $iterator = new \RecursiveIteratorIterator(new \RecursiveDirectoryIterator(
                $directory,
                \FilesystemIterator::SKIP_DOTS
            ));

            /** @var \SplFileInfo $file */
            foreach ($iterator as $file) {
                if ('avsc' === $file->getExtension()) {
                    $this->registerSchemaFile($file);
                }
            }
        }
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
     * @return mixed|null
     */
    public function getSchemaAsArray(string $schemaId)
    {
        if(false === isset($this->schemas[$schemaId])) {
            return null;
        }

        return $this->schemas[$schemaId];
    }

    /**
     * @param \SplFileInfo $fileInfo
     */
    protected function registerSchemaFile(\SplFileInfo $fileInfo)
    {
        $schemaData = json_decode(file_get_contents($fileInfo->getRealPath()), true);
        $this->schemas[$this->getSchemaId($schemaData)] = $schemaData;
    }

    /**
     * @param array $schemaData
     * @return string
     */
    public function getSchemaId(array $schemaData)
    {
        return $schemaData['namespace'] . '.' . $schemaData['name'];
    }
}
