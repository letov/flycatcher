<?php

use JonnyW\PhantomJs\Client;
use JonnyW\PhantomJs\DependencyInjection\ServiceContainer;
use Letov\Flycatcher\Cache\Cache;
use Letov\Flycatcher\Captcha\Anticaptcha\ImageToTextAnticaptcha;
use Letov\Flycatcher\Downloader\ArgsSupport\ArgsSupport;
use Letov\Flycatcher\Downloader\ToolSupport\Packages\PhantomJSPackage;
use Letov\Flycatcher\Downloader\ToolSupport\Shells\Curl;
use Letov\Flycatcher\Downloader\ToolSupport\Shells\PhantomJS;
use Letov\Flycatcher\Downloader\ToolSupport\Shells\Wget;
use Letov\Flycatcher\ProxyPool\Proxy6\ProxyPoolProxy6;
use Letov\Flycatcher\ProxyPool\Proxy6\ProxyProxy6;
use Letov\Flycatcher\Shell\Shell;
use Letov\Flycatcher\Worker\DownloadToolWorker;
use Psr\Container\ContainerInterface;

return [
    'Test.urlImage' => 'https://static.pleer.ru/i/logo.png',
    'Dir.TempStorage' => '/tmp/flycatcher_storage/',
    'Dir.Tests' => DI\string('{Dir.TempStorage}tests/'),
    'Downloader.accept' => 'text/html,applicPhantomJSPackageation/xhtml+xml,application/xml;q=0.9,image/avif,image/webp,*/*;q=0.8',
    'Downloader.acceptLanguage' => 'ru-RU,ru;q=0.8,en-US;q=0.5,en;q=0.3',
    'Downloader.acceptEncoding' => 'gzip, deflate',
    'Downloader.connection' => 'keep-alive',
    'Downloader.timeout' => 15,
    'Downloader.timeoutWithCaptcha' => 60,
    'Proxy' => DI\create(ProxyProxy6::class),
    'Proxy6.apiKey' => "fceed63ebe-0d6f24275b-dfcbe32351",
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
    'Anticaptcha.apiKey' => '63a2a4966f793615acebd66a7778f17f',
    'Captcha.imageToText' => DI\create(ImageToTextAnticaptcha::class)
        ->constructor(
            DI\get('Anticaptcha.apiKey'),
        ),
    'GearmanWorker' => DI\create(GearmanWorker::class),
    'GearmanClient' => DI\create(GearmanClient::class),
    'DownloadToolWorker' => DI\create(DownloadToolWorker::class),
    'Cache.maxFileLifetimeSecond' => 1,
    'Cache.imageAlwaysFresh' => true,
    'Cache' => DI\create(Cache::class)
        ->constructor(
            DI\get('Cache.maxFileLifetimeSecond'),
            DI\get('Cache.imageAlwaysFresh'),
            DI\get('Stat')
        ),
    'DomParser' => DI\create(\Letov\Flycatcher\DomParser\PhpHtmlParser\DomDocument::class),
    'Shell' => DI\create(Shell::class),
    'Stat' => function() {
        return (new Shell('stat', '='))
            ->addArg('--printf', '%s');
    },
    'ArgSupport' => DI\create(ArgsSupport::class),
    'Curl' => DI\create(Curl::class),
    'Curl.shell' => function() {
        return new Shell('curl');
    },
    'Wget' => DI\create(Wget::class),
    'Wget.shell' => function() {
        return new Shell('wget');
    },
    'PhantomJS' => DI\create(PhantomJS::class),
    'PhantomJS.shell' => function(ContainerInterface $c) {
        return new Shell($c->get('PhantomJS.path'), '=');
    },
    'PhantomJS.path' => '/usr/local/bin/phantomjs',
    'PhantomJS.connector.captchaImageToText' => 'PahntomJSConnectors/PhantomJSCaptchaText.js',
    'PhantomJSPackage' => DI\create(PhantomJSPackage::class),
    'PhantomJSPackage.serviceContainer' => DI\factory([serviceContainer::class, 'getInstance']),
    'PhantomJSPackage.client' => DI\factory([Client::class, 'getInstance']),
];