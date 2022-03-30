<?php

namespace Letov\Flycatcher\Modules\Downloader\ShellCmdControllers;

use Exception;
use Exception as ExceptionAlias;
use Letov\Flycatcher\Modules\Downloader\ArgsSupport\AbstractArgsSupport;
use Letov\Flycatcher\Modules\Downloader\DownloadSupportInterface;
use Letov\Flycatcher\Modules\ShellCmd\ShellCmdInterface;

class Wget extends AbstractArgsSupport
    implements DownloadSupportInterface
{
    private ShellCmdInterface $shellCmd;

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

    /**
     * @throws ExceptionAlias
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
            ->addArg("--method", $this->getHttpMethod())
            ->addArg("--compression=auto")
            ->addArg("--connect-timeout", $this->getTimeOut())
            ->addArg("--server-response");
    }

    private function setCookies()
    {
        $this->shellCmd
            ->addArg("--load-cookies", $this->getCookieFilePath())
            ->addArg("--save-cookies", $this->getCookieFilePath())
            ->addArg("--keep-session-cookies");
    }

    private function setProxy()
    {
        if (!empty($this->getProxy()) && $this->getProxy()->getType() == 'https')
        {
            $this->shellCmd
                ->addArg("-e", "use_proxy=yes")
                ->addArg("-e", "https_proxy={$this->getProxy()->getSocket()}")
                ->addArg("--proxy-user", $this->getProxy()->getUser())
                ->addArg("--proxy-password", $this->getProxy()->getPass());
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
            $this->shellCmd->addArg("--body-data", $data);
        }
    }
}