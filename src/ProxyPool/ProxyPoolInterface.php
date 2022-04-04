<?php

namespace Letov\Flycatcher\ProxyPool;

interface ProxyPoolInterface
{
    public function getProxyList(string $proxyType = 'socks5'): array;
}