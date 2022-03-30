<?php

use Letov\Flycatcher\Modules\Cache\Cache;
use Letov\Flycatcher\Modules\Captcha\Anticaptcha\ImageToTextAnticaptcha;
use Letov\Flycatcher\Modules\Downloader\Controllers\Curl;
use Letov\Flycatcher\Modules\Downloader\Controllers\PhantomJS;
use Letov\Flycatcher\Modules\Downloader\Controllers\Wget;
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
    'ShellCmd.stat' => DI\create(ShellCmd::class)
        ->constructor('stat', '=')
        ->method('addArg', '--printf', '%s'),
    'ShellCmd.curl' => DI\create(ShellCmd::class)
        ->constructor(DI\get('Curl.path')),
    'ShellCmd.wget' => DI\create(ShellCmd::class)
        ->constructor(DI\get('Wget.path')),
    'ShellCmd.phantomjs' => DI\create(ShellCmd::class)
        ->constructor(DI\get('PhantomJS.path'), '=')
        ->method('addArg', '--web-security', 'no')
        ->method('addArg', '--ignore-ssl-errors', 'true')
        ->method('addArg', '--ssl-protocol', 'any'),
    'Curl.path' => 'curl',
    'Curl' => DI\create(Curl::class),
    'Wget.path' => 'wget',
    'Wget' => DI\create(Wget::class),
    'PhantomJS.path' => 'phantomjs',
    'PhantomJS.connector' => 'PhantomJSIncludeCaptchaSolve.js',
    'PhantomJS.maxExecTime' => 60,
    'PhantomJS' => DI\create(PhantomJS::class),
];