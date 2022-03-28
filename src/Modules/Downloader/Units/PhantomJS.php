<?php

namespace Letov\Flycatcher\Modules\Downloader\Units;

use Letov\Flycatcher\Modules\Downloader\DownloaderInterface;
use Letov\Flycatcher\Modules\Downloader\ShellCmdSupport\ArgsSupportShellCmd;

class PhantomJS extends ArgsSupportShellCmd
    implements DownloaderInterface
{
    public function downloadFile($url, $filePath)
    {
        $shellCmd = clone $this->shellCmd;
        $httpCode = (int)$shellCmd
            ->addArg($filePath)
            ->addArg($url)
            ->run();
        if (200 !== $httpCode) {
            @unlink($filePath);
        }
    }

    protected function setShellCmdArgs()
    {
        $this->setShellCmdProxyArgs();
        $this->shellCmd
            ->addArg("cookies-file", $this->getCookieFilePath());
    }

    private function setShellCmdProxyArgs()
    {
        if (empty($this->getProxy()))
        {
            return;
        }
        $this->shellCmd
            ->addArg("proxy-type", $this->getProxy()->getType() == 'socks5' ? 'socks5' : 'http')
            ->addArg("proxy", $this->getProxy()->getSocket())
            ->addArg("proxy-auth", $this->getProxy()->getAuth());
    }
}