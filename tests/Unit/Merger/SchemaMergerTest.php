<?php

declare(strict_types=1);

namespace NickZh\PhpAvroSchemaGenerator\Tests\Unit\Merger;

use NickZh\PhpAvroSchemaGenerator\Exception\SchemaMergerException;
use NickZh\PhpAvroSchemaGenerator\Merger\SchemaMerger;
use NickZh\PhpAvroSchemaGenerator\Registry\SchemaRegistryInterface;
use NickZh\PhpAvroSchemaGenerator\Schema\SchemaTemplateInterface;
use PHPUnit\Framework\TestCase;

/**
 * @covers NickZh\PhpAvroSchemaGenerator\Merger\SchemaMerger
 */
class SchemaMergerTest extends TestCase
{

    public function testGetSchemaRegistry()
    {
        $schemaRegistry = $this->getMockForAbstractClass(SchemaRegistryInterface::class);
        $merger = new SchemaMerger($schemaRegistry);
        self::assertEquals($schemaRegistry, $merger->getSchemaRegistry());
    }

    public function testGetOutputDirectoryDefault()
    {
        $schemaRegistry = $this->getMockForAbstractClass(SchemaRegistryInterface::class);
        $merger = new SchemaMerger($schemaRegistry);
        self::assertEquals('/tmp', $merger->getOutputDirectory());
    }

    public function testGetOutputDirectory()
    {
        $schemaRegistry = $this->getMockForAbstractClass(SchemaRegistryInterface::class);
        $outputDirectory = '/root';
        $merger = new SchemaMerger($schemaRegistry, $outputDirectory);
        self::assertEquals($outputDirectory, $merger->getOutputDirectory());
    }

    public function testGetAllTypesForSchemaTemplate()
    {
        $schemaRegistry = $this->getMockForAbstractClass(SchemaRegistryInterface::class);
        $schemaTemplate = $this->getMockForAbstractClass(SchemaTemplateInterface::class);
        $schemaTemplate->expects(self::once())->method('getSchemaDefinition')->willReturn('{}');
        $merger = new SchemaMerger($schemaRegistry);

        self::assertEquals([], $merger->getAllTypesForSchemaTemplate($schemaTemplate));
    }

    public function testGetAllTypesForSchemaTemplateThrowsException()
    {
        self::expectException(\AvroSchemaParseException::class);

        $schemaRegistry = $this->getMockForAbstractClass(SchemaRegistryInterface::class);
        $schemaTemplate = $this->getMockForAbstractClass(SchemaTemplateInterface::class);
        $schemaTemplate->expects(self::once())->method('getSchemaDefinition')->willReturn('{"type": 1}');
        $merger = new SchemaMerger($schemaRegistry);

        self::assertEquals([], $merger->getAllTypesForSchemaTemplate($schemaTemplate));
    }

    public function testGetAllTypesForSchemaTemplateResolveEmbeddedException()
    {
        self::expectException(SchemaMergerException::class);
        self::expectExceptionMessage(sprintf(SchemaMergerException::UNKNOWN_SCHEMA_TYPE_EXCEPTION_MESSAGE, 'com.example.Page'));

        $definitionWithType = '{
            "type": "record",
            "namespace": "com.example",
            "name": "Book",
            "fields": [
                { "name": "items", "type": {"type": "array", "items": "com.example.Page" }, "default": [] }
            ]
        }';
        $schemaRegistry = $this->getMockForAbstractClass(SchemaRegistryInterface::class);
        $schemaTemplate = $this->getMockForAbstractClass(SchemaTemplateInterface::class);
        $schemaTemplate
            ->expects(self::once())
            ->method('getSchemaDefinition')
            ->willReturn($definitionWithType);
        $merger = new SchemaMerger($schemaRegistry);

        self::assertEquals([], $merger->getAllTypesForSchemaTemplate($schemaTemplate));
    }

    public function testGetAllTypesForSchemaTemplateResolveEmbedded()
    {
        $definitionWithType = '{
            "type": "record",
            "namespace": "com.example",
            "name": "Book",
            "fields": [
                { "name": "items", "type": {"type": "array", "items": ["string","com.example.Page"] }, "default": [] }
            ]
        }';
        $replacedDefinition = '{
            "type": "record",
            "namespace": "com.example",
            "name": "Book",
            "fields": [
                { "name": "items", "type": {"type": "array", "items": ["string"] }, "default": [] }
            ]
        }';
        $emptyTemplate = $this->getMockForAbstractClass(SchemaTemplateInterface::class);
        $emptyTemplate
            ->expects(self::exactly(2))
            ->method('getSchemaDefinition')
            ->willReturn('{}');
        $schemaTemplate = $this->getMockForAbstractClass(SchemaTemplateInterface::class);
        $schemaTemplate
            ->expects(self::once())
            ->method('getSchemaDefinition')
            ->willReturn($definitionWithType);
        $schemaTemplate->expects(self::once())
            ->method('withSchemaDefinition')
            ->with($replacedDefinition)
            ->willReturn($emptyTemplate);
        $schemaRegistry = $this->getMockForAbstractClass(SchemaRegistryInterface::class);
        $schemaRegistry
            ->expects(self::once())
            ->method('getSchemaById')
            ->with('com.example.Page')
            ->willReturn($emptyTemplate);
        $merger = new SchemaMerger($schemaRegistry);

        self::assertEquals(['com.example.Page'], $merger->getAllTypesForSchemaTemplate($schemaTemplate));
    }

    public function testMergeException()
    {
        self::expectException(SchemaMergerException::class);
        self::expectExceptionMessage(sprintf(SchemaMergerException::UNKNOWN_SCHEMA_TYPE_EXCEPTION_MESSAGE, 'com.example.Page'));

        $definitionWithType = '{
            "type": "record",
            "namespace": "com.example",
            "name": "Book",
            "fields": [
                { "name": "items", "type": {"type": "array", "items": ["string","com.example.Page"] }, "default": [] }
            ]
        }';
        $schemaTemplate = $this->getMockForAbstractClass(SchemaTemplateInterface::class);
        $schemaTemplate
            ->expects(self::once())
            ->method('getSchemaDefinition')
            ->willReturn($definitionWithType);

        $schemaRegistry = $this->getMockForAbstractClass(SchemaRegistryInterface::class);
        $schemaRegistry
            ->expects(self::once())
            ->method('getRootSchemas')
            ->willReturn([$schemaTemplate]);
        $merger = new SchemaMerger($schemaRegistry);
        $merger->merge();
    }

    public function testMerge()
    {
        $definitionWithType = '{
            "type": "record",
            "namespace": "com.example",
            "name": "Book",
            "fields": [
                { "name": "items", "type": {"type": "array", "items": ["string"] }, "default": [] }
            ]
        }';
        $schemaTemplate = $this->getMockForAbstractClass(SchemaTemplateInterface::class);
        $schemaTemplate
            ->expects(self::exactly(2))
            ->method('getSchemaDefinition')
            ->willReturn($definitionWithType);

        $schemaRegistry = $this->getMockForAbstractClass(SchemaRegistryInterface::class);
        $schemaRegistry
            ->expects(self::once())
            ->method('getRootSchemas')
            ->willReturn([$schemaTemplate]);
        $merger = new SchemaMerger($schemaRegistry, '/tmp/foobar');
        $merger->merge();

        self::assertFileExists('/tmp/foobar/Book.avsc');
        unlink('/tmp/foobar/Book.avsc');
        rmdir('/tmp/foobar');
    }

    public function testExportSchemaException()
    {
        self::expectException(SchemaMergerException::class);
        self::expectExceptionMessage(sprintf(SchemaMergerException::UNKNOWN_SCHEMA_TYPE_EXCEPTION_MESSAGE, 'test'));

        $schemaRegistry = $this->getMockForAbstractClass(SchemaRegistryInterface::class);
        $schemaRegistry
            ->expects(self::once())
            ->method('getSchemaById')
            ->willReturn(null);
        $schemaTemplate = $this->getMockForAbstractClass(SchemaTemplateInterface::class);

        $merger = new SchemaMerger($schemaRegistry, '/tmp/foobar');
        $merger->exportSchema($schemaTemplate, ['test']);
    }

    public function testExportSchema()
    {
        $schemaTemplate = $this->getMockForAbstractClass(SchemaTemplateInterface::class);
        $schemaTemplate
            ->expects(self::exactly(2))
            ->method('getSchemaDefinition')
            ->willReturn('{"name": "test"}');
        $schemaRegistry = $this->getMockForAbstractClass(SchemaRegistryInterface::class);
        $schemaRegistry
            ->expects(self::once())
            ->method('getSchemaById')
            ->willReturn($schemaTemplate);

        $merger = new SchemaMerger($schemaRegistry);
        $merger->exportSchema($schemaTemplate, ['test', 'test']);

        self::assertFileExists('/tmp/test.avsc');
        unlink('/tmp/test.avsc');
    }
}
