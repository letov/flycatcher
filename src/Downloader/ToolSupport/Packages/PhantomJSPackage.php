<?php

namespace Letov\Flycatcher\Downloader\ToolSupport\Packages;

use Exception;
use JonnyW\PhantomJs\ClientInterface;
use JonnyW\PhantomJs\Http\RequestInterface;
use JonnyW\PhantomJs\Http\ResponseInterface;
use Letov\Flycatcher\Downloader\ArgsSupport\ArgsSupportInterface;
use Letov\Flycatcher\Downloader\DownloaderInterface;
use Letov\Flycatcher\Downloader\ToolSupport\ToolSupportInterface;
use Psr\Log\LoggerInterface;

class PhantomJSPackage implements ToolSupportInterface, DownloaderInterface
{
    protected ArgsSupportInterface $argsSupport;
    protected ClientInterface $client;
    protected RequestInterface $request;
    protected ResponseInterface $response;
    protected ?LoggerInterface $logger;

    /**
     * @throws Exception
     */
    public function __construct(ArgsSupportInterface $argsSupport, ?LoggerInterface $logger = null)
    {
        if (empty($argsSupport->getPhantomJSClient()) || empty($argsSupport->getPhantomJSPath())) {
            throw new Exception("PhantomJSClient or PhantomJSPath empty");
        }
        $this->client = $argsSupport->getPhantomJSClient();
        $this->argsSupport = $argsSupport;
        $this->logger = $logger;
        $this->setArgsToClient();
    }

    /**
     * @throws Exception
     */
    protected function setArgsToClient()
    {
        $this->client = $this->argsSupport->getPhantomJSClient();
        $this->client->getEngine()->setPath($this->argsSupport->getPhantomJSPath());
        $this->request = $this->client->getMessageFactory()->createRequest();
        $this->response = $this->client->getMessageFactory()->createResponse();
        $this->setOptions();
        $this->setCookies();
        $this->setProxy();
        $this->setHeaders();
        $this->setPayload();
        $this->setTimeout();
    }

    private function setOptions()
    {
        $this->client->getEngine()->addOption('--web-security=no');
        $this->client->getEngine()->addOption('--ignore-ssl-errors=true');
        $this->client->getEngine()->addOption('--ssl-protocol=any');
        if (!empty($this->argsSupport->getDiskCachePath())) {
            $this->client->getEngine()->addOption('--disk-cache=true');
            $this->client->getEngine()->addOption('--disk-cache-path="' . $this->argsSupport->getDiskCachePath() . '"');
        }
        if (!empty($this->argsSupport->getLocalStoragePath())) {
            $this->client->getEngine()->addOption('--local-storage-path="' . $this->argsSupport->getLocalStoragePath() . '"');
        }
    }

    private function setCookies()
    {
        if (!empty($this->argsSupport->getCookieFilePath())) {
            $this->client->getEngine()->addOption('--cookies-file="' . $this->argsSupport->getCookieFilePath() . '"');
        }
    }

    private function setProxy()
    {
        if (!empty($this->argsSupport->getProxy())) {
            $proxyType = $this->argsSupport->getProxy()->getType() == 'socks5' ? 'socks5' : 'http';
            $this->client->getEngine()->addOption('--proxy-type="' . $proxyType . '"');
            $this->client->getEngine()->addOption('--proxy="' . $this->argsSupport->getProxy()->getSocket() . '"');
            $this->client->getEngine()->addOption('--proxy-auth="' . $this->argsSupport->getProxy()->getAuth() . '"');
        }
    }

    private function setHeaders()
    {
        if (!is_null($this->argsSupport->getHeaders())) {
            foreach ($this->argsSupport->getHeaders() as $header => $value) {
                $this->request->addHeader($header, $value);
            }
        }
    }

    private function setPayload()
    {
        if (!empty($this->argsSupport->getHttpMethod())) {
            $this->request->setMethod($this->argsSupport->getHttpMethod());
        }
        if (!empty($this->argsSupport->getPayloadForm())) {
            $this->request->setRequestData($this->argsSupport->getPayloadForm());
        }
    }

    private function setTimeout()
    {
        if (!empty($this->argsSupport->getTimeout())) {
            $this->request->setTimeout($this->argsSupport->getTimeout() * 1000);
        }
    }

    public function updateArgs(array $args)
    {
        $this->argsSupport->updateArgs($args);
        $this->setArgsToClient();
    }

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
}