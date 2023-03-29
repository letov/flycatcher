<?php

declare(strict_types=1);

namespace Letov\Flycatcher\Downloader\ToolSupport\Shells;

use Letov\Flycatcher\Downloader\DownloadToolInterface;

class Wget extends AbstractShellSupport implements DownloadToolInterface
{
    public function downloadFile($url, $filePath): void
    {
        $httpCode = $this->shell
            ->addArg('--output-document', $filePath)
            ->addArg($url)
            ->addArgUnsafe("2>&1 | grep 'HTTP/' | awk '{print $2}'")
            ->run()
        ;
        $this->shell->removeFromTail(3);
        if (false === stripos($httpCode, '200')) {
            @unlink($filePath);
        }
    }

    protected function setArgsToShell(): void
    {
        $this->shell = clone $this->argsSupport->getShell();
        $this->setCookies();
        $this->setProxy();
        $this->setHeaders();
        $this->setPayload();
        $this->shell
            ->addArg('--method', $this->argsSupport->getHttpMethod())
            ->addArg('--compression=auto')
            ->addArg('--connect-timeout', $this->argsSupport->getTimeout())
            ->addArg('--server-response')
        ;
    }

    private function setCookies(): void
    {
        $this->shell
            ->addArg('--load-cookies', $this->fileNameAddPid($this->argsSupport->getCookieFilePath()))
            ->addArg('--save-cookies', $this->fileNameAddPid($this->argsSupport->getCookieFilePath()))
            ->addArg('--keep-session-cookies')
        ;
    }

    private function setProxy(): void
    {
        if (!empty($this->argsSupport->getProxy()) && 'https' === $this->argsSupport->getProxy()->getType()) {
            $this->shell
                ->addArg('-e', 'use_proxy=yes')
                ->addArg('-e', "https_proxy={$this->argsSupport->getProxy()->getSocket()}")
                ->addArg('--proxy-user', $this->argsSupport->getProxy()->getUser())
                ->addArg('--proxy-password', $this->argsSupport->getProxy()->getPass())
            ;
        }
    }

    private function setHeaders(): void
    {
        if (!empty($this->argsSupport->getHeaders())) {
            foreach ($this->argsSupport->getHeaders() as $header => $value) {
                $this->shell->addArg('--header', "{$header}: {$value}");
            }
        }
    }

    private function setPayload(): void
    {
        if (!empty($this->argsSupport->getPayloadDataRaw()) || !empty($this->argsSupport->getPayloadDataArray())) {
            $data = $this->argsSupport->getPayloadDataRaw() ?: http_build_query($this->argsSupport->getPayloadDataArray());
            $this->shell->addArg('--body-data', $data);
        }
    }
}
