<?php

declare(strict_types=1);

namespace Letov\Flycatcher\Tests\Shell;

use DI\DependencyException;
use DI\NotFoundException as NotFoundExceptionAlias;
use Letov\Flycatcher\Tests\TestCaseContainer;

/**
 * @internal
 *
 * @coversNothing
 */
final class ShellTest extends TestCaseContainer
{
    /**
     * @throws DependencyException
     * @throws NotFoundExceptionAlias
     */
    public function testShell(): void
    {
        $shell = $this->container->make(
            'Shell',
            [
                'cmd' => 'echo',
                'logger' => $this->container->get('Logger'),
            ]
        );
        static::assertSame(
            'flycatcher',
            $shell
                ->addArg('-n')
                ->addArg('flycatcher')
                ->run()
        );
        static::assertSame(
            'flycatcher next',
            $shell
                ->addArg('next')
                ->run()
        );
    }

    /**
     * @throws DependencyException
     * @throws NotFoundExceptionAlias
     */
    public function testWrongsCmd(): void
    {
        $this->expectException(\Exception::class);
        $this->container->make(
            'Shell',
            [
                'cmd' => 'fakeCommand',
                'logger' => $this->container->get('Logger'),
            ]
        );
    }
}
