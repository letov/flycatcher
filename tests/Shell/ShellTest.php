<?php

namespace Letov\Flycatcher\Tests\Shell;

use DI\DependencyException;
use DI\NotFoundException as NotFoundExceptionAlias;
use Exception;
use Letov\Flycatcher\Tests\TestCaseContainer;

class ShellTest extends TestCaseContainer
{
    /**
     * @throws DependencyException
     * @throws NotFoundExceptionAlias
     */
    public function testShell()
    {
        $shell = $this->container->make('Shell',
            array(
                'cmd' => 'echo',
                'logger' => $this->container->get('Logger')
            )
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
            array(
                'cmd' => 'fakeCommand',
                'logger' => $this->container->get('Logger')
            )
        );
    }
}
