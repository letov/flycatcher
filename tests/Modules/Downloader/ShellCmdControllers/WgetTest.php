<?php

namespace Letov\Flycatcher\Tests\Modules\Downloader\Controllers;

use DI\DependencyException;
use DI\NotFoundException;
use Letov\Flycatcher\Tests\TestCaseIncludeContainer;

class WgetTest extends TestCaseIncludeContainer
{
    /**
     * @throws NotFoundException
     * @throws DependencyException
     */
    public function testWgetDownloader()
    {
        $proxyList = $this->container->get("ProxyService")->getProxyList('https');
        $this->assertGreaterThan(0, count($proxyList));
        $wget = $this->container->make('Wget', array(
            'args' => array(
                'ProxyProxy6' => $proxyList[0],
                'TimeOut' => $this->container->get('Downloader.timeout'),
                'CookieFilePath' => $this->tmpCookie,
                'HttpMethod' => 'GET',
                'Payload' => implode("=", array(
                    'someData' => 'someValue',
                    'moreData' => 'moreValue'
                )),
                'Headers' => array(
                    'User-Agent' => 'someUserAgent',
                    'Referer' => 'https://someReferer.com',
                    'Accept' => $this->container->get('Downloader.accept'),
                    'Accept-Language' => $this->container->get('Downloader.acceptLanguage'),
                    'Accept-Encoding' => $this->container->get('Downloader.acceptEncoding'),
                    'Connection' => $this->container->get('Downloader.connection'),
                ),
            ),
            'shellCmd' => $this->container->get("ShellCmd.wget")
        ));
        $wget->downloadFile('https://google.ru/fakeUrl/fakeUrl', $this->tmpFile);
        $this->assertFileDoesNotExist($this->tmpFile);
        $wget->downloadFile($this->container->get('Test.urlImage'), $this->tmpFile);
        $this->assertFileExists($this->tmpFile);
    }
}
