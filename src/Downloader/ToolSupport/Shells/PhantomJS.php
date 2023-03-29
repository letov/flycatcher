<?php

declare(strict_types=1);

namespace Letov\Flycatcher\Downloader\ToolSupport\Shells;

use Letov\Flycatcher\Downloader\DownloadToolInterface;

class PhantomJS extends AbstractShellSupport implements DownloadToolInterface
{
    public function downloadFile($url, $filePath): void
    {
        $shellDownloadFile = clone $this->shell;
        $shellDownloadFile
            ->addArg(__DIR__.'/'.$this->argsSupport->getPhantomJSConnector())
            ->addArg('--viewport-width', $this->argsSupport->getPhantomJSViewportWidth())
            ->addArg('--viewport-height', $this->argsSupport->getPhantomJSViewportHeight())
            ->addArg('--snapshot-selector', $this->argsSupport->getPhantomJSSnapshotSelector())
            ->addArg('--snapshot-path', $this->fileNameAddPid($this->argsSupport->getPhantomJSSnapshotPath()))
            ->addArg('--file-path', $filePath)
            ->addArg('--url', $url)
            ->addArg('--connect-timeout', $this->argsSupport->getTimeout())
            ->addArg('--disk-cache-path', $this->dirAddPid($this->argsSupport->getDiskCachePath()))
        ;
        $this->setClickSelectorMap($shellDownloadFile);
        $this->setPageContent($shellDownloadFile);
        $this->setHeaders($shellDownloadFile);
        $this->setPayload($shellDownloadFile);
        $this->setCaptcha($shellDownloadFile);
        $response = $shellDownloadFile->run();
        if (!empty($this->logger)) {
            $logs = explode("\n", $response);
            foreach ($logs as $log) {
                $this->logger->debug($log);
            }
        }
        if (false === stripos($response, 'SUCCESS')) {
            @unlink($filePath);
        }
    }

    protected function setArgsToShell(): void
    {
        $this->shell = clone $this->argsSupport->getShell();
        $this->setCookies();
        $this->setProxy();
        $this->shell
            ->addArg('--web-security', 'no')
            ->addArg('--ignore-ssl-errors', 'true')
            ->addArg('--ssl-protocol', 'any')
        ;
        if (!empty($this->argsSupport->getDiskCachePath())) {
            $this->shell
                ->addArg('--disk-cache', 'true')
                ->addArg('--disk-cache-path', $this->dirAddPid($this->argsSupport->getDiskCachePath()))
            ;
        }
        $this->shell->addArg('--local-storage-path', $this->dirAddPid($this->argsSupport->getLocalStoragePath()));
    }

    private function setClickSelectorMap($shellDownloadFile): void
    {
        if (!empty($this->argsSupport->getPhantomJSClickSelectorMap())) {
            foreach ($this->argsSupport->getPhantomJSClickSelectorMap() as $selector) {
                $shellDownloadFile->addArg('--clk', $selector);
            }
        }
        $shellDownloadFile->addArg('--click-map-repeat', $this->argsSupport->getPhantomJSClickSelectorMapRepeat());
    }

    private function setPageContent($shellDownloadFile): void
    {
        $pageContentMimeFilter = empty($this->argsSupport->getPhantomJSSaveContentMimeFilter()) ?
            null :
            implode(',', $this->argsSupport->getPhantomJSSaveContentMimeFilter());
        $shellDownloadFile
            ->addArg('--save-content-path', $this->argsSupport->getPhantomJSSaveContentPath())
            ->addArg('--save-content-mime-filter', $pageContentMimeFilter)
            ->addArg('--save-content-wait', $this->argsSupport->getPhantomJSSaveContentWait())
        ;
    }

    private function setHeaders($shellDownloadFile): void
    {
        if (!empty($this->argsSupport->getHeaders())) {
            foreach ($this->argsSupport->getHeaders() as $header => $value) {
                $shellDownloadFile->addArg('--header', "{$header}: {$value}");
            }
        }
    }

    private function setPayload($shellDownloadFile): void
    {
        $shellDownloadFile->addArg('--method', $this->argsSupport->getHttpMethod());
        if (!empty($this->argsSupport->getPayloadDataRaw()) || !empty($this->argsSupport->getPayloadDataArray())) {
            $data = $this->argsSupport->getPayloadDataRaw() ?: http_build_query($this->argsSupport->getPayloadDataArray());
            $shellDownloadFile->addArg('--data', $data);
        }
    }

    private function setCaptcha($shellDownloadFile): void
    {
        $shellDownloadFile
            ->addArg('--captcha-api-key', $this->argsSupport->getCaptchaApiKey())
            ->addArg('--captcha-sign', $this->argsSupport->getCaptchaSign())
            ->addArg('--captcha-image-selector', $this->argsSupport->getCaptchaImageSelector())
            ->addArg('--captcha-input-selector', $this->argsSupport->getCaptchaInputSelector())
            ->addArg('--captcha-form-selector', $this->argsSupport->getCaptchaFormSelector())
            ->addArg('--captcha-incorrect-report', $this->argsSupport->getCaptchaSendIncorrectSolveReport() ? 1 : null)
        ;
    }

    private function setCookies(): void
    {
        $this->shell->addArg('--cookies-file', $this->fileNameAddPid($this->argsSupport->getCookieFilePath()));
    }

    private function setProxy(): void
    {
        if (!empty($this->argsSupport->getProxy())) {
            $proxyType = 'socks5' === $this->argsSupport->getProxy()->getType() ? 'socks5' : 'http';
            $this->shell
                ->addArg('--proxy-type', $proxyType)
                ->addArg('--proxy', $this->argsSupport->getProxy()->getSocket())
                ->addArg('--proxy-auth', $this->argsSupport->getProxy()->getAuth());
        }
    }
}
