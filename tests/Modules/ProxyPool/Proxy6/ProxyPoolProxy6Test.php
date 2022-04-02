<?php

namespace Letov\Flycatcher\Tests\Modules\ProxyPool\Proxy6;

use DI\DependencyException;
use DI\NotFoundException;
use Letov\Flycatcher\Tests\TestCaseIncludeContainer;

class ProxyPoolProxy6Test extends TestCaseIncludeContainer
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
