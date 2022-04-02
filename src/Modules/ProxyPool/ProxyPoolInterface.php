<?php

namespace Letov\Flycatcher\Modules\ProxyPool;

interface ProxyPoolInterface
{
    public function getProxyList(string $proxyType = 'socks5'): array;
}