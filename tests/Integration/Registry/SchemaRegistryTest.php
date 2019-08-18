<?php

namespace NickZh\PhpAvroSchemaGenerator\Tests\Integration\Registry;

use NickZh\PhpAvroSchemaGenerator\Exception\SchemaRegistryException;
use NickZh\PhpAvroSchemaGenerator\Registry\SchemaRegistry;
use NickZh\PhpAvroSchemaGenerator\Registry\SchemaRegistryInterface;
use NickZh\PhpAvroSchemaGenerator\Schema\SchemaTemplateInterface;
use PHPUnit\Framework\TestCase;
use ReflectionClass;
use SplFileInfo;

/**
 * @covers NickZh\PhpAvroSchemaGenerator\Registry\SchemaRegistry
 */
class SchemaRegistryTest extends TestCase
{
    public function testSchemaDirectories()
    {
        $registry = new SchemaRegistry();
        $result = $registry->addSchemaTemplateDirectory('/tmp');

        self::assertInstanceOf(SchemaRegistryInterface::class, $result);
        self::assertEquals(['/tmp' => 1], $result->getSchemaDirectories());
    }

    public function testLoad()
    {
        $schemaIds = [
            'com.example.Book',
            'com.example.CD',
            'com.example.Collection',
            'com.example.Page',
            'com.example.Library',
        ];

        $schemaDir = __DIR__ . '/../../../example/schemaTemplates';
        $registry = (new SchemaRegistry())->addSchemaTemplateDirectory($schemaDir)->load();
        $schemas = $registry->getSchemas();

        self::assertCount(5, $schemas);

        /** @var SchemaTemplateInterface $schema */
        foreach ($schemas as $schema) {
            self::assertInstanceOf(SchemaTemplateInterface::class, $schema);
            self::assertContains($schema->getSchemaId(), $schemaIds);
        }
    }

    public function testGetRootSchemas()
    {
        $schemaDir = __DIR__ . '/../../../example/schemaTemplates';
        $registry = (new SchemaRegistry())->addSchemaTemplateDirectory($schemaDir)->load();

        $rootSchemas = $registry->getRootSchemas();

        self::assertCount(1, $rootSchemas);

        foreach ($rootSchemas as $rootSchema) {
            self::assertInstanceOf(SchemaTemplateInterface::class, $rootSchema);
        }
    }

    public function testGetSchemaByIdNotExisting()
    {
        $registry = new SchemaRegistry();

        self::assertNull($registry->getSchemaById('test'));
    }

    public function testGetSchemaById()
    {
        $template = $this->getMockForAbstractClass(SchemaTemplateInterface::class);

        $registry = new SchemaRegistry();

        $reflection = new ReflectionClass($registry);
        $property = $reflection->getProperty('schemas');
        $property->setAccessible(true);
        $property->setValue($registry, ['test' => $template]);

        self::assertEquals($template, $registry->getSchemaById('test'));
    }

    public function testRegisterSchemaFileThatDoesntExist()
    {
        $fileInfo = new SplFileInfo('somenonexistingfile');
        $registry = new SchemaRegistry();

        self::expectException(SchemaRegistryException::class);
        self::expectExceptionMessage(SchemaRegistryException::FILE_PATH_EXCEPTION_MESSAGE);

        $reflection = new ReflectionClass(SchemaRegistry::class);
        $method = $reflection->getMethod('registerSchemaFile');
        $method->setAccessible(true);
        $method->invokeArgs($registry, [$fileInfo]);
    }

    public function testRegisterSchemaFileThatIsNotReadable()
    {
        touch('testfile');
        chmod('testfile', 222);

        $fileInfo = new SplFileInfo('testfile');

        $registry = new SchemaRegistry();

        self::expectException(SchemaRegistryException::class);
        self::expectExceptionMessage(
            sprintf(SchemaRegistryException::FILE_NOT_READABLE_EXCEPTION_MESSAGE, $fileInfo->getRealPath())
        );

        $reflection = new ReflectionClass(SchemaRegistry::class);
        $method = $reflection->getMethod('registerSchemaFile');
        $method->setAccessible(true);
        try {
            $method->invokeArgs($registry, [$fileInfo]);
        } finally {
            unlink('testfile');
        }
    }
}
