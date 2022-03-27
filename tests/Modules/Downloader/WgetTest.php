<?php

namespace Letov\Flycatcher\Tests\Modules\Downloader;

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
        $tmpCookie = '/tmp/cookies';
        @unlink($tmpCookie);
        $proxyList = $this->container->get("ProxyService")->getProxyList('https');
        $this->assertGreaterThan(0, count($proxyList));
        $wgetDownloader = $this->container->make('Wget', array(
            'args' => array(
                'Proxy' => $proxyList[0],
                'TimeOut' => $this->container->get('Downloader.timeout'),
                'CookieFilePath' => $tmpCookie,
                'HttpMethod' => 'GET',
                'Payload' => implode("=", array(
                    'someData' => 'someValue',
                    'moreData' => 'moreValue'
                )),
                'Headers' => array(
                    'User-Agent' => 'someUserAgent',
                    'Referer' => 'https://someReferer.com',
                    'Accept' => $this->container->get('Header.accept'),
                    'Accept-Language' => $this->container->get('Header.acceptLanguage'),
                    'Accept-Encoding' => $this->container->get('Header.acceptEncoding'),
                    'Connection' => $this->container->get('Header.connection'),
                ),
            ),
            'shellCmd' => $this->container->get("ShellCmd.wget")
        ));
        $tmpFile = '/tmp/testDownload';
        @unlink($tmpFile);
        $wgetDownloader->downloadFile('https://google.ru/fakeUrl/fakeUrl', $tmpFile);
        $this->assertFileDoesNotExist($tmpFile);
        $wgetDownloader->downloadFile($this->container->get('Test.urlImage'), $tmpFile);
        $this->assertFileExists($tmpFile);
        @unlink($tmpFile);
        @unlink($tmpCookie);
    }
}
