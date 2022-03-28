<?php

use Letov\Flycatcher\Modules\Cache\Cache;
use Letov\Flycatcher\Modules\Proxy\Proxy6\ProxyProxy6;
use Letov\Flycatcher\Modules\Proxy\Proxy6\ProxyServiceProxy6;
use Letov\Flycatcher\Modules\ShellCmd\ShellCmd;

return [
    'ProxyProxy6' => DI\create(ProxyProxy6::class),
    'ProxyServiceProxy6' => DI\create(ProxyServiceProxy6::class),
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
        ->constructor('stat')
        ->method('updateArgDelimiter', '=')
        ->method('addArg', '--printf', '%s')
];