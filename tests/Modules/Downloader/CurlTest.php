<?php

namespace Letov\Flycatcher\Tests\Modules\Downloader;

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
        $tmpCookie = '/tmp/cookies';
        @unlink($tmpCookie);
        $proxyList = $this->container->get("ProxyService")->getProxyList('socks5');
        $this->assertGreaterThan(0, count($proxyList));
        $curlDownloader = $this->container->make('Curl', array(
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
            'shellCmd' => $this->container->get("ShellCmd.curl")
        ));
        $tmpFile = '/tmp/testDownload';
        @unlink($tmpFile);
        $curlDownloader->downloadFile('https://google.ru/fakeUrl/fakeUrl', $tmpFile);
        $this->assertFileDoesNotExist($tmpFile);
        $curlDownloader->downloadFile($this->container->get('Test.urlImage'), $tmpFile);
        $this->assertFileExists($tmpFile);
        @unlink($tmpFile);
        @unlink($tmpCookie);
    }
}
