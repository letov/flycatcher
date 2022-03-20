<?php

namespace Letov\Flycatcher\Tests\Modules\ShellCmd;

use Letov\Flycatcher\Modules\ShellCmd\ShellCmd;
use PHPUnit\Framework\TestCase;

class ShellCmdTest extends TestCase
{
    public function testShellCmd()
    {
        $shellCmd = new ShellCmd();
        $shellCmd->addFlag("-n")
            ->addFlag("flycatcher");
        $this->assertSame("flycatcher", $shellCmd->run("echo"));
    }
}
