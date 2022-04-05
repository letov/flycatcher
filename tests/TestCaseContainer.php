<?php

namespace Letov\Flycatcher\Tests;

use DI\Container;
use DI\DependencyException;
use DI\NotFoundException as NotFoundExceptionAlias;
use Exception;
use PHPUnit\Framework\TestCase;
use ReflectionException as ReflectionExceptionAlias;
use ReflectionMethod;

class TestCaseContainer extends TestCase
{
    protected Container $container;
    protected string $tmpFile;
    protected string $tmpCookie;

    /**
     * @throws DependencyException
     * @throws NotFoundExceptionAlias
     */
    public function setUp(): void
    {
        $this->container = require 'app.dev/bootstrap.dev.php';
        $dirs = $this->container->get('Dirs');
        $rnd = $this->generateRandomString();
        $this->tmpFile = "{$dirs['tests']}/download_$rnd";
        $this->tmpCookie = "{$dirs['browsersData']}/cookie_$rnd";
    }

    function generateRandomString($length = 20)
    {
        return substr(str_shuffle(str_repeat($x='0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ',
            ceil($length/strlen($x)) )),1,$length);
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