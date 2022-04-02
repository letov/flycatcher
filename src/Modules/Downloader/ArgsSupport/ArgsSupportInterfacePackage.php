<?php
// autogenerated file
// DO NOT EDIT
namespace Letov\Flycatcher\Modules\Downloader\ArgsSupport;

interface ArgsSupportInterfacePackage extends ArgInterfaces\BrowserDiskCacheArgInterface, ArgInterfaces\CaptchaTextToImageArgInterface, ArgInterfaces\HttpBaseArgInterface, ArgInterfaces\PhantomJSArgInterface, ArgInterfaces\PhantomJSPackageArgInterface, ArgInterfaces\ShellArgInterface
{
	function __construct(array $args);


	function updateArgs(array $args);


	function getDiskCachePath(): ?string;


	function getLocalStoragePath(): ?string;


	function getCaptchaApiKey(): ?string;


	function getCaptchaSign(): ?string;


	function getCaptchaImageSelector(): ?string;


	function getCaptchaInputSelector(): ?string;


	function getCaptchaFormSelector(): ?string;


	function getCaptchaSendIncorrectSolveReport(): ?bool;


	function getCookieFilePath(): ?string;


	function getHeaders(): ?array;


	function getHttpMethod(): ?string;


	function getPayloadForm(): ?array;


	function getPayloadRaw(): ?string;


	function getProxy(): ?\Letov\Flycatcher\Modules\ProxyPool\ProxyInterface;


	function getTimeOut(): ?int;


	function getPhantomJSConnector(): ?string;


	function getPhantomJSViewportWidth(): ?int;


	function getPhantomJSViewportHeight(): ?int;


	function getPhantomJSSnapshotSelector(): ?string;


	function getPhantomJSSnapshotPath(): ?string;


	function getPhantomJSClient(): ?\JonnyW\PhantomJs\ClientInterface;


	function getPhantomJSPath(): ?string;


	function getShell(): ?\Letov\Flycatcher\Modules\Shell\ShellInterface;
}
