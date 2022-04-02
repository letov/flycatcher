<?php

namespace Letov\Flycatcher\Modules\Downloader\Shells;

use Letov\Flycatcher\Modules\Downloader\DownloaderInterface;

class Wget extends AbstractShellSupport implements DownloaderInterface
{

    public function downloadFile($url, $filePath)
    {
        $httpCode = (int)$this->shell
            ->addArg("--output-document", $filePath)
            ->addArg($url)
            ->addArgUnsafe("2>&1 | grep 'HTTP/' | awk '{print $2}'")
            ->run();
        $this->shell->removeFromTail(3);
        if (200 !== $httpCode)
        {
            @unlink($filePath);
        }
    }

    protected function setArgsToClient()
    {
        $this->setCookies();
        $this->setProxy();
        $this->setHeaders();
        $this->setPayload();
        $this->shell
            ->addArg("--method", $this->argsSupport->getHttpMethod())
            ->addArg("--compression=auto")
            ->addArg("--connect-timeout", $this->argsSupport->getTimeOut())
            ->addArg("--server-response");
    }

    private function setCookies()
    {
        $this->shell
            ->addArg("--load-cookies", $this->argsSupport->getCookieFilePath())
            ->addArg("--save-cookies", $this->argsSupport->getCookieFilePath())
            ->addArg("--keep-session-cookies");
    }

    private function setProxy()
    {
        if (!empty($this->argsSupport->getProxy()) && $this->argsSupport->getProxy()->getType() == 'https')
        {
            $this->shell
                ->addArg("-e", "use_proxy=yes")
                ->addArg("-e", "https_proxy={$this->argsSupport->getProxy()->getSocket()}")
                ->addArg("--proxy-user", $this->argsSupport->getProxy()->getUser())
                ->addArg("--proxy-password", $this->argsSupport->getProxy()->getPass());
        }
    }

    private function setHeaders()
    {
        if (!empty($this->argsSupport->getHeaders()))
        {
            foreach ($this->argsSupport->getHeaders() as $header => $value) {
                $this->shell->addArg("--header", "$header: $value");
            }
        }
    }

    private function setPayload()
    {
        if (!empty($this->argsSupport->getPayloadRaw()) || !empty($this->argsSupport->getPayloadForm()))
        {
            $data = $this->argsSupport->getPayloadRaw() ? $this->argsSupport->getPayloadRaw() : http_build_query($this->argsSupport->getPayloadForm());
            $this->shell->addArg("--body-data", $data);
        }
    }
}