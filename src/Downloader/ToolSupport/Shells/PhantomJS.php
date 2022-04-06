<?php

namespace Letov\Flycatcher\Downloader\ToolSupport\Shells;

use Letov\Flycatcher\Downloader\DownloadToolInterface;

class PhantomJS extends AbstractShellSupport implements DownloadToolInterface
{
    public function downloadFile($url, $filePath)
    {
        $shell = clone $this->shell;
        $shell
            ->addArg(__DIR__ . "/" . $this->argsSupport->getPhantomJSConnector())
            ->addArg("--viewport-width", $this->argsSupport->getPhantomJSViewportWidth())
            ->addArg("--viewport-height", $this->argsSupport->getPhantomJSViewportHeight())
            ->addArg("--snapshot-selector", $this->argsSupport->getPhantomJSSnapshotSelector())
            ->addArg("--snapshot-path", $this->fileNameAddPid($this->argsSupport->getPhantomJSSnapshotPath()))
            ->addArg("--file-path", $filePath)
            ->addArg("--url", $url)
            ->addArg("--connect-timeout", $this->argsSupport->getTimeout());
        $this->setHeaders($shell);
        $this->setPayload($shell);
        $this->setCaptcha($shell);
        $response = $shell->run();
        echo $response;
        if (false === stripos($response, 'SUCCESS'))
        {
            @unlink($filePath);
        }
    }

    protected function setArgsToClient()
    {
        $this->shell = $this->argsSupport->getShell();
        $this->setCookies();
        $this->setProxy();
        $this->shell
            ->addArg("--web-security", "no")
            ->addArg("--ignore-ssl-errors", "true")
            ->addArg("--ssl-protocol", "any");
        if (!empty($this->argsSupport->getDiskCachePath()))
        {
            $this->shell
                ->addArg("--disk-cache", "true")
                ->addArg("--disk-cache-path", $this->dirAddPid($this->argsSupport->getDiskCachePath()));
        }
        $this->shell->addArg("--local-storage-path", $this->dirAddPid($this->argsSupport->getLocalStoragePath()));
    }

    private function setCookies()
    {
        $this->shell->addArg("--cookies-file", $this->fileNameAddPid($this->argsSupport->getCookieFilePath()));
    }

    private function setProxy()
    {
        if (!empty($this->argsSupport->getProxy()))
        {
            $proxyType = $this->argsSupport->getProxy()->getType() == 'socks5' ? 'socks5' : 'http';
            $this->shell
                ->addArg("--proxy-type", $proxyType )
                ->addArg("--proxy",  $this->argsSupport->getProxy()->getSocket() )
                ->addArg("--proxy-auth", $this->argsSupport->getProxy()->getAuth());
        }
    }

    private function setHeaders($shell)
    {
        if (!empty($this->argsSupport->getHeaders()))
        {
            foreach ($this->argsSupport->getHeaders() as $header => $value) {
                $shell->addArg("--header", "$header: $value");
            }
        }
    }

    private function setPayload($shell)
    {
        $shell->addArg("--method", $this->argsSupport->getHttpMethod());
        if (!empty($this->argsSupport->getPayloadRaw()) || !empty($this->argsSupport->getPayloadForm()))
        {
            $data = $this->argsSupport->getPayloadRaw() ? $this->argsSupport->getPayloadRaw() : http_build_query($this->argsSupport->getPayloadForm());
            $shell->addArg("--data", $data);
        }
    }

    private function setCaptcha($shell)
    {
        $shell
            ->addArg("--captcha-api-key", $this->argsSupport->getCaptchaApiKey())
            ->addArg("--captcha-sign", $this->argsSupport->getCaptchaSign())
            ->addArg("--captcha-image-selector", $this->argsSupport->getCaptchaImageSelector())
            ->addArg("--captcha-input-selector", $this->argsSupport->getCaptchaInputSelector())
            ->addArg("--captcha-form-selector", $this->argsSupport->getCaptchaFormSelector())
            ->addArg("--captcha-incorrect-report", $this->argsSupport->getCaptchaSendIncorrectSolveReport() ? 1 : null);
    }
}