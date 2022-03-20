<?php

namespace Letov\Flycatcher\Tests\Modules\Proxy;

use Exception;
use Letov\Flycatcher\Modules\Proxy\Proxy;
use Letov\Flycatcher\Modules\Proxy\ProxyList;
use PHPUnit\Framework\TestCase;

class ProxyTest extends TestCase
{
    function testProxy()
    {

        $proxy = new Proxy('12.34.56.78', 9, 'fakeUser', 'fakePass');
        $this->assertSame("12.34.56.78:9",$proxy->getSocket());
        $this->assertSame("fakeUser:fakePass",$proxy->getAuth());
    }

    function testProxyList()
    {
        $this->expectException(Exception::class);
        new ProxyList("fakeKey", 3);
    }

}
