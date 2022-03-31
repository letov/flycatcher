<?php

use JonnyW\PhantomJs\Client;
use JonnyW\PhantomJs\DependencyInjection\ServiceContainer;
use Letov\Flycatcher\Modules\Cache\Cache;
use Letov\Flycatcher\Modules\Captcha\Anticaptcha\ImageToTextAnticaptcha;
use Letov\Flycatcher\Modules\Downloader\ArgsSupport\ArgsSupport;
use Letov\Flycatcher\Modules\Downloader\Packages\PhantomJSPackage;
use Letov\Flycatcher\Modules\Downloader\Shells\Curl;
use Letov\Flycatcher\Modules\Downloader\Shells\PhantomJS;
use Letov\Flycatcher\Modules\Downloader\Shells\Wget;
use Letov\Flycatcher\Modules\Proxy\Proxy6\ProxyProxy6;
use Letov\Flycatcher\Modules\Proxy\Proxy6\ProxyServiceProxy6;
use Letov\Flycatcher\Modules\Shell\Shell;
use Psr\Container\ContainerInterface;

return [
    'Test.urlImage' => 'https://static.pleer.ru/i/logo.png',
    'Dir.TempStorage' => '/tmp/flycatcher_storage/',
    'Dir.Tests' => DI\string('{Dir.TempStorage}tests/'),
    'Downloader.accept' => 'text/html,application/xhtml+xml,application/xml;q=0.9,image/avif,image/webp,*/*;q=0.8',
    'Downloader.acceptLanguage' => 'ru-RU,ru;q=0.8,en-US;q=0.5,en;q=0.3',
    'Downloader.acceptEncoding' => 'gzip, deflate',
    'Downloader.connection' => 'keep-alive',
    'Downloader.timeout' => 15,
    'Downloader.timeoutWithCaptcha' => 30,
    'Proxy' => DI\create(ProxyProxy6::class),
    'Proxy6.apiKey' => "fceed63ebe-0d6f24275b-dfcbe32351",
    'Proxy6.minCount' => 3,
    'Proxy6.throwIfLessMinCount' => false,
    'Proxy6.httpsCount' => 1,
    'ProxyService' => DI\create(ProxyServiceProxy6::class)
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
    'Cache.maxFileLifetimeSecond' => 1,
    'Cache.imageAlwaysFresh' => true,
    'Cache' => DI\create(Cache::class)
        ->constructor(
            DI\get('Cache.maxFileLifetimeSecond'),
            DI\get('Cache.imageAlwaysFresh'),
            DI\get('Shell.stat')
        ),
    'DomParser' => DI\create(\Letov\Flycatcher\Modules\DomParser\PhpHtmlParser\DomDocument::class),
    'Shell' => DI\create(Shell::class),
    'Shell.stat' => function() {
        return (new Shell('stat', '='))
            ->addArg('--printf', '%s');
    },
    'Shell.curl' => function() {
        return new Shell('curl');
    },
    'Shell.wget' => function() {
        return new Shell('wget');
    },
    'Shell.phantomJS' => function(ContainerInterface $c) {
        return new Shell($c->get('PhantomJS.path'), '=');
    },
    'ArgSupport' => DI\create(ArgsSupport::class),
    'Curl' => DI\create(Curl::class),
    'Wget' => DI\create(Wget::class),
    'PhantomJS' => DI\create(PhantomJS::class),
    'PhantomJS.path' => '/usr/local/bin/phantomjs',
    'PhantomJS.connector.captchaImageToText' => 'PhantomJSCaptchaText.js',
    'PhantomJSPackage' => DI\create(PhantomJSPackage::class),
    'PhantomJSPackage.serviceContainer' => DI\factory([serviceContainer::class, 'getInstance']),
    'PhantomJSPackage.client' => DI\factory([Client::class, 'getInstance']),
];