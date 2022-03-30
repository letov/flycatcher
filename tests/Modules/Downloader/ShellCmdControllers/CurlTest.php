<?php

namespace Letov\Flycatcher\Tests\Modules\Downloader\ShellCmdControllers;

use DI\DependencyException;
use DI\NotFoundException;
use Letov\Flycatcher\Tests\TestCaseIncludeContainer;

class CurlTest extends TestCaseIncludeContainer
{
    /**
     * @throws NotFoundException
     * @throws DependencyException
     */
    public function testCurl()
    {
        $proxyList = $this->container->get("ProxyService")->getProxyList('socks5');
        $this->assertGreaterThan(0, count($proxyList));
        $curl = $this->container->make('Curl', array(
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
                'ShellCmd' => $this->container->get("ShellCmd.curl")
            ),
        ));
        $curl->downloadFile('https://google.ru/fakeUrl/fakeUrl', $this->tmpFile);
        $this->assertFileDoesNotExist($this->tmpFile);
        $curl->downloadFile($this->container->get('Test.urlImage'), $this->tmpFile);
        $this->assertFileExists($this->tmpFile);
    }
}
