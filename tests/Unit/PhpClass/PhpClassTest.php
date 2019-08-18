<?php

declare(strict_types=1);

namespace NickZh\PhpAvroSchemaGenerator\Tests\Unit\PhpClass;

use NickZh\PhpAvroSchemaGenerator\PhpClass\PhpClass;
use PHPUnit\Framework\TestCase;

/**
 * @covers NickZh\PhpAvroSchemaGenerator\PhpClass\PhpClass
 */
class PhpClassTest extends TestCase
{
    public function testGetters()
    {
        $phpClass = new PhpClass('TestClass', 'Test\\Space', 'some php code', []);

        self::assertEquals('TestClass', $phpClass->getClassName());
        self::assertEquals('Test\\Space', $phpClass->getClassNamespace());
        self::assertEquals('some php code', $phpClass->getClassBody());
        self::assertEquals([], $phpClass->getClassProperties());
    }
}
