<?php

namespace Letov\Flycatcher\Tests\Modules\Proxy\Proxy6;

use DI\DependencyException;
use DI\NotFoundException;
use Letov\Flycatcher\Tests\TestCaseIncludeContainer;

class ProxyProxy6Test extends TestCaseIncludeContainer
{
    /**
     * @throws DependencyException
     * @throws NotFoundException
     */
    function testProxyService()
    {
        $proxyList = $this->container->get("ProxyService")->getProxyList();
        $this->assertGreaterThan(0, count($proxyList));
    }
}
