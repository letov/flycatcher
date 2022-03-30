<?php

namespace Letov\Flycatcher\Modules\Downloader;

use Letov\Flycatcher\Modules\Downloader\ArgsSupport\AbstractArgsSupport;
use Letov\Flycatcher\Modules\ShellCmd\ShellCmdInterface;

abstract class AbstractShellCmdSupport extends AbstractArgsSupport
{
	protected ShellCmdInterface $shellCmd;

	public function __construct(array $args, ShellCmdInterface $shellCmd)
	{
        $this->setArgs($args);
		$this->shellCmd = $shellCmd;
		$this->setShellCmdArgs();
	}

    abstract protected function setShellCmdArgs();
}
