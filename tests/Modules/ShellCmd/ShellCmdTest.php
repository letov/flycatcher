<?php

namespace Letov\Flycatcher\Tests\Modules\ShellCmd;

use DI\DependencyException;
use DI\NotFoundException as NotFoundExceptionAlias;
use Exception;
use Letov\Flycatcher\Tests\TestCaseIncludeContainer;

class ShellCmdTest extends TestCaseIncludeContainer
{
    /**
     * @throws DependencyException
     * @throws NotFoundExceptionAlias
     */
    public function testShellCmd()
    {
        $shellCmd = $this->container->make('ShellCmd',
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

    /**
     * @throws DependencyException
     * @throws NotFoundExceptionAlias
     */
    public function testShellCmdThrow()
    {
        $this->expectException(Exception::class);
        $this->container->make('ShellCmd',
            array('cmd' => 'fakeCommand')
        );
    }
}
