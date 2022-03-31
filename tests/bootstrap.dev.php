<?php

use DI\ContainerBuilder;
use Letov\Flycatcher\Modules\Downloader\ArgsSupport\ArgsSupportCodeGenerator;
use Letov\Flycatcher\Modules\Downloader\ArgsSupport\ArgInterfaces\CookieArgInterface;

require __DIR__ . '/../vendor/autoload.php';

if (!in_array(CookieArgInterface::class, get_declared_interfaces()))
{
    ArgsSupportCodeGenerator::generate();
}

$builder = new ContainerBuilder();
$builder->addDefinitions(__DIR__ . '/config.dev.php');
$container = $builder->build();

$container
    ->make('Shell', array('cmd' => 'rm'))
    ->addArg('-rf')
    ->addArg($container->get('Dir.TempStorage'))
    ->run();
$container
    ->make('Shell', array('cmd' => 'mkdir'))
    ->addArg($container->get('Dir.TempStorage'))
    ->run();
$container
    ->make('Shell', array('cmd' => 'mkdir'))
    ->addArg($container->get('Dir.Tests'))
    ->run();
return $container;