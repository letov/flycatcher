<?php

declare(strict_types=1);

namespace Letov\Flycatcher\ProxyPool;

interface ProxyPoolInterface
{
    public function getProxyList(string $proxyType = 'socks5'): array;
}
