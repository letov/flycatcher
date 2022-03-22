<?php

namespace Letov\Flycatcher\Tests\Modules\Proxy;

use DI\Container;
use DI\DependencyException;
use DI\NotFoundException as NotFoundExceptionAlias;
use Exception;
use PHPUnit\Framework\TestCase;

class ProxyTest extends TestCase
{
    public Container $container;

    /**
     * @throws DependencyException
     * @throws NotFoundExceptionAlias
     */
    public function setUp(): void
    {
        $this->container = require __DIR__ . '/../../bootstrap.dev.php';
    }

    /**
     * @throws DependencyException
     * @throws NotFoundExceptionAlias
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
     * @throws NotFoundExceptionAlias
     */
    function testProxyService()
    {
        $realApiKey = "";
        $this->expectException(Exception::class);
        $proxyList = $this->container
            ->make('ProxyService',
                array(
                    "apiKey" => $realApiKey,
                    "proxyNeededCount" => 1
                ))
            ->getProxyList();
        $this->assertSame(1, count($proxyList));
        $this->assertSame(2, count(explode(":", $proxyList[0]->getSocket())));
    }
}
