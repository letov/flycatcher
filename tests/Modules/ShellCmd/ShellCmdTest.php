<?php

namespace Letov\Flycatcher\Tests\Modules\ShellCmd;

use DI\Container;
use DI\DependencyException;
use DI\NotFoundException as NotFoundExceptionAlias;
use Exception;
use Letov\Flycatcher\Modules\ShellCmd\ShellCmd;
use PHPUnit\Framework\TestCase;

class ShellCmdTest extends TestCase
{
    public Container $container;

    /**
     * @throws DependencyException
     * @throws NotFoundExceptionAlias
     */
    public function setUp(): void
    {
        $this->container = require __DIR__ . '/../../bootstrap.dev.php';
    }

    public function testShellCmd()
    {
        $shellCmd = $this->container->make('shellCmd',
            array('cmd' => 'echo')
        );
        $this->assertSame("flycatcher",
            $shellCmd
                ->addArg("-n")
                ->addArg("flycatcher")
                ->run()
        );
        $this->assertSame("flycatcher next",
            $shellCmd
                ->addArg("next")
                ->run()
        );
    }

    public function testShellCmdThrow()
    {
        $this->expectException(Exception::class);
        new ShellCmd("fakeCommand");
    }
}
