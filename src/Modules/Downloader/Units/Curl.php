<?php

namespace Letov\Flycatcher\Modules\Downloader\Units;

use Letov\Flycatcher\Modules\Downloader\DownloaderInterface;
use Letov\Flycatcher\Modules\Downloader\ShellCmdSupport\AbstractShellCmdSupport;

class Curl extends AbstractShellCmdSupport
    implements DownloaderInterface
{

    public function downloadFile($url, $filePath)
    {
        $shellCmd = clone $this->shellCmd;
        $httpCode = (int)$shellCmd
            ->addArg("--output", $filePath)
            ->addArg($url)
            ->run();
        if (200 !== $httpCode) {
            @unlink($filePath);
        }
    }

    protected function setShellCmdArgs()
    {
        $this->setShellCmdProxyArgs();
        $this->setShellCmdHeaderArgs();
        $this->setShellCmdCookiesArgs();
        $this->shellCmd
            ->addArg("--request", $this->getHttpMethod())
            ->addArg("--data", $this->getPayload())
            ->addArg("--location")
            ->addArg("--silent")
            ->addArg("--compressed")
            ->addArg("--write-out", "%{http_code}");
    }

    private function setShellCmdCookiesArgs()
    {
        $this->shellCmd
            ->addArg("--cookie", $this->getCookieFilePath())
            ->addArg("--cookie-jar", $this->getCookieFilePath())
            ->addArg("--connect-timeout", $this->getTimeOut());
    }

    private function setShellCmdProxyArgs()
    {
        if (empty($this->getProxy()))
        {
            return;
        }
        $proxySocket = $this->getProxy()->getType() . "://" . $this->getProxy()->getSocket();
        $proxyAuth = $this->getProxy()->getAuth();
        $this->shellCmd
            ->addArg("--proxy", $proxySocket)
            ->addArg("--proxy-user", $proxyAuth);
    }

    private function setShellCmdHeaderArgs()
    {
        if (!is_null($this->getHeaders())) {
            foreach ($this->getHeaders() as $header => $value) {
                $this->shellCmd->addArg("--header", "$header: $value");
            }
        }
    }
}