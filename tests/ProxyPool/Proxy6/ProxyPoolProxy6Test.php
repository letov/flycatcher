<?php

namespace Letov\Flycatcher\Tests\ProxyPool\Proxy6;

use DI\DependencyException;
use DI\NotFoundException;
use Letov\Flycatcher\Tests\TestCaseContainer;

class ProxyPoolProxy6Test extends TestCaseContainer
{
    /**
     * @throws DependencyException
     * @throws NotFoundException
     */
    function testProxyPool()
    {
        $proxyList = $this->container->get("ProxyPool")->getProxyList();
        $this->assertGreaterThan(0, count($proxyList));
    }
}
