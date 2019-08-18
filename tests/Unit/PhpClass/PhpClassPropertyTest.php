<?php

declare(strict_types=1);

namespace NickZh\PhpAvroSchemaGenerator\Tests\Unit\PhpClass;

use NickZh\PhpAvroSchemaGenerator\PhpClass\PhpClassProperty;
use PHPUnit\Framework\TestCase;

/**
 * @covers NickZh\PhpAvroSchemaGenerator\PhpClass\PhpClassProperty
 */
class PhpClassPropertyTest extends TestCase
{
    public function testGetters()
    {
        $property = new PhpClassProperty('propertyName', 'array', 'string');

        self::assertEquals('propertyName', $property->getPropertyName());
        self::assertEquals('array', $property->getPropertyType());
        self::assertEquals('string', $property->getPropertyArrayType());
    }
}
