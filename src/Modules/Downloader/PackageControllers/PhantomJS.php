<?php

namespace Letov\Flycatcher\Modules\Downloader\PackageControllers;

use Exception;
use JonnyW\PhantomJs\ClientInterface;
use JonnyW\PhantomJs\Http\RequestInterface;
use JonnyW\PhantomJs\Http\ResponseInterface;
use Letov\Flycatcher\Modules\Downloader\ArgsSupport\AbstractArgsSupport;
use Letov\Flycatcher\Modules\Downloader\ClientSupportInterface;
use Letov\Flycatcher\Modules\Downloader\DownloadSupportInterface;

class PhantomJS extends AbstractArgsSupport implements DownloadSupportInterface
{
    protected ClientInterface $client;
    protected RequestInterface $request;
    protected ResponseInterface $response;

    public function downloadFile($url, $filePath)
    {
        $this->request->setUrl($url);
        $this->client->send($this->request, $this->response);
        if (!empty($this->response->status) &&
            (200 == $this->response->status ||
                ('3' == substr($this->response->status, 0, 1) && $this->response->isRedirect()))) {
            file_put_contents($filePath, $this->response->content);
        }
    }

    /**
     * @throws Exception
     */
    protected function setArgsToClient()
    {
        if (empty($this->getPhantomJSClient()) || empty($this->getPhantomJSPath()))
        {
            throw new Exception("Client or bin path empty");
        }
        $this->client = $this->getPhantomJSClient();
        $this->client->getEngine()->setPath($this->getPhantomJSPath());
        $this->request = $this->client->getMessageFactory()->createRequest();
        $this->response = $this->client->getMessageFactory()->createResponse();
        $this->setOptions();
        $this->setCookies();
        $this->setProxy();
        $this->setHeader();
        $this->setPayload();
        $this->setTimeout();
    }

    private function setTimeout()
    {
        if (!empty($this->getTimeOut())) {
            $this->request->setTimeout($this->getTimeOut() * 1000);
        }
    }

    private function setOptions()
    {
        $this->client->getEngine()->addOption('--web-security=no');
        $this->client->getEngine()->addOption('--ignore-ssl-errors=true');
        $this->client->getEngine()->addOption('--ssl-protocol=any ');
    }

    private function setCookies()
    {
        if (!empty($this->getCookieFilePath()))
        {
            $this->client->getEngine()->addOption('--cookies-file="' . $this->getCookieFilePath() . '"');
        }
    }

    private function setProxy()
    {
        if (!empty($this->getProxy()))
        {
            $proxyType = $this->getProxy()->getType() == 'socks5' ? 'socks5' : 'http';
            $this->client->getEngine()->addOption('--proxy-type="' . $proxyType . '"');
            $this->client->getEngine()->addOption('--proxy="' . $this->getProxy()->getSocket() . '"');
            $this->client->getEngine()->addOption('--proxy-auth="' . $this->getProxy()->getAuth() . '"');
        }
    }

    private function setHeader()
    {
        if (!is_null($this->getHeaders())) {
            foreach ($this->getHeaders() as $header => $value) {
                $this->request->addHeader($header, $value);
            }
        }
    }

    private function setPayload()
    {
        if (!empty($this->getHttpMethod())) {
            $this->request->setMethod($this->getHttpMethod());
        }
        if (!empty($this->getPayloadForm()))
        {
            $this->request->setRequestData($this->getPayloadForm());
        }
    }
}