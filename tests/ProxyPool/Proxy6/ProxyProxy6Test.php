<?php

namespace Letov\Flycatcher\Tests\ProxyPool\Proxy6;

use DI\DependencyException;
use DI\NotFoundException;
use Letov\Flycatcher\Tests\TestCaseContainer;

class ProxyProxy6Test extends TestCaseContainer
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
}
