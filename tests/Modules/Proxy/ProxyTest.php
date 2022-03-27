<?php

namespace Letov\Flycatcher\Tests\Modules\Proxy;

use DI\DependencyException;
use DI\NotFoundException;
use Letov\Flycatcher\Tests\TestCaseIncludeContainer;

class ProxyTest extends TestCaseIncludeContainer
{
    /**
     * @throws DependencyException
     * @throws NotFoundException
     */
    function testProxy()
    {
        $proxy = $this->container->make('Proxy', array("proxy" =>
            (object)array(
                "ip" => "12.34.56.78",
                "port" => "9",
                "user" => "fakeUser",
                "pass" => "fakePass",
            )
        ));
        $this->assertSame("12.34.56.78:9",$proxy->getSocket());
        $this->assertSame("fakeUser:fakePass",$proxy->getAuth());
    }

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
