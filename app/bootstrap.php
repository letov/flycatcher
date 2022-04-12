<?php

use DI\ContainerBuilder;
use Letov\Flycatcher\CodeGenerator\CodeGeneratorInterface;
use Letov\Flycatcher\CodeGenerator\CodeGeneratorManager;
use Letov\Flycatcher\Downloader\ArgsSupport\ArgsSupportCodeGenerator;

require __DIR__ . '/../vendor/autoload.php';

if (!in_array(CodeGeneratorInterface::class, get_declared_interfaces())) {
    CodeGeneratorManager::generateAll(array(
        new ArgsSupportCodeGenerator()
    ));
}

$builder = new ContainerBuilder();
$builder->addDefinitions(__DIR__ . '/config.php');
$container = $builder->build();

set_exception_handler(function($e) use ($container) {
    $lines = explode("\n", (string) $e);
    foreach ($lines as $line)
    {
        $container->get('Logger')->error($line);
    }
    die();
});

$container
    ->get('Cache')
    ->setAppDirs(
        $container->get('RootDir'),
        $container->get('Dirs')
    );

return $container;