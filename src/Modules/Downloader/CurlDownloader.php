<?php

namespace Letov\Flycatcher\Modules\Downloader;

use DI\Container;
use DI\DependencyException;
use DI\NotFoundException;
use Letov\Flycatcher\Modules\Proxy\ProxyInterface;
use Letov\Flycatcher\Modules\ShellCmd\ShellCmd;

class CurlDownloader implements DownloaderInterface
{
    private ShellCmd $shellCmd;
    private Container $container;

    /**
     * @throws DependencyException
     * @throws NotFoundException
     */
    public function __construct(ProxyInterface $proxy, string $cookieFilePath, string $userAgent, int $timeOut)
    {
        $this->container = new Container();
        $this->shellCmd = $this->container->make(ShellCmd::class, array("cmd" => "curl"));
        $this->shellCmd->addArg("-x", "socks5://{$proxy->getSocket()}")
            ->addArg("--proxy-user", "{$proxy->getAuth()}")
            ->addArg("--cookie", $cookieFilePath)
            ->addArg("--cookie-jar", $cookieFilePath)
            ->addArg("-A", $userAgent)
            ->addArg("-L")
            ->addArg("--connect-timeout", $timeOut);
    }

    public function downloadFile($url, $filePath)
    {

    }
}