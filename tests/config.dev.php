<?php

use Letov\Flycatcher\Modules\Cache\Cache;
use Letov\Flycatcher\Modules\Downloader\Classes\Curl;
use Letov\Flycatcher\Modules\Downloader\Classes\Wget;
use Letov\Flycatcher\Modules\Proxy\Proxy6Service\Proxy6;
use Letov\Flycatcher\Modules\Proxy\Proxy6Service\Proxy6Service;
use Letov\Flycatcher\Modules\ShellCmd\ShellCmd;

return array(
    'Test.urlImage' => 'https://static.pleer.ru/i/logo.png',
    'Proxy' => DI\create(Proxy6::class),
    'Proxy6Service.apiKey' => "fceed63ebe-0d6f24275b-dfcbe32351",
    'Proxy6Service.minCount' => 3,
    'Proxy6Service.throwIfLessMinCount' => false,
    'Proxy6Service.httpsCount' => 1,
    'ProxyService' => DI\create(Proxy6Service::class)
        ->constructor(
            DI\get('Proxy6Service.apiKey'),
            DI\get('Proxy6Service.minCount'),
            DI\get('Proxy6Service.throwIfLessMinCount'),
            DI\get('Proxy6Service.httpsCount'),
        ),
    'Cache.maxFileLifetimeSecond' => 1,
    'Cache.imageAlwaysFresh' => true,
    'Cache' => DI\create(Cache::class)
        ->constructor(
                DI\get('Cache.maxFileLifetimeSecond'),
                DI\get('Cache.imageAlwaysFresh'),
                DI\get('ShellCmd.stat')
        ),
    'Downloader.timeout' => 15,
    'Curl.path' => 'curl',
    'Header.accept' => 'text/html,application/xhtml+xml,application/xml;q=0.9,image/avif,image/webp,*/*;q=0.8',
    'Header.acceptLanguage' => 'ru-RU,ru;q=0.8,en-US;q=0.5,en;q=0.3',
    'Header.acceptEncoding' => 'gzip, deflate',
    'Header.connection' => 'keep-alive',
    'Wget.path' => 'wget',
    'ShellCmd' => DI\create(ShellCmd::class),
    'ShellCmd.stat' => DI\create(ShellCmd::class)
        ->constructor('stat', '=')
        ->method('addArg', '--printf', '%s'),
    'ShellCmd.curl' => DI\create(ShellCmd::class)
        ->constructor(DI\get('Curl.path')),
    'ShellCmd.wget' => DI\create(ShellCmd::class)
        ->constructor(DI\get('Wget.path')),
    'Curl' => DI\create(Curl::class),
    'Wget' => DI\create(Wget::class),
);