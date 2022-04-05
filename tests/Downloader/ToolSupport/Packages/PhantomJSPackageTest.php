<?php

namespace Letov\Flycatcher\Tests\Downloader\ToolSupport\Packages;

use DI\DependencyException;
use DI\NotFoundException;
use Letov\Flycatcher\Tests\TestCaseContainer;

class PhantomJSPackageTest extends TestCaseContainer
{
    /**
     * @throws DependencyException
     * @throws NotFoundException
     */
    public function testPhantomJS()
    {
        $phantomJS = $this->container->make('PhantomJSPackage', array(
            'argsSupport' => $this->container->make('ArgSupport', array(
                'args' => array(
                    'Timeout' => $this->container->get('Downloader.timeout'),
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
                    'PhantomJSClient' => $this->container->get('PhantomJSPackage.client'),
                    'PhantomJSPath' => $this->container->get('PhantomJS.path'),
                )
            ))
        ));
        $phantomJS->downloadFile('https://google.ru/fakeUrl/fakeUrl', $this->tmpFile);
        $this->assertFileDoesNotExist($this->tmpFile);
        $phantomJS->downloadFile($this->container->get('Test.urlImage'), $this->tmpFile);
        $this->assertFileExists($this->tmpFile);
    }
}
