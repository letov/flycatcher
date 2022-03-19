<?php

namespace Letov\Flycatcher\Modules\Proxy;

use Exception;

abstract class AbstractProxyList
{
    /**
     * @param $apiKey
     * @param $proxyNeededCount
     * @throws Exception
     */
    abstract function __construct($apiKey, $proxyNeededCount);
    /**
     * @throws Exception
     */
    abstract function getProxyList(): array;
}