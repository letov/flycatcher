<?php

use Letov\Flycatcher\Modules\Cache\Cache;
use Letov\Flycatcher\Modules\ProxyPool\Proxy6\ProxyProxy6;
use Letov\Flycatcher\Modules\ProxyPool\Proxy6\ProxyPoolProxy6;
use Letov\Flycatcher\Modules\Shell\Shell;

return [
    'ProxyProxy6' => DI\create(ProxyProxy6::class),
    'ProxyPoolProxy6' => DI\create(ProxyPoolProxy6::class),
    'cache.maxFileLifetimeSecond' => 1,
    'cache.imageAlwaysFresh' => true,
    'Cache' => DI\create(Cache::class)
        ->constructor(
                DI\get('cache.maxFileLifetimeSecond'),
                DI\get('cache.imageAlwaysFresh'),
                DI\get('shell.stat'
            )
        ),
    'shell' => DI\create(Shell::class),
    'shell.stat' => DI\create(Shell::class)
        ->constructor('stat')
        ->method('updateArgDelimiter', '=')
        ->method('addArg', '--printf', '%s')
];