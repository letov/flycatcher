<?php

namespace Proxy;

use Exception;
use Letov\Flycatcher\Modules\Proxy\Proxy;
use Letov\Flycatcher\Modules\Proxy\Proxy6List;
use PHPUnit\Framework\TestCase;

class ProxyTest extends TestCase
{
    function testProxy() {
        $proxy = new Proxy("12.34.56.78","9","user","pass");
        $this->assertSame("12.34.56.78:9",$proxy->getSocket());
        $this->assertSame("user:pass",$proxy->getAuth());
    }

    function testProxy6List() {
        $this->expectException(Exception::class);
        $proxyList = new Proxy6List("fakeKey", 10);
        $proxyList->getProxyList();
    }

}
