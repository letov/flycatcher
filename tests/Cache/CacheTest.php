<?php

namespace Letov\Flycatcher\Tests\Cache;

use DI\DependencyException;
use DI\NotFoundException;
use Letov\Flycatcher\Cache\Cache;
use Letov\Flycatcher\Tests\TestCaseContainer;
use ReflectionException as ReflectionExceptionAlias;

class CacheTest extends TestCaseContainer
{
    public Cache $cache;

    public function setUp(): void
    {
        parent::setUp();
        $this->cache = $this->container->get('Cache');
    }

    /**
     * @throws ReflectionExceptionAlias
     */
    /*public function testIsFileExpire() {
        shell_exec("touch $this->tmpFile");
        $this->assertFalse($this->reflectionMethod($this->cache, 'isFileExpire', ['filePath' => $this->tmpFile]));
        sleep(2);
        $this->assertTrue($this->reflectionMethod($this->cache, 'isFileExpire', ['filePath' => $this->tmpFile]));
    }*/

    /**
     * @throws ReflectionExceptionAlias
     */
    public function testIsZeroSize()
    {
        shell_exec("touch $this->tmpFile");
        $this->assertTrue($this->reflectionMethod($this->cache, 'isZeroSize', ['filePath' => $this->tmpFile]));
        shell_exec("echo \"test\" > $this->tmpFile");
        $this->assertFalse($this->reflectionMethod($this->cache, 'isZeroSize', ['filePath' => $this->tmpFile]));
    }

    /**
     * @throws ReflectionExceptionAlias
     * @throws DependencyException
     * @throws NotFoundException
     */
    public function testisImageFile()
    {
        $imgUrl = $this->container->get('Test.urlImage');
        shell_exec("wget -q -O $this->tmpFile $imgUrl");
        $this->assertTrue($this->reflectionMethod($this->cache, 'isImageFile', ['filePath' => $this->tmpFile]));
        shell_exec("echo \"crushImageStructure\" > $this->tmpFile");
        $this->assertFalse($this->reflectionMethod($this->cache, 'isImageFile', ['filePath' => $this->tmpFile]));
    }
}
