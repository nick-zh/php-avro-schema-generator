<?php

namespace NickZh\PhpAvroSchemaGenerator\Schema;

final class SchemaTemplate implements SchemaTemplateInterface
{

    /**
     * @var array
     */
    private $schemaDefinition = [];

    /**
     * @var string
     */
    private $schemaLevel = 'root';

    /**
     * @return array
     */
    public function getSchemaDefinition(): array
    {
        return $this->schemaDefinition;
    }

    /**
     * @return string
     */
    public function getSchemaLevel(): string
    {
        return $this->schemaLevel;
    }

    /**
     * @param array $schemaDefinition
     * @return SchemaTemplateInterface
     */
    public function withSchemaDefinition(array $schemaDefinition): SchemaTemplateInterface
    {
        $this->schemaDefinition = $schemaDefinition;

        return $this;
    }

    /**
     * @param string $schemaLevel
     * @return SchemaTemplateInterface
     */
    public function withSchemaLevel(string $schemaLevel): SchemaTemplateInterface
    {
        $this->schemaLevel = $schemaLevel;

        return $this;
    }
}