<?php
// autogenerated file
// DO NOT EDIT
namespace Letov\Flycatcher\Downloader\ArgsSupport;

interface ArgsSupportInterface extends ArgInterfaces\BrowserSettingsArgInterface, ArgInterfaces\CaptchaTextToImageArgInterface, ArgInterfaces\HTTPArgInterface, ArgInterfaces\PhantomJSArgInterface, ArgInterfaces\SeleniumArgInterface, ArgInterfaces\ShellArgInterface
{
	function __construct(array $args);


	function updateArgs(array $args);


	function getProxy(): ?\Letov\Flycatcher\ProxyPool\ProxyInterface;


	function getTimeout(): ?int;


	function getCookieFilePath(): ?string;


	function getDiskCachePath(): ?string;


	function getLocalStoragePath(): ?string;


	function getCaptchaApiKey(): ?string;


	function getCaptchaSign(): ?string;


	function getCaptchaImageSelector(): ?string;


	function getCaptchaInputSelector(): ?string;


	function getCaptchaFormSelector(): ?string;


	function getCaptchaSendIncorrectSolveReport(): ?bool;


	function getHeaders(): ?array;


	function getHttpMethod(): ?string;


	function getPayloadForm(): ?array;


	function getPayloadRaw(): ?string;


	function getPhantomJSConnector(): ?string;


	function getPhantomJSViewportWidth(): ?int;


	function getPhantomJSViewportHeight(): ?int;


	function getPhantomJSSnapshotSelector(): ?string;


	function getPhantomJSSnapshotPath(): ?string;


	function getPhantomJSClient(): ?\JonnyW\PhantomJs\ClientInterface;


	function getPhantomJSPath(): ?string;


	function getPhantomJSSaveContentPath(): ?string;


	function getPhantomJSSaveContentMimeFilter(): ?array;


	function getPhantomJSSaveContentWait(): ?int;


	function getPhantomJSClickSelectorMap(): ?array;


	function getPhantomJSClickSelectorMapRepeat(): ?int;


	function getOffHeadlessMode(): ?bool;


	function getShell(): ?\Letov\Flycatcher\Shell\ShellInterface;
}
