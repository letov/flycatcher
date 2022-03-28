<?php
// autogenerated file
// DO NOT EDIT
namespace Letov\Flycatcher\Modules\Downloader\ShellCmdSupport;

abstract class AbstractShellCmdSupport extends \Letov\Flycatcher\Modules\Downloader\ArgsSupport\ArgsSupport implements \Letov\Flycatcher\Modules\Downloader\ArgsSupport\ArgInterfaces\CookieArgInterface, \Letov\Flycatcher\Modules\Downloader\ArgsSupport\ArgInterfaces\HeadersArgInterface, \Letov\Flycatcher\Modules\Downloader\ArgsSupport\ArgInterfaces\HttpMethodArgInterface, \Letov\Flycatcher\Modules\Downloader\ArgsSupport\ArgInterfaces\PayloadArgInterface, \Letov\Flycatcher\Modules\Downloader\ArgsSupport\ArgInterfaces\ProxyArgInterface, \Letov\Flycatcher\Modules\Downloader\ArgsSupport\ArgInterfaces\TimeOutArgInterface
{
	abstract protected function setShellCmdArgs();
	protected \Letov\Flycatcher\Modules\ShellCmd\ShellCmdInterface $shellCmd;


	public function __construct(array $args, \Letov\Flycatcher\Modules\ShellCmd\ShellCmdInterface $shellCmd)
	{
		$this->shellCmd = $shellCmd;
		parent::__construct($args);
		$this->setShellCmdArgs();
	}


	public function getCookieFilePath(): ?string
	{
		return $this->getArg(__FUNCTION__);
	}


	public function getHeaders(): ?array
	{
		return $this->getArg(__FUNCTION__);
	}


	public function getHttpMethod(): ?string
	{
		return $this->getArg(__FUNCTION__);
	}


	public function getPayload(): ?string
	{
		return $this->getArg(__FUNCTION__);
	}


	public function getProxy(): ?\Letov\Flycatcher\Modules\Proxy\ProxyInterface
	{
		return $this->getArg(__FUNCTION__);
	}


	public function getTimeOut(): ?int
	{
		return $this->getArg(__FUNCTION__);
	}
}