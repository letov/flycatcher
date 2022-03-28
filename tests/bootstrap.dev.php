<?php

use DI\ContainerBuilder;
use Letov\Flycatcher\Modules\Downloader\ArgsSupport\ArgInterfaces\CookieArgInterface;
use Letov\Flycatcher\Modules\Downloader\ShellCmdSupport\CodeGeneratorShellCmdSupport;

require __DIR__ . '/../vendor/autoload.php';

if (!in_array(CookieArgInterface::class, get_declared_interfaces()))
{
    CodeGeneratorShellCmdSupport::generate();
}

$builder = new ContainerBuilder();
$builder->addDefinitions(__DIR__ . '/config.dev.php');
$container = $builder->build();

return $container;