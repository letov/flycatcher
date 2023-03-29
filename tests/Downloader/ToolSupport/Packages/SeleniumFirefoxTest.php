<?php

declare(strict_types=1);

namespace Letov\Flycatcher\Tests\Downloader\ToolSupport\Packages;

use DI\DependencyException;
use DI\NotFoundException;
use Facebook\WebDriver\Firefox\FirefoxDriver;
use Letov\Flycatcher\Tests\TestCaseContainer;

/**
 * @internal
 *
 * @coversNothing
 */
final class SeleniumFirefoxTest extends TestCaseContainer
{
    /**
     * @throws NotFoundException
     * @throws DependencyException
     */
    public function testSeleniumFirefox(): void
    {
        $seleniumFirefox = $this->container->make('Selenium.firefox', [
            'argsSupport' => $this->container->make('ArgSupport', [
                'args' => [
                    'Timeout' => $this->container->get('Downloader.timeout'),
                    'OffHeadlessMode' => true,
                    'BeforeDownloadCall' => function (FirefoxDriver $driver): void {
                        // some actions
                    },
                ],
            ]),
            'logger' => $this->container->get('Logger'),
        ]);
        $seleniumFirefox->downloadFile('https://google.ru/', $this->tmpFile);
        static::assertFileExists($this->tmpFile);
        $seleniumFirefox->closeBrowser();
    }
}
