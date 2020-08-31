<?php

declare(strict_types=1);

namespace NickZh\PhpAvroSchemaGenerator\Registry;

use NickZh\PhpAvroSchemaGenerator\PhpClass\PhpClass;

interface ClassRegistryInterface
{

    /**
     * @param  string $classDirectory
     * @return ClassRegistryInterface
     */
    public function addClassDirectory(string $classDirectory): ClassRegistryInterface;

    /**
     * @return array<string,int>
     */
    public function getClassDirectories(): array;

    /**
     * @return ClassRegistryInterface
     */
    public function load(): ClassRegistryInterface;

    /**
     * @return PhpClass[]
     */
    public function getClasses(): array;
}
