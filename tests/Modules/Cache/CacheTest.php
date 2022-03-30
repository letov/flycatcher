<?php

namespace Letov\Flycatcher\Tests\Modules\Cache;

use DI\DependencyException;
use DI\NotFoundException as NotFoundExceptionAlias;
use Letov\Flycatcher\Modules\Cache\Cache;
use Letov\Flycatcher\Tests\TestCaseIncludeContainer;
use ReflectionException as ReflectionExceptionAlias;

class CacheTest extends TestCaseIncludeContainer
{
    public Cache $cache;

    /**
     * @throws DependencyException
     * @throws NotFoundExceptionAlias
     */
    public function setUp(): void
    {
        parent::setUp();
        $this->cache = $this->container->get('Cache');
    }

    /**
     * @throws ReflectionExceptionAlias
     */
    public function testIsFileExpire() {
        shell_exec("touch $this->tmpFile");
        $this->assertFalse($this->reflectionMethod($this->cache, 'isFileExpire', ['filePath' => $this->tmpFile]));
        sleep(2);
        $this->assertTrue($this->reflectionMethod($this->cache, 'isFileExpire', ['filePath' => $this->tmpFile]));
    }

    /**
     * @throws ReflectionExceptionAlias
     */
    public function testIsZeroSize() {
        shell_exec("touch $this->tmpFile");
        $this->assertTrue($this->reflectionMethod($this->cache, 'isZeroSize', ['filePath' => $this->tmpFile]));
        shell_exec("echo \"test\" > $this->tmpFile");
        $this->assertFalse($this->reflectionMethod($this->cache, 'isZeroSize', ['filePath' => $this->tmpFile]));
    }

    /**
     * @throws ReflectionExceptionAlias
     * @throws DependencyException
     * @throws NotFoundExceptionAlias
     */
    public function testisImageFile() {
        $imgUrl = $this->container->get('Test.urlImage');
        shell_exec("wget -q -O $this->tmpFile $imgUrl");
        $this->assertTrue($this->reflectionMethod($this->cache, 'isImageFile', ['filePath' => $this->tmpFile]));
        shell_exec("echo \"crushImageStructure\" > $this->tmpFile");
        $this->assertFalse($this->reflectionMethod($this->cache, 'isImageFile', ['filePath' => $this->tmpFile]));
    }
}
