<?php

declare(strict_types=1);

namespace NickZh\PhpAvroSchemaGenerator\Tests\Unit\Generator;

use NickZh\PhpAvroSchemaGenerator\Merger\SchemaMerger;
use NickZh\PhpAvroSchemaGenerator\Merger\SchemaMergerInterface;
use NickZh\PhpAvroSchemaGenerator\Registry\SchemaRegistryInterface;
use NickZh\PhpAvroSchemaGenerator\Schema\SchemaTemplateInterface;
use PHPUnit\Framework\TestCase;

/**
 * @covers NickZh\PhpAvroSchemaGenerator\Merger\SchemaMerger
 */
class SchemaMergerTest extends TestCase
{
    public function testCreate()
    {
        $this->assertInstanceOf(SchemaMergerInterface::class, SchemaMerger::create());
    }

    public function testSetSchemaRegistry()
    {
        $schemaRegistry = $this->getMockForAbstractClass(SchemaRegistryInterface::class);

        $merger = SchemaMerger::create();

        $this->assertInstanceOf(SchemaMergerInterface::class, $merger->setSchemaRegistry($schemaRegistry));
        $this->assertInstanceOf(SchemaRegistryInterface::class, $merger->getSchemaRegistry());
    }

    public function testSetOutputDirectory()
    {
        $merger = SchemaMerger::create();

        $this->assertInstanceOf(SchemaMergerInterface::class, $merger->setOutputDirectory('test-dir'));

        $reflectionProperty = new \ReflectionProperty($merger, 'outputDirectory');
        $reflectionProperty->setAccessible(true);

        self::assertSame('test-dir', $reflectionProperty->getValue($merger));
    }

    public function testResolveSchemaTemplateBasic()
    {
        $merger = SchemaMerger::create();
        $schemaDefinition = [
            'fields' => [
                [
                    'type' => [
                        'type' => 'array',
                        'items' => 'int'
                    ]
                ],
                [
                    'type' => 'string'
                ]
            ]
        ];
        $schemaTemplate = $this->getMockForAbstractClass(SchemaTemplateInterface::class);
        $schemaTemplate->expects(self::once())->method('getSchemaDefinition')->willReturn($schemaDefinition);
        $schemaTemplate->expects(self::once())->method('withSchemaDefinition')->with($schemaDefinition);

        $merger->resolveSchemaTemplate($schemaTemplate);
    }

    public function testResolveSchemaTemplateNested()
    {
        $schemaDefinition = [
            'fields' => [
                [
                    'type' => [
                        'type' => 'array',
                        'items' => 'someType'
                    ]
                ],
                [
                    'type' => 'string'
                ]
            ]
        ];
        $expectedSchemaDefinition = [
            'fields' => [
                [
                    'type' => [
                        'type' => 'array',
                        'items' => []
                    ]
                ],
                [
                    'type' => 'string'
                ]
            ]
        ];

        $schemaTemplate = $this->getMockForAbstractClass(SchemaTemplateInterface::class);
        $schemaTemplate->expects(self::once())->method('getSchemaDefinition')->willReturn($schemaDefinition);
        $schemaTemplate->expects(self::once())->method('withSchemaDefinition')->with($expectedSchemaDefinition);

        $childSchemaTemplate =  $this->getMockForAbstractClass(SchemaTemplateInterface::class);
        $childSchemaTemplate->expects(self::once())->method('getSchemaDefinition')->willReturn(['fields' => []]);
        $childSchemaTemplate->expects(self::once())->method('withSchemaDefinition')->with(['fields' => []]);

        $schemaRegistry = $this->getMockForAbstractClass(SchemaRegistryInterface::class);
        $schemaRegistry->expects(self::once())->method('getSchemaById')->with('someType')->willReturn($childSchemaTemplate);


        $merger = SchemaMerger::create()->setSchemaRegistry($schemaRegistry);
        $merger->resolveSchemaTemplate($schemaTemplate);
    }

    public function testResolveSchemaTemplateArrayType()
    {
        $schemaDefinition = [
            'fields' => [
                [
                    'type' => ['int', 'string']
                ],
                [
                    'type' => 1
                ]
            ]
        ];

        $schemaTemplate = $this->getMockForAbstractClass(SchemaTemplateInterface::class);
        $schemaTemplate->expects(self::once())->method('getSchemaDefinition')->willReturn($schemaDefinition);
        $schemaTemplate->expects(self::once())->method('withSchemaDefinition')->with($schemaDefinition);

        $merger = SchemaMerger::create();
        $merger->resolveSchemaTemplate($schemaTemplate);
    }
}
