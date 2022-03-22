<?php

namespace Letov\Flycatcher\Modules\Proxy;

use Exception;
use Slruslan\Proxy6\ProxyState;
use Slruslan\Proxy6\ProxyType;
use Slruslan\Proxy6\ProxyVersion;
use Slruslan\Proxy6\Wrapper;

class Proxy6Service implements ProxyServiceInterface
{

    private Wrapper $api;
    private object $response;
    private int $proxyNeededCount;

    /**
     * @throws Exception
     */
    public function __construct(string $apiKey, int $proxyNeededCount)
    {
        $this->api = new Wrapper($apiKey);
        try {
            $this->api->getBalance();
        } catch (Exception $e) {
            throw new Exception("Invalid key.");
        }
        $this->proxyNeededCount = $proxyNeededCount;
    }

    /**
     * @throws Exception
     */
    public function getProxyList(): array
    {
        $this->getActiveProxyList();
        if (!isset($this->response->list_count)) {
            throw new Exception("Cant get proxy.");
        }
        $this->checkProxyCount();
        $this->setSock5Type();
        $result = [];
        foreach ($this->response->list as $proxy) {
            $result[] = new Proxy6($proxy);
        }
        return array_slice($result, 0, $this->proxyNeededCount);
    }

    private function getActiveProxyList() {
        $this->response = $this->api->getProxy(ProxyState::ACTIVE);
    }

    /**
     * @throws Exception
     */
    private function checkProxyCount()
    {
        if ($this->response->list_count < $this->proxyNeededCount) {
            $buyCount = $this->proxyNeededCount - $this->response->list_count;
            $this->api->buy($buyCount, 30, "ru", ProxyVersion::IPV4);
            $this->getActiveProxyList();
            if ($this->response < $this->proxyNeededCount) {
                throw new Exception("Need more money in Proxy6 service.");
            }
        }
    }

    private function setSock5Type()
    {
        $keys = [];
        foreach ($this->response->list as $proxy) {
            $keys[] = $proxy->id;
        }
        $this->api->setType($keys, ProxyType::SOCKS5);
    }
}