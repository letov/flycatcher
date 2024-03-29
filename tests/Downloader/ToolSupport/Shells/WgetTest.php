<?php

declare(strict_types=1);

namespace Letov\Flycatcher\Tests\Downloader\ToolSupport\Shells;

use DI\DependencyException;
use DI\NotFoundException;
use Letov\Flycatcher\Tests\TestCaseContainer;

/**
 * @internal
 *
 * @coversNothing
 */
final class WgetTest extends TestCaseContainer
{
    /**
     * @throws NotFoundException
     * @throws DependencyException
     */
    public function testWgetDownloader(): void
    {
        $wget = $this->container->make('Wget', [
            'argsSupport' => $this->container->make('ArgSupport', [
                'args' => [
                    'Timeout' => $this->container->get('Downloader.timeout'),
                    'CookieFilePath' => $this->tmpCookie,
                    'HttpMethod' => 'GET',
                    /*'PayloadDataArray' => array(
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
                    ),*/
                    'Shell' => $this->container->get('Wget.shell'),
                ],
                'logger' => $this->container->get('Logger'),
            ]),
        ]);
        $wget->downloadFile('https://google.com', $this->tmpFile);
        static::assertFileExists($this->tmpFile);
    }
}
