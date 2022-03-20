<?php

namespace Letov\Flycatcher\Modules\Proxy;

interface ProxyListInterface
{
    public function getProxyList(string $proxyClassName): array;
}