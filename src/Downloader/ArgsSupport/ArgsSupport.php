<?php
// autogenerated file
// DO NOT EDIT
namespace Letov\Flycatcher\Downloader\ArgsSupport;

class ArgsSupport implements ArgsSupportInterface
{
	private array $argsStorage;


	public function __construct(array $args)
	{
		$this->argsStorage = $args;
	}


	public function updateArgs(array $args)
	{
		$this->argsStorage = array_merge($this->argsStorage, $args);
	}


	private function getArg(string $methodName)
	{
		$argName = substr($methodName, 3);
		return $this->argsStorage[$argName] ?? null;
	}


	public function getProxy(): ?\Letov\Flycatcher\ProxyPool\ProxyInterface
	{
		return $this->getArg(__FUNCTION__);
	}


	public function getTimeout(): ?int
	{
		return $this->getArg(__FUNCTION__);
	}


	public function getCookieFilePath(): ?string
	{
		return $this->getArg(__FUNCTION__);
	}


	public function getDiskCachePath(): ?string
	{
		return $this->getArg(__FUNCTION__);
	}


	public function getLocalStoragePath(): ?string
	{
		return $this->getArg(__FUNCTION__);
	}


	public function getCaptchaApiKey(): ?string
	{
		return $this->getArg(__FUNCTION__);
	}


	public function getCaptchaSign(): ?string
	{
		return $this->getArg(__FUNCTION__);
	}


	public function getCaptchaImageSelector(): ?string
	{
		return $this->getArg(__FUNCTION__);
	}


	public function getCaptchaInputSelector(): ?string
	{
		return $this->getArg(__FUNCTION__);
	}


	public function getCaptchaFormSelector(): ?string
	{
		return $this->getArg(__FUNCTION__);
	}


	public function getCaptchaSendIncorrectSolveReport(): ?bool
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


	public function getPayloadForm(): ?array
	{
		return $this->getArg(__FUNCTION__);
	}


	public function getPayloadRaw(): ?string
	{
		return $this->getArg(__FUNCTION__);
	}


	public function getPhantomJSConnector(): ?string
	{
		return $this->getArg(__FUNCTION__);
	}


	public function getPhantomJSViewportWidth(): ?int
	{
		return $this->getArg(__FUNCTION__);
	}


	public function getPhantomJSViewportHeight(): ?int
	{
		return $this->getArg(__FUNCTION__);
	}


	public function getPhantomJSSnapshotSelector(): ?string
	{
		return $this->getArg(__FUNCTION__);
	}


	public function getPhantomJSSnapshotPath(): ?string
	{
		return $this->getArg(__FUNCTION__);
	}


	public function getPhantomJSClient(): ?\JonnyW\PhantomJs\ClientInterface
	{
		return $this->getArg(__FUNCTION__);
	}


	public function getPhantomJSPath(): ?string
	{
		return $this->getArg(__FUNCTION__);
	}


	public function getPhantomJSPageContentPath(): ?string
	{
		return $this->getArg(__FUNCTION__);
	}


	public function getPhantomJSPageContentMimeFilter(): ?array
	{
		return $this->getArg(__FUNCTION__);
	}


	public function getPhantomJSPageContentWait(): ?int
	{
		return $this->getArg(__FUNCTION__);
	}


	public function getPhantomJSClickSelectorMap(): ?array
	{
		return $this->getArg(__FUNCTION__);
	}


	public function getPhantomJSClickSelectorMapRepeat(): ?int
	{
		return $this->getArg(__FUNCTION__);
	}


	public function getShell(): ?\Letov\Flycatcher\Shell\ShellInterface
	{
		return $this->getArg(__FUNCTION__);
	}
}
