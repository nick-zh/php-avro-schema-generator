<?php

namespace NickZh\PhpAvroSchemaGenerator\Schema;

interface SchemaTemplateInterface
{
    /**
     * @return array
     */
    public function getSchemaDefinition(): array;

    /**
     * @return string
     */
    public function getSchemaLevel(): string;

    /**
     * @param array $schemaDefinition
     * @return SchemaTemplateInterface
     */
    public function withSchemaDefinition(array $schemaDefinition): SchemaTemplateInterface;

    /**
     * @param string $schemaLevel
     * @return SchemaTemplateInterface
     */
    public function withSchemaLevel(string $schemaLevel): SchemaTemplateInterface;
}