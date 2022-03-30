<?php

namespace Letov\Flycatcher\Modules\Downloader\Controllers;

use Exception as ExceptionAlias;
use Letov\Flycatcher\Modules\Downloader\AbstractShellCmdSupport;
use Letov\Flycatcher\Modules\Downloader\DownloaderInterface;

class Wget extends AbstractShellCmdSupport
    implements DownloaderInterface
{
    /**
     * @throws ExceptionAlias
     */
    public function downloadFile($url, $filePath)
    {
        $httpCode = (int)$this->shellCmd
            ->addArg("--output-document", $filePath)
            ->addArg($url)
            ->addArgUnsafe("2>&1 | grep 'HTTP/' | awk '{print $2}'")
            ->run();
        $this->shellCmd->removeFromTail(3);
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
            ->addArg("--method", $this->getHttpMethod())
            ->addArg("--body-data", $this->getPayload())
            ->addArg("--compression=auto")
            ->addArg("--connect-timeout", $this->getTimeOut())
            ->addArg("--server-response");
    }

    private function setShellCmdCookiesArgs()
    {
        $this->shellCmd
            ->addArg("--load-cookies", $this->getCookieFilePath())
            ->addArg("--save-cookies", $this->getCookieFilePath())
            ->addArg("--keep-session-cookies");
    }

    private function setShellCmdProxyArgs()
    {
        if (empty($this->getProxy()) || $this->getProxy()->getType() != 'https')
        {
            return;
        }
        $this->shellCmd
            ->addArg("-e", "use_proxy=yes")
            ->addArg("-e", "https_proxy={$this->getProxy()->getSocket()}")
            ->addArg("--proxy-user", $this->getProxy()->getUser())
            ->addArg("--proxy-password", $this->getProxy()->getPass());
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