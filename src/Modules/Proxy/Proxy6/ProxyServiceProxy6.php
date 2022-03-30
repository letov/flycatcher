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
        $this->response = json_decode('{"status":"yes","user_id":"388103","balance":"4.00","currency":"RUB","date_mod":"2022-03-17 19:23:57","list_count":3,"list":{"14148420":{"id":"14148420","version":"4","ip":"193.7.197.167","host":"193.7.197.167","port":"8000","user":"2CNLLk","pass":"dfqFcs","type":"http","country":"ru","date":"2022-03-16 22:17:03","date_end":"2022-04-15 22:17:03","unixtime":1647458223,"unixtime_end":1650050223,"descr":"","active":"1"},"14144852":{"id":"14144852","version":"4","ip":"176.124.46.204","host":"176.124.46.204","port":"8000","user":"mAEpmJ","pass":"vjL8Tm","type":"socks","country":"ru","date":"2022-03-16 17:06:42","date_end":"2022-04-15 17:06:42","unixtime":1647439602,"unixtime_end":1650031602,"descr":"","active":"1"},"14162351":{"id":"14162351","version":"4","ip":"88.218.72.205","host":"88.218.72.205","port":"8000","user":"KQNoyH","pass":"VTJ4c4","type":"socks","country":"ru","date":"2022-03-17 19:23:57","date_end":"2022-04-16 19:23:57","unixtime":1647534237,"unixtime_end":1650126237,"descr":"","active":"1"}}}');
        return;
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