<?php

declare(strict_types=1);

namespace Letov\Flycatcher\Tests\Cache;

use DI\DependencyException;
use DI\NotFoundException;
use Letov\Flycatcher\Cache\Cache;
use Letov\Flycatcher\Tests\TestCaseContainer;
use ReflectionException as ReflectionExceptionAlias;

/**
 * @internal
 *
 * @coversNothing
 */
final class CacheTest extends TestCaseContainer
{
    public Cache $cache;

    protected function setUp(): void
    {
        parent::setUp();
        $this->cache = $this->container->get('Cache');
    }

    /**
     * @throws ReflectionExceptionAlias
     */
    public function testIsZeroSize(): void
    {
        shell_exec("touch {$this->tmpFile}");
        static::assertTrue($this->reflectionMethod($this->cache, 'isZeroSize', ['filePath' => $this->tmpFile]));
        shell_exec("echo \"test\" > {$this->tmpFile}");
        static::assertFalse($this->reflectionMethod($this->cache, 'isZeroSize', ['filePath' => $this->tmpFile]));
    }

    /**
     * @throws ReflectionExceptionAlias
     * @throws DependencyException
     * @throws NotFoundException
     */
    public function testisImageFile(): void
    {
        $imgUrl = $this->container->get('Test.urlImage');
        shell_exec("wget -q -O {$this->tmpFile} {$imgUrl}");
        static::assertTrue($this->reflectionMethod($this->cache, 'isImageFile', ['filePath' => $this->tmpFile]));
        shell_exec("echo \"crushImageStructure\" > {$this->tmpFile}");
        static::assertFalse($this->reflectionMethod($this->cache, 'isImageFile', ['filePath' => $this->tmpFile]));
    }
}
