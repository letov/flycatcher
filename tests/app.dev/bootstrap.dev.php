<?php

use DI\ContainerBuilder;
use Letov\Flycatcher\CodeGenerator\CodeGeneratorInterface;
use Letov\Flycatcher\CodeGenerator\CodeGeneratorManager;
use Letov\Flycatcher\Downloader\ArgsSupport\ArgsSupportCodeGenerator;

require __DIR__ . '/../../vendor/autoload.php';

if (!in_array(CodeGeneratorInterface::class, get_declared_interfaces()))
{
    CodeGeneratorManager::generateAll(array(
        new ArgsSupportCodeGenerator()
    ));
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