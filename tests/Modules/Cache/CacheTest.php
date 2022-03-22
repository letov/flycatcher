<?php

namespace Letov\Flycatcher\Tests\Modules\Cache;

use DI\DependencyException;
use DI\NotFoundException as NotFoundExceptionAlias;
use Letov\Flycatcher\Modules\Cache\Cache;
use PHPUnit\Framework\TestCase;
use ReflectionException as ReflectionExceptionAlias;
use ReflectionMethod;

class CacheTest extends TestCase
{
    public Cache $cache;

    /**
     * @throws DependencyException
     * @throws NotFoundExceptionAlias
     */
    public function setUp(): void
    {
        $container = require __DIR__ . '/../../bootstrap.dev.php';
        $this->cache = $container->get('Cache');
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

    /**
     * @throws ReflectionExceptionAlias
     */
    public function testIsFileExpire() {
        $filePath = "/tmp/testIsFileExpire";
        shell_exec("touch $filePath");
        $this->assertFalse($this->reflectionMethod($this->cache, 'isFileExpire', ['filePath' => $filePath]));
        sleep(2);
        $this->assertTrue($this->reflectionMethod($this->cache, 'isFileExpire', ['filePath' => $filePath]));
        @unlink($filePath);
    }

    /**
     * @throws ReflectionExceptionAlias
     */
    public function testIsZeroSize() {
        $filePath = "/tmp/testIsZeroSize";
        shell_exec("touch $filePath");
        $this->assertTrue($this->reflectionMethod($this->cache, 'isZeroSize', ['filePath' => $filePath]));
        shell_exec("echo \"test\" > $filePath");
        $this->assertFalse($this->reflectionMethod($this->cache, 'isZeroSize', ['filePath' => $filePath]));
        @unlink($filePath);
    }

    /**
     * @throws ReflectionExceptionAlias
     */
    public function testisImageFile() {
        $filePath = "/tmp/testisImageFile.png";
        shell_exec("wget -q -O $filePath https://static.pleer.ru/i/logo.png");
        $this->assertTrue($this->reflectionMethod($this->cache, 'isImageFile', ['filePath' => $filePath]));
        shell_exec("echo \"crushImageStructure\" > $filePath");
        $this->assertFalse($this->reflectionMethod($this->cache, 'isImageFile', ['filePath' => $filePath]));
        @unlink($filePath);
    }
}
