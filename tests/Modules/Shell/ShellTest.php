<?php

namespace Letov\Flycatcher\Tests\Modules\Shell;

use DI\DependencyException;
use DI\NotFoundException as NotFoundExceptionAlias;
use Exception;
use Letov\Flycatcher\Tests\TestCaseIncludeContainer;

class ShellTest extends TestCaseIncludeContainer
{
    /**
     * @throws DependencyException
     * @throws NotFoundExceptionAlias
     */
    public function testshell()
    {
        $shell = $this->container->make('Shell',
            array('cmd' => 'echo')
        );
        $this->assertSame("flycatcher",
            $shell
                ->addArg("-n")
                ->addArg("flycatcher")
                ->run()
        );
        $this->assertSame("flycatcher next",
            $shell
                ->addArg("next")
                ->run()
        );
    }

    /**
     * @throws DependencyException
     * @throws NotFoundExceptionAlias
     */
    public function testWrongsCmd()
    {
        $this->expectException(Exception::class);
        $this->container->make('Shell',
            array('cmd' => 'fakeCommand')
        );
    }
}
