<?php

declare(strict_types=1);

namespace NickZh\PhpAvroSchemaGenerator\Registry;

final class ClassRegistry implements ClassRegistryInterface
{

    /**
     * @param string $classDirectory
     * @return ClassRegistryInterface
     */
    public function addClassDirectory(string $classDirectory): ClassRegistryInterface
    {
        // TODO: Implement addClassDirectory() method.
    }

    /**
     * @return array
     */
    public function getClassDirectories(): array
    {
        // TODO: Implement getClassDirectories() method.
    }

    /**
     * @return ClassRegistryInterface
     */
    public function load(): ClassRegistryInterface
    {
        // TODO: Implement load() method.
    }

    /**
     * @return array
     */
    public function getClasses(): array
    {
        // TODO: Implement getClasses() method.
    }
}
