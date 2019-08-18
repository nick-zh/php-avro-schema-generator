<?php

declare(strict_types=1);

namespace NickZh\PhpAvroSchemaGenerator\Tests\Unit\Schema;

use NickZh\PhpAvroSchemaGenerator\Schema\SchemaTemplate;
use NickZh\PhpAvroSchemaGenerator\Schema\SchemaTemplateInterface;
use PHPUnit\Framework\TestCase;

/**
 * @covers NickZh\PhpAvroSchemaGenerator\Schema\SchemaTemplate
 */
class SchemaTemplateTest extends TestCase
{
    public function testSchemaId()
    {
        $template = (new SchemaTemplate())->withSchemaId('test');

        self::assertInstanceOf(SchemaTemplateInterface::class, $template);
        self::assertEquals('test', $template->getSchemaId());
    }

    public function testSchemaLevel()
    {
        $template = (new SchemaTemplate())->withSchemaLevel('root');

        self::assertInstanceOf(SchemaTemplateInterface::class, $template);
        self::assertEquals('root', $template->getSchemaLevel());
    }

    public function testSchemaDefinition()
    {
        $template = (new SchemaTemplate())->withSchemaDefinition('test');

        self::assertInstanceOf(SchemaTemplateInterface::class, $template);
        self::assertEquals('test', $template->getSchemaDefinition());
    }
}
