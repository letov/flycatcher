<?php

namespace Letov\Flycatcher\Modules\Downloader\ShellCmdControllers;

use Exception;
use Letov\Flycatcher\Modules\Downloader\ArgsSupport\AbstractArgsSupport;
use Letov\Flycatcher\Modules\Downloader\DownloadSupportInterface;
use Letov\Flycatcher\Modules\ShellCmd\ShellCmdInterface;

class Curl extends AbstractArgsSupport
    implements DownloadSupportInterface
{

    private ShellCmdInterface $shellCmd;

    public function downloadFile($url, $filePath)
    {
        $httpCode = (int)$this->shellCmd
            ->addArg("--output", $filePath)
            ->addArg($url)
            ->run();
        $this->shellCmd->removeFromTail(2);
        if (200 !== $httpCode) {
            @unlink($filePath);
        }
    }

    /**
     * @throws Exception
     */
    protected function setArgsToClient()
    {
        if (isset($this->shellCmd))
        {
            $this->shellCmd->removeAll();
        }
        if (empty($this->getShellCmd()))
        {
            throw new Exception("Shell command client empty");
        }
        $this->shellCmd = $this->getShellCmd();
        $this->setCookies();
        $this->setProxy();
        $this->setHeader();
        $this->setPayload();
        $this->shellCmd
            ->addArg("--request", $this->getHttpMethod())
            ->addArg("--location")
            ->addArg("--silent")
            ->addArg("--compressed")
            ->addArg("--write-out", "%{http_code}");
    }

    private function setCookies()
    {
        $this->shellCmd
            ->addArg("--cookie", $this->getCookieFilePath())
            ->addArg("--cookie-jar", $this->getCookieFilePath())
            ->addArg("--connect-timeout", $this->getTimeOut());
    }

    private function setProxy()
    {
        if (!empty($this->getProxy()))
        {
            $proxySocket = $this->getProxy()->getType() . "://" . $this->getProxy()->getSocket();
            $proxyAuth = $this->getProxy()->getAuth();
            $this->shellCmd
                ->addArg("--proxy", $proxySocket)
                ->addArg("--proxy-user", $proxyAuth);
        }
    }

    private function setHeader()
    {
        if (!is_null($this->getHeaders())) {
            foreach ($this->getHeaders() as $header => $value) {
                $this->shellCmd->addArg("--header", "$header: $value");
            }
        }
    }

    private function setPayload()
    {
        if (!empty($this->getPayloadRaw()) || !empty($this->getPayloadForm()))
        {
            $data = $this->getPayloadRaw() ? $this->getPayloadRaw() : http_build_query($this->getPayloadForm());
            $this->shellCmd->addArg("--data", $data);
        }
    }
}