<?php

namespace Letov\Flycatcher\Modules\ProxyPool\Proxy6;

use Exception;
use Letov\Flycatcher\Modules\ProxyPool\ProxyPoolInterface;
use Slruslan\Proxy6\ProxyState;
use Slruslan\Proxy6\ProxyType;
use Slruslan\Proxy6\ProxyVersion;
use Slruslan\Proxy6\Wrapper;

class ProxyPoolProxy6 implements ProxyPoolInterface
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
        try {
            $this->makeResponse();
            $this->setProxyType();
        } catch (Exception $e) {
            throw new Exception('Response error');
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

    /**
     * @throws Exception
     */
    private function makeResponse()
    {
        $this->response = $this->api->getProxy(ProxyState::ACTIVE);
        if ($this->response->list_count < $this->minCount) {
            $buyCount = $this->minCount - $this->response->list_count;
            $this->api->buy($buyCount, 30, "ru", ProxyVersion::IPV4);
            $this->response = $this->api->getProxy(ProxyState::ACTIVE);
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
        $this->response = $this->api->getProxy(ProxyState::ACTIVE);
    }
}