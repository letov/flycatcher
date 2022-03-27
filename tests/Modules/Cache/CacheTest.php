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
     * @throws DependencyException
     * @throws NotFoundExceptionAlias
     */
    public function testisImageFile() {
        $filePath = "/tmp/testisImageFile.png";
        $imgUrl = $this->container->get('Test.urlImage');
        shell_exec("wget -q -O $filePath $imgUrl");
        $this->assertTrue($this->reflectionMethod($this->cache, 'isImageFile', ['filePath' => $filePath]));
        shell_exec("echo \"crushImageStructure\" > $filePath");
        $this->assertFalse($this->reflectionMethod($this->cache, 'isImageFile', ['filePath' => $filePath]));
        @unlink($filePath);
    }
}
