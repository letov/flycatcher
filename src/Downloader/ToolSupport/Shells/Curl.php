<?php

namespace Letov\Flycatcher\Downloader\ToolSupport\Shells;

use Letov\Flycatcher\Downloader\DownloadToolInterface;

class Curl extends AbstractShellSupport implements DownloadToolInterface
{

    public function downloadFile($url, $filePath)
    {
        $httpCode = (int)$this->shell
            ->addArg("--output", $filePath)
            ->addArg($url)
            ->run();
        $this->shell->removeFromTail(2);
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
            ->addArg("--request", $this->argsSupport->getHttpMethod())
            ->addArg("--location")
            ->addArg("--silent")
            ->addArg("--compressed")
            ->addArg("--write-out", "%{http_code}");
    }

    private function setCookies()
    {
        $this->shell
            ->addArg("--cookie", $this->fileNameAddPid($this->argsSupport->getCookieFilePath()))
            ->addArg("--cookie-jar", $this->fileNameAddPid($this->argsSupport->getCookieFilePath()))
            ->addArg("--connect-timeout", $this->argsSupport->getTimeout());
    }

    private function setProxy()
    {
        if (!empty($this->argsSupport->getProxy()))
        {
            $proxySocket = $this->argsSupport->getProxy()->getType() . "://" . $this->argsSupport->getProxy()->getSocket();
            $proxyAuth = $this->argsSupport->getProxy()->getAuth();
            $this->shell
                ->addArg("--proxy", $proxySocket)
                ->addArg("--proxy-user", $proxyAuth);
        }
    }

    private function setHeaders()
    {
        if (!empty($this->argsSupport->getHeaders()))
        {
            foreach ($this->argsSupport->getHeaders() as $header => $value)
            {
                $this->shell->addArg("--header", "$header: $value");
            }
        }
    }

    private function setPayload()
    {
        if (!empty($this->argsSupport->getPayloadRaw()) || !empty($this->argsSupport->getPayloadForm()))
        {
            $data = $this->argsSupport->getPayloadRaw() ? $this->argsSupport->getPayloadRaw() : http_build_query($this->argsSupport->getPayloadForm());
            $this->shell->addArg("--data", $data);
        }
    }
}