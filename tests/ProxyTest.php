<?php

use DI\Container;
use DI\DependencyException;
use DI\NotFoundException;
use Letov\Flycatcher\Modules\Proxy\ProxyList;
use PHPUnit\Framework\TestCase;

class ProxyTest extends TestCase
{
    private static Container $container;

    public static function setUpBeforeClass(): void
    {
        self::$container = new Container();
    }

    /**
     * @throws DependencyException
     * @throws NotFoundException
     */
    function testProxy()
    {
        $proxy = self::$container->make("Letov\Flycatcher\Modules\Proxy\Proxy", [
            'ip' => '12.34.56.78',
            'port' => 9,
            'user' => 'fakeUser',
            'pass' => 'fakePass'
        ]);
        $this->assertSame("12.34.56.78:9",$proxy->getSocket());
        $this->assertSame("fakeUser:fakePass",$proxy->getAuth());
    }

    function testProxyList()
    {
        $this->expectException(Exception::class);
        new ProxyList("fakeKey", 10);
    }

}
