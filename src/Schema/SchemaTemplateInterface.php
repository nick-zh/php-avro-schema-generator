<?php

namespace NickZh\PhpAvroSchemaGenerator\Schema;

interface SchemaTemplateInterface
{
    /**
     * @return string
     */
    public function getSchemaDefinition(): string;

    /**
     * @return string
     */
    public function getSchemaLevel(): string;

    /**
     * @return string
     */
    public function getSchemaId(): string;

    /**
     * @param  string $schemaId
     * @return SchemaTemplateInterface
     */
    public function withSchemaId(string $schemaId): SchemaTemplateInterface;

    /**
     * @param  string $schemaDefinition
     * @return SchemaTemplateInterface
     */
    public function withSchemaDefinition(string $schemaDefinition): SchemaTemplateInterface;

    /**
     * @param  string $schemaLevel
     * @return SchemaTemplateInterface
     */
    public function withSchemaLevel(string $schemaLevel): SchemaTemplateInterface;
}
