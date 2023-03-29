<?php

declare(strict_types=1);

namespace Letov\Flycatcher\Downloader\ToolSupport\Packages;

use Facebook\WebDriver\Firefox\FirefoxDriver;
use Facebook\WebDriver\Firefox\FirefoxOptions;
use Facebook\WebDriver\Remote\DesiredCapabilities;
use Facebook\WebDriver\Remote\WebDriverCapabilityType;
use Letov\Flycatcher\Downloader\ArgsSupport\ArgsSupportInterface;
use Letov\Flycatcher\Downloader\BrowserEmulationInterface;
use Letov\Flycatcher\Downloader\DownloaderInterface;
use Letov\Flycatcher\Downloader\ToolSupport\ToolSupportInterface;
use Psr\Log\LoggerInterface;

class SeleniumFirefox implements ToolSupportInterface, DownloaderInterface, BrowserEmulationInterface
{
    protected FirefoxDriver $driver;
    protected ArgsSupportInterface $argsSupport;
    protected ?LoggerInterface $logger;

    public function __construct(ArgsSupportInterface $argsSupport, ?LoggerInterface $logger = null)
    {
        $this->argsSupport = $argsSupport;
        $firefoxOptions = new FirefoxOptions();
        if (true !== $argsSupport->getOffHeadlessMode()) {
            $firefoxOptions->addArguments(['-headless']);
        }
        $capabilities = DesiredCapabilities::firefox();
        $capabilities->setCapability(FirefoxOptions::CAPABILITY, $firefoxOptions);
        if (!empty($this->argsSupport->getProxy())) {
            $proxy = $this->argsSupport->getProxy();
            $capabilities->setCapability(
                WebDriverCapabilityType::PROXY,
                [
                    'socksProxy' => $proxy->getSocket(),
                    'socksVersion' => 5,
                ]
            );
        }
        $this->driver = FirefoxDriver::start($capabilities);
        if (!empty($this->argsSupport->getTimeout())) {
            $this->driver->manage()->timeouts()->pageLoadTimeout($this->argsSupport->getTimeout());
        }
        $this->logger = $logger;
        $this->setArgsToClient();
    }

    public function makeAction(callable $function): void
    {
        $function($this->driver);
    }

    public function closeBrowser(): void
    {
        $this->driver->close();
    }

    /**
     * @param mixed $url
     * @param mixed $filePath
     *
     * @throws \Exception
     */
    public function downloadFile($url, $filePath): void
    {
        try {
            $this->driver->get($url);
            $beforeDownloadCall = $this->argsSupport->getBeforeDownloadCall();
            if (!empty($beforeDownloadCall)) {
                $beforeDownloadCall($this->driver);
            }
            $source = $this->driver->getPageSource();
            @file_put_contents($filePath, $source);
            $this->logger->debug($url.'   ->   '.$filePath);
        } catch (\Exception $e) {
            $this->closeBrowser();

            throw new \Exception($e->getMessage());
        }
    }

    public function updateArgs(array $args): void
    {
        $this->argsSupport->updateArgs($args);
        $this->setArgsToClient();
    }

    private function setArgsToClient(): void
    {
    }
}
