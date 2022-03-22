<?php

use Letov\Flycatcher\Modules\Cache\Cache;
use Letov\Flycatcher\Modules\Proxy\Proxy6;
use Letov\Flycatcher\Modules\Proxy\Proxy6Service;
use Letov\Flycatcher\Modules\ShellCmd\ShellCmd;

return [
    'Proxy' => DI\create(Proxy6::class),
    'ProxyService' => DI\create(Proxy6Service::class),
    'cache.maxFileLifetimeSecond' => 1,
    'cache.imageAlwaysFresh' => true,
    'Cache' => DI\create(Cache::class)
        ->constructor(
                DI\get('cache.maxFileLifetimeSecond'),
                DI\get('cache.imageAlwaysFresh'),
                DI\get('shellCmd.stat'
            )
        ),
    'shellCmd' => DI\create(ShellCmd::class),
    'shellCmd.stat' => DI\create(ShellCmd::class)
        ->constructor('stat', '=')
        ->method('addArg', '--printf', '%s')
];