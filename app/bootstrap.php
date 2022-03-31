<?php

use DI\ContainerBuilder;

require __DIR__ . '/../vendor/autoload.php';

ArgsSupportshellCodegen::generate();

$builder = new ContainerBuilder();
$builder->addDefinitions(__DIR__ . '/config.php');
/*$builder->enableCompilation(__DIR__ . '/tmp');
$builder->writeProxiesToFile(true, __DIR__ . '/tmp/proxies');*/
$container = $builder->build();

return $container;