<?php

declare(strict_types=1);

namespace NickZh\PhpAvroSchemaGenerator\Registry;

interface ClassRegistryInterface
{

    /**
     * @param  string $classDirectory
     * @return ClassRegistryInterface
     */
    public function addClassDirectory(string $classDirectory): ClassRegistryInterface;

    /**
     * @return array
     */
    public function getClassDirectories(): array;

    /**
     * @return ClassRegistryInterface
     */
    public function load(): ClassRegistryInterface;

    /**
     * @return array
     */
    public function getClasses(): array;
}
