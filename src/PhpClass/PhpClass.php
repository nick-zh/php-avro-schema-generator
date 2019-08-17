<?php

declare(strict_types=1);

namespace NickZh\PhpAvroSchemaGenerator\PhpClass;

class PhpClass implements PhpClassInterface
{
    private $classBody;

    private $className;

    private $classNamespace;

    private $classProperties;

    /**
     * @param string $className
     * @param string $classNamespace
     * @param string $classBody
     * @param array  $classProperties
     */
    public function __construct(string $className, string $classNamespace, string $classBody, array $classProperties)
    {
        $this->className = $className;
        $this->classNamespace = $classNamespace;
        $this->classBody = $classBody;
        $this->classProperties = $classProperties;
    }

    /**
     * @return string
     */
    public function getClassNamespace(): string
    {
        return $this->classNamespace;
    }

    /**
     * @return string
     */
    public function getClassName(): string
    {
        return $this->classNamespace;
    }

    /**
     * @return string
     */
    public function getClassBody(): string
    {
        return $this->classBody;
    }

    /**
     * @return array
     */
    public function getClassProperties(): array
    {
        return $this->classProperties;
    }
}
