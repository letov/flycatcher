<?php

use DI\ContainerBuilder;
use Letov\Flycatcher\Modules\Downloader\ArgsSupport\ArgsSupportShellCmdCodegen;

require __DIR__ . '/../vendor/autoload.php';

ArgsSupportShellCmdCodegen::generate();

$builder = new ContainerBuilder();
$builder->addDefinitions(__DIR__ . '/config.dev.php');
$container = $builder->build();

return $container;