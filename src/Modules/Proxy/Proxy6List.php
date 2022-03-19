<?php

namespace Letov\Flycatcher\Modules\Proxy;

use Exception;
use Slruslan\Proxy6\ProxyState;
use Slruslan\Proxy6\ProxyType;
use Slruslan\Proxy6\ProxyVersion;
use Slruslan\Proxy6\Wrapper;

class Proxy6List extends AbstractProxyList
{
    private Wrapper $api;
    private object $list;
    private int $proxyNeededCount;

    /**
     * @param $apiKey
     * @param $proxyNeededCount
     * @throws Exception
     */
    public function __construct($apiKey, $proxyNeededCount)
    {
        $this->api = new Wrapper($apiKey);
        try {
            $this->api->getBalance();
        } catch (Exception $e) {
            throw new Exception('Invalid key');
        }
        $this->proxyNeededCount = $proxyNeededCount;
    }

    /**
     * @throws Exception
     */
    public function getProxyList(): array
    {
        $this->list = $this->api->getProxy(ProxyState::ACTIVE);
        if (!isset($this->list->list_count)) {
            throw new Exception('Cant get proxy');
        }
        $this->checkProxyCount();
        $this->setType();
        $result = [Proxy::class];
        foreach ($this->list as $proxy) {
            $result[] = new Proxy($proxy->ip, $proxy->port, $proxy->user, $proxy->pass);
        }
        return $result;
    }

    /**
     * @throws Exception
     */
    private function checkProxyCount()
    {
        if ($this->list < $this->proxyNeededCount) {
            $buyCount = $this->proxyNeededCount - $this->list->list_count;
            $this->api->buy($buyCount, 30, 'ru', ProxyVersion::IPV4);
            $this->getProxyList();
            if ($this->list < $this->proxyNeededCount) {
                throw new Exception('Need more money in Proxy6 service');
            }
        }
    }

    private function setType()
    {
        $keys = [];
        foreach ($this->list as $proxy) {
            $keys[] = $proxy->id;
        }
        $this->api->setType($keys, ProxyType::SOCKS5);
    }
}