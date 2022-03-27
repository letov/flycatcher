<?php
// THIS IS CODEGENERATED FILE
namespace Letov\Flycatcher\Modules\Downloader\ArgsSupport;

abstract class ArgsSupportShellCmd extends ArgsSupport implements ArgInterfaces\CookieArgInterface, ArgInterfaces\HeadersArgInterface, ArgInterfaces\HttpMethodArgInterface, ArgInterfaces\PayloadArgInterface, ArgInterfaces\ProxyArgInterface, ArgInterfaces\TimeOutArgInterface
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
