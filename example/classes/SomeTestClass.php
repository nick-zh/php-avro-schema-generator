<?php

declare(strict_types=1);

namespace Test\Space;

use Test\Space\SomeOtherTestClass;

class SomeTestClass
{

    /**
     * @var string
     */
    private $string;

    /**
     * @var array|int[]
     */
    protected $array;

    /**
     * @var int
     */
    public $int;

    /**
     * @var integer
     */
    public $integer;

    /**
     * @var float
     */
    public $float;

    /**
     * @var bool
     */
    public $bool;

    /**
     * @var boolean
     */
    public $boolean;

    /**
     * @var mixed
     */
    public $mixed;

    /**
     * @var double
     */
    public $double;

    /**
     * @var object
     */
    public $object;

    /**
     * @var callable
     */
    public $callable;

    /**
     * @var resource
     */
    public $resource;

    /**
     * @var SomeOtherTestClass
     */
    public $someOtherTestClass;

    /**
     * @var array|SomeOtherTestClass[]
     */
    public $someOtherTestClasses;
}
