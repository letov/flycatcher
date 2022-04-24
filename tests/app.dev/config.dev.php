<?php

use JonnyW\PhantomJs\Client;
use JonnyW\PhantomJs\DependencyInjection\ServiceContainer;
use Letov\Flycatcher\Cache\Cache;
use Letov\Flycatcher\Captcha\Anticaptcha\ImageToTextAnticaptcha;
use Letov\Flycatcher\Directories\Directories;
use Letov\Flycatcher\Downloader\ArgsSupport\ArgsSupport;
use Letov\Flycatcher\Downloader\ToolSupport\Packages\PhantomJSPackage;
use Letov\Flycatcher\Downloader\ToolSupport\Packages\SeleniumFirefox;
use Letov\Flycatcher\Downloader\ToolSupport\Shells\Curl;
use Letov\Flycatcher\Downloader\ToolSupport\Shells\PhantomJS;
use Letov\Flycatcher\Downloader\ToolSupport\Shells\Wget;
use Letov\Flycatcher\ProxyPool\Proxy6\ProxyPoolProxy6;
use Letov\Flycatcher\ProxyPool\Proxy6\ProxyProxy6;
use Letov\Flycatcher\Shell\Shell;
use Letov\Flycatcher\Spyder\JsonUrlTree;
use Letov\Flycatcher\Spyder\SpyderDepth;
use Letov\Flycatcher\Spyder\SpyderSitemap;
use Letov\Flycatcher\Spyder\SpyderUrlList;
use Letov\Flycatcher\Spyder\SpyderUrlTemplate;
use Letov\Flycatcher\Worker\WorkerDownloadTool;
use Monolog\Handler\StreamHandler;
use Monolog\Logger;
use Psr\Container\ContainerInterface;

return [
    'Test.urlImage' => 'https://c.s-microsoft.com/favicon.ico',
    'Directories.rootPath' => '/tmp/flycatcher_storage_test',
    'Directories.paths' => array(
        'browsersData' => DI\string('{Directories.rootPath}/browsers_data'),
        'download' => DI\string('{Directories.rootPath}/download'),
        'logs' => DI\string('{Directories.rootPath}/logs'),
    ),
    'Directories' => DI\create(Directories::class),
    'Logger' => DI\factory(function ($c) {
        $logger = new Logger('log');
        $pid = posix_getpid();
        $logger->pushHandler(new StreamHandler($c->get('Directories.paths')['logs'] . '/debug_' . $pid . '.log', Logger::DEBUG));
        $logger->pushHandler(new StreamHandler('php://stdout', Logger::DEBUG));
        return $logger;
    }),
    'Downloader.accept' => 'text/html,application/xhtml+xml,application/xml;q=0.9,image/avif,image/webp,*/*;q=0.8',
    'Downloader.acceptLanguage' => 'ru-RU,ru;q=0.8,en-US;q=0.5,en;q=0.3',
    'Downloader.acceptEncoding' => 'gzip, deflate',
    'Downloader.connection' => 'keep-alive',
    'Downloader.timeout' => 15,
    'Downloader.timeoutWithCaptcha' => 60,
    'Downloader.timeoutWithPageContent' => 60,
    'Proxy' => DI\create(ProxyProxy6::class),
    'Proxy6.apiKey' => "",
    'Proxy6.minCount' => 3,
    'Proxy6.throwIfLessMinCount' => false,
    'Proxy6.httpsCount' => 1,
    'ProxyPool' => DI\create(ProxyPoolProxy6::class)
        ->constructor(
            DI\get('Proxy6.apiKey'),
            DI\get('Proxy6.minCount'),
            DI\get('Proxy6.throwIfLessMinCount'),
            DI\get('Proxy6.httpsCount'),
        ),
    'Anticaptcha.apiKey' => '',
    'Captcha.imageToText' => DI\create(ImageToTextAnticaptcha::class)
        ->constructor(
            DI\get('Anticaptcha.apiKey'),
        ),
    'Gearman.worker' => DI\create(GearmanWorker::class),
    'Gearman.client' => DI\create(GearmanClient::class),
    'Gearman.host' => '127.0.0.1',
    'Gearman.port' => 4730,
    'Worker.downloadToolWorker' => DI\create(WorkerDownloadTool::class),
    'Worker.downloadToolWorker.count' => 3,
    'Cache.maxFileLifetimeSecond' => 60 * 60 * 24 * 5,
    'Cache.imageAlwaysFresh' => true,
    'Cache' => DI\create(Cache::class)
        ->constructor(
            DI\get('Cache.maxFileLifetimeSecond'),
            DI\get('Cache.imageAlwaysFresh'),
            DI\get('Stat')
        ),
    'DomParser' => DI\create(\Letov\Flycatcher\DomParser\PhpHtmlParser\DomDocument::class),
    'Shell' => DI\create(Shell::class),
    'Stat' => function ($c) {
        return (new Shell('stat', $c->get('Logger'), '='))
            ->addArg('--printf', '%s');
    },
    'ArgSupport' => DI\create(ArgsSupport::class),
    'Curl' => DI\create(Curl::class),
    'Curl.shell' => function (ContainerInterface $c) {
        return new Shell('curl', $c->get('Logger'));
    },
    'Wget' => DI\create(Wget::class),
    'Wget.shell' => function (ContainerInterface $c) {
        return new Shell('wget', $c->get('Logger'));
    },
    'PhantomJS' => DI\create(PhantomJS::class),
    'PhantomJS.shell' => function (ContainerInterface $c) {
        return new Shell($c->get('PhantomJS.path'), $c->get('Logger'), '=');
    },
    'PhantomJS.path' => '/usr/local/bin/phantomjs',
    'PhantomJS.connector.path' => 'PahntomJSConnectors/PhantomJSConnector.js',
    'PhantomJS.connector.pageContentWait' => 10,
    'Selenium.firefox' => DI\create(SeleniumFirefox::class),
    'JsonUrlTree' => DI\create(JsonUrlTree::class),
    'Spyder.depth' => DI\create(SpyderDepth::class),
    'Spyder.urlList' => DI\create(SpyderUrlList::class),
    'Spyder.urlTemplate' => DI\create(SpyderUrlTemplate::class),
    'Spyder.sitemap' => DI\create(SpyderSitemap::class),
];