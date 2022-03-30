<?php

use DI\ContainerBuilder;
use Letov\Flycatcher\Modules\Downloader\ArgsSupport\AbstractArgsSupportCodeGenerator;
use Letov\Flycatcher\Modules\Downloader\ArgsSupport\ArgInterfaces\CookieArgInterface;

require __DIR__ . '/../vendor/autoload.php';

if (!in_array(CookieArgInterface::class, get_declared_interfaces()))
{
    AbstractArgsSupportCodeGenerator::generate();
}

$builder = new ContainerBuilder();
$builder->addDefinitions(__DIR__ . '/config.dev.php');
$container = $builder->build();

$container
    ->make('ShellCmd', array('cmd' => 'rm'))
    ->addArg('-rf')
    ->addArg($container->get('Dir.TempStorage'))
    ->run();
$container
    ->make('ShellCmd', array('cmd' => 'mkdir'))
    ->addArg($container->get('Dir.TempStorage'))
    ->run();
$container
    ->make('ShellCmd', array('cmd' => 'mkdir'))
    ->addArg($container->get('Dir.Tests'))
    ->run();
return $container;