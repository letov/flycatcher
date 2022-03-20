<?php

namespace Letov\Flycatcher\Modules\Proxy;

interface ProxyListInterface
{
    /**
     * @param string $proxyClassName Class name implements ProxyInterface.
     * @return array Return [ProxyInterface]
     */
    public function getProxyList(string $proxyClassName): array;
}