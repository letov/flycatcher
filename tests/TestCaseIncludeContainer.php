<?php

namespace Letov\Flycatcher\Tests;

use DI\Container;
use DI\DependencyException;
use DI\NotFoundException as NotFoundExceptionAlias;
use PHPUnit\Framework\TestCase;
use ReflectionException as ReflectionExceptionAlias;
use ReflectionMethod;

class TestCaseIncludeContainer extends TestCase
{
    protected Container $container;

    /**
     * @throws DependencyException
     * @throws NotFoundExceptionAlias
     */
    public function setUp(): void
    {
        $this->container = require 'bootstrap.dev.php';
    }

    /**
     * @throws ReflectionExceptionAlias
     */
    public function reflectionMethod($class, $methodName, $args)
    {
        $method = new ReflectionMethod(get_class($class), $methodName);
        $method->setAccessible(true);
        return count($args) > 0 ?
            $method->invokeArgs($class, $args) :
            $method->invoke($class);
    }
}