<?php

namespace Letov\Flycatcher\Tests\Downloader\ToolSupport\Shells;

use DI\DependencyException;
use DI\NotFoundException;
use Letov\Flycatcher\Tests\TestCaseContainer;

class CurlTest extends TestCaseContainer
{
    /**
     * @throws NotFoundException
     * @throws DependencyException
     */
    public function testCurl()
    {
        $curl = $this->container->make('Curl', array(
            'argsSupport' => $this->container->make('ArgSupport', array(
                'args' =>  array(
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
                    'Shell' => $this->container->get("Curl.shell")
                )
            ))
        ));
        $curl->downloadFile('https://google.ru/fakeUrl/fakeUrl', $this->tmpFile);
        $this->assertFileDoesNotExist($this->tmpFile);
        $curl->downloadFile($this->container->get('Test.urlImage'), $this->tmpFile);
        $this->assertFileExists($this->tmpFile);
    }
}