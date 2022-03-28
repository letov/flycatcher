<?php

namespace Letov\Flycatcher\Modules\Proxy\Proxy6;

use Exception;
use Letov\Flycatcher\Modules\Proxy\ProxyServiceInterface;
use Slruslan\Proxy6\ProxyState;
use Slruslan\Proxy6\ProxyType;
use Slruslan\Proxy6\ProxyVersion;
use Slruslan\Proxy6\Wrapper;

class ProxyServiceProxy6 implements ProxyServiceInterface
{

    private Wrapper $api;
    private object $response;
    private int $minCount;
    private bool $throwIfLessMinCount;
    private int $httpsCount;

    /**
     * @throws Exception
     */
    public function __construct(string $apiKey, int $minCount, bool $throwIfLessMinCount = false, int $httpsCount = 0)
    {
        $this->minCount = $minCount;
        $this->throwIfLessMinCount = $throwIfLessMinCount;
        $this->httpsCount = $httpsCount;
        $this->api = new Wrapper($apiKey);
        $this->validateResponse();
        $this->checkProxyCount();
        $this->setProxyType();
    }

    /**
     * @throws Exception
     */
    private function validateResponse() {
        try {
            $this->api->getBalance();
        } catch (Exception $e) {
            throw new Exception("Invalid key");
        }
        $this->updateResponse();
        if (!isset($this->response->list_count)) {
            throw new Exception("Cant get proxy");
        }
    }

    /**
     * @throws Exception
     */
    public function getProxyList(string $proxyType = 'socks5'): array
    {
        $result = [];
        foreach ($this->response->list as $proxy) {
            if (('socks5' == $proxyType  && ProxyType::SOCKS5 == $proxy->type) ||
                ('https' == $proxyType && ProxyType::HTTPS == $proxy->type)){
                $result[] = new ProxyProxy6($proxy);
            }
        }
        return $result;
    }

    private function updateResponse() {
        $this->response = $this->api->getProxy(ProxyState::ACTIVE);
    }

    /**
     * @throws Exception
     */
    private function checkProxyCount()
    {
        if ($this->response->list_count < $this->minCount) {
            $buyCount = $this->minCount - $this->response->list_count;
            $this->api->buy($buyCount, 30, "ru", ProxyVersion::IPV4);
            $this->updateResponse();
            if (true === $this->throwIfLessMinCount && $this->response < $this->minCount) {
                throw new Exception("Need more money in ProxyProxy6 service");
            }
        }
    }

    private function setProxyType()
    {
        $keysSocks = [];
        $keysHttps = [];
        $httpsCount = $this->httpsCount;
        foreach ($this->response->list as $proxy) {
            if ($httpsCount > 0) {
                $keysHttps[] = $proxy->id;
                $httpsCount--;
            } else {
                $keysSocks[] = $proxy->id;
            }
        }
        $this->api->setType($keysSocks, ProxyType::SOCKS5);
        $this->api->setType($keysHttps, ProxyType::HTTPS);
        $this->updateResponse();
    }
}