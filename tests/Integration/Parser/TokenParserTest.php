<?php

declare(strict_types=1);

namespace NickZh\PhpAvroSchemaGenerator\Tests\Integration\Parser;

use NickZh\PhpAvroSchemaGenerator\Parser\TokenParser;
use NickZh\PhpAvroSchemaGenerator\PhpClass\PhpClassPropertyInterface;
use PHPUnit\Framework\TestCase;

/**
 * @covers NickZh\PhpAvroSchemaGenerator\Parser\TokenParser
 */
class TokenParserTest extends TestCase
{
    public function testGetClassName()
    {
        $filePath = __DIR__ . '/../../../example/classes/SomeTestClass.php';
        $parser = new TokenParser(file_get_contents($filePath));
        self::assertEquals('SomeTestClass', $parser->getClassName());
        self::assertEquals('SomeTestClass', $parser->getClassName());
    }

    public function testGetNamespace()
    {
        $filePath = __DIR__ . '/../../../example/classes/SomeTestClass.php';
        $parser = new TokenParser(file_get_contents($filePath));
        self::assertEquals('NickZh\\PhpAvroSchemaGenerator\\Example', $parser->getNamespace());
        self::assertEquals('NickZh\\PhpAvroSchemaGenerator\\Example', $parser->getNamespace());
    }

    public function testGetProperties()
    {
        $filePath = __DIR__ . '/../../../example/classes/SomeTestClass.php';
        $parser = new TokenParser(file_get_contents($filePath));
        $properties = $parser->getProperties($parser->getNamespace() . '\\' . $parser->getClassName());
        self::assertCount(14, $properties);

        foreach($properties as $property) {
            self::assertInstanceOf(PhpClassPropertyInterface::class, $property);
        }
    }
}
