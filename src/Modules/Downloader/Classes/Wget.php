<?php

namespace Letov\Flycatcher\Modules\Downloader\Classes;

use Exception as ExceptionAlias;
use Letov\Flycatcher\Modules\Downloader\ArgsSupport\ArgsSupportShellCmd;
use Letov\Flycatcher\Modules\Downloader\DownloaderInterface;

class Wget extends ArgsSupportShellCmd
    implements DownloaderInterface
{
    /**
     * @throws ExceptionAlias
     */
    public function downloadFile($url, $filePath)
    {
        $shellCmd = clone $this->shellCmd;
        $httpCode = (int)$shellCmd
            ->addArg("--output-document", $filePath)
            ->addArg($url)
            ->addUnsafeSuffix("2>&1 | grep 'HTTP/' | awk '{print $2}'")
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
        $proxy = $this->getProxy() && $this->getProxy()->getType() == 'https' ? $this->getProxy() : null;
        $proxySocket = $proxy ? $proxy->getSocket() : null;
        $proxyUser = $proxy ? $proxy->getUser() : null;
        $proxyPass = $proxy ? $proxy->getPass() : null;
        $this->shellCmd
            ->addArg("-e", "use_proxy=yes")
            ->addArg("-e", "https_proxy=$proxySocket")
            ->addArg("--proxy-user", $proxyUser)
            ->addArg("--proxy-password", $proxyPass);
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