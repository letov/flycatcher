<?php

namespace Letov\Flycatcher\Tests\Downloader\ToolSupport\Packages;

use DI\DependencyException;
use DI\NotFoundException;
use Facebook\WebDriver\Firefox\FirefoxDriver;
use Facebook\WebDriver\WebDriverBy;
use Letov\Flycatcher\Tests\TestCaseContainer;

class SeleniumFirefoxTest extends TestCaseContainer
{
    /**
     * @throws NotFoundException
     * @throws DependencyException
     */
    public function testSeleniumFirefox()
    {
        $seleniumFirefox = $this->container->make('Selenium.firefox', array(
            'argsSupport' => $this->container->make('ArgSupport', array(
                'args' => [
                    'Timeout' => $this->container->get('Downloader.timeout'),
                    'OffHeadlessMode' => true,
                    'BeforeDownloadCall' => function(FirefoxDriver $driver)
                    {
                        // some actions
                    }
                ]
            )),
            'logger' => $this->container->get('Logger')
        ));
        $seleniumFirefox->downloadFile('https://google.ru/', $this->tmpFile);
        $this->assertFileExists($this->tmpFile);
        $seleniumFirefox->closeBrowser();
    }
}
