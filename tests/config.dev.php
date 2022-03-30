<?php

use JonnyW\PhantomJs\Client;
use Letov\Flycatcher\Modules\Cache\Cache;
use Letov\Flycatcher\Modules\Captcha\Anticaptcha\ImageToTextAnticaptcha;
use Letov\Flycatcher\Modules\Downloader\PackageControllers\PhantomJS;
use Letov\Flycatcher\Modules\Downloader\ShellCmdControllers\Curl;
use Letov\Flycatcher\Modules\Downloader\ShellCmdControllers\Wget;
use Letov\Flycatcher\Modules\Proxy\Proxy6\ProxyProxy6;
use Letov\Flycatcher\Modules\Proxy\Proxy6\ProxyServiceProxy6;
use Letov\Flycatcher\Modules\ShellCmd\ShellCmd;

return [
    'Test.urlImage' => 'https://static.pleer.ru/i/logo.png',
    'Dir.TempStorage' => '/tmp/flycatcher_storage/',
    'Dir.Tests' => DI\string('{Dir.TempStorage}tests/'),
    'Downloader.accept' => 'text/html,application/xhtml+xml,application/xml;q=0.9,image/avif,image/webp,*/*;q=0.8',
    'Downloader.acceptLanguage' => 'ru-RU,ru;q=0.8,en-US;q=0.5,en;q=0.3',
    'Downloader.acceptEncoding' => 'gzip, deflate',
    'Downloader.connection' => 'keep-alive',
    'Downloader.timeout' => 15,
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
            DI\get('ShellCmd.stat')
        ),
    'DomParser' => DI\create(\Letov\Flycatcher\Modules\DomParser\PhpHtmlParser\DomDocument::class),
    'ShellCmd' => DI\create(ShellCmd::class),
    'ShellCmd.stat' => function() {
        return (new ShellCmd('stat', '='))
            ->addArg('--printf', '%s');
    },
    'ShellCmd.curl' => function() {
        return new ShellCmd('curl');
    },
    'ShellCmd.wget' => function() {
        return new ShellCmd('wget');
    },
    'Curl' => DI\create(Curl::class),
    'Wget' => DI\create(Wget::class),
    'PhantomJS' => DI\create(PhantomJS::class),
    'PhantomJS.client' => DI\factory([Client::class, 'getInstance']),
    'PhantomJS.path' => '/usr/local/bin/phantomjs',
];