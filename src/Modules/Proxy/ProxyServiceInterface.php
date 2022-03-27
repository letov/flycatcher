<?php

namespace Letov\Flycatcher\Modules\Proxy;

interface ProxyServiceInterface
{
    public function getProxyList(string $proxyType = 'socks5'): array;
}