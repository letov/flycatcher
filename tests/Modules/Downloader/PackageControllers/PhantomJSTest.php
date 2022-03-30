<?php

namespace Letov\Flycatcher\Tests\Modules\Downloader\PackageControllers;

use Letov\Flycatcher\Tests\TestCaseIncludeContainer;

class PhantomJSTest extends TestCaseIncludeContainer
{
    public function testPhantomJS()
    {
        $proxyList = $this->container->get("ProxyService")->getProxyList('socks5');
        $this->assertGreaterThan(0, count($proxyList));
        $phantomJS = $this->container->make('PhantomJS', array(
            'args' => array(
                'Proxy' => $proxyList[0],
                'TimeOut' => $this->container->get('Downloader.timeout'),
                'CookieFilePath' => $this->tmpCookie,
                'HttpMethod' => 'GET',
                'PayloadForm' => array(
                    'name1' => 'value&val1',
                    'name2' => 'value% val2'
                ),
                'Headers' => array(
                    'User-Agent' => 'someUserAgent',
                    'Referer' => 'https://someReferer.com',
                    'Accept' => $this->container->get('Downloader.accept'),
                    'Accept-Language' => $this->container->get('Downloader.acceptLanguage'),
                    'Accept-Encoding' => $this->container->get('Downloader.acceptEncoding'),
                    'Connection' => $this->container->get('Downloader.connection'),
                ),
                'PhantomJSClient' => $this->container->get('PhantomJS.client'),
                'PhantomJSPath' => $this->container->get('PhantomJS.path'),
            ),
        ));
        $phantomJS->downloadFile('https://google.ru/fakeUrl/fakeUrl', $this->tmpFile);
        $this->assertFileDoesNotExist($this->tmpFile);
        $phantomJS->downloadFile($this->container->get('Test.urlImage'), $this->tmpFile);
        $this->assertFileExists($this->tmpFile);
    }
}
